<?php
/**
 * Class FormObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use Wepo\Model\Status;
use Zend\InputFilter\Factory;
use Zend\InputFilter\InputFilter;

class MailSendObserver extends FormObserver
{

    public function process( $model )
    {
        $form = $this->initCustomForm();
        $this->processForm( $form, $this->getModel() );
    }

    public function initCustomForm()
    {
        $form = $this->initForm();

        $form->getFieldsets()[ 'button' ]->getElements()[ 'submit' ]->setValue( 'Send' );

        //updating default form

        $re = $this->getSubject()->getParam( 're' );

        $actionParams =
            array_merge( $form->getActionParams(), [ 're' => $re ] );
        $form->setActionParams( $actionParams );

        //FROM FIELD INITIALIZATION

        $query  = $this->getSubject()->getQueryServiceVerify()
                       ->get( 'MailSendSetting.lookup' )->process();
        $_where = $query->getWhere();
        $res    = $this->getSubject()->getGatewayServiceVerify()
                       ->get( 'MailSendSetting' )->find( $_where );

        $mailSendSettingsOptions = [ ];
        $defaultOption           = null;
        foreach ($res as $option) {
            $mailSendSettingsOptions[ $option->_id ] = $option->title;
            if ($option->is_default) {
                $defaultOption = $option->_id;
            }
        }
        if (!isset( $defaultOption )) {
            $mailSendSettingsOptions[ '0' ] = 'Please select ...';
        }
        ksort( $mailSendSettingsOptions );

        $form->getFieldsets()[ 'fields' ]->add( [
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'from',
            'options'    => [
                'label'         => 'From',
                'value_options' => $mailSendSettingsOptions,
            ],
            'attributes' => [
                'value' => $defaultOption, //set selected to '1'
                'class' => 'static-select2'
            ]
        ] );

        //TO FIELD INITIALIZATION

        $defaultOption = $this->getSubject()->getParam( 'to', 0 );

        $toOptions = [ ];

        if (!empty( $defaultOption )) {
            $toOptions[ $defaultOption ] = urldecode( $defaultOption );
        }

        $defaultRecipient_id = $this->getSubject()->getParam('recipient',0 );
        if ($defaultRecipient_id){
            $defaultRecipient = $this->getSubject()
                ->getGatewayServiceVerify()
                ->get('Email')
                ->findOne(['model_id'=>$defaultRecipient_id]);
            if($defaultRecipient){
                $defaultRecipient = $defaultRecipient->email;
                $toOptions[ $defaultRecipient ] = urldecode($defaultRecipient );
            }

        }

        $form->getFieldsets()[ 'fields' ]->add( [
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'to',
            'options'    => [
                'label'         => 'To',
                'value_options' => $toOptions,
            ],
            'attributes' => [
                'id'         => 'email',
                'value'      => $toOptions,
                'class'      => 'email-select2',
                'data-scope' => 'Email',
                'multiple'   => 'multiple'
            ]
        ] );

        //INPUT FILTER SETTINGS

        $form->addValidationField( 'fields', 'from' );
        $form->addValidationField( 'fields', 'to' );

        $factory           = new Factory();
        $fieldsInputFilter = $form->getInputFilter()->get( 'fields' );

        $fieldsInputFilter->add(
            $factory->createInput( [
                'name'       => 'from',
                'required'   => true,
                'filters'    => [
                    [ 'name' => 'StripTags' ],
                    [ 'name' => 'StringTrim' ],
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ],
                ],
            ] )
        );
        $fieldsInputFilter->add(
            $factory->createInput( [
                'name'       => 'to',
                'required'   => true,
                'filters'    => [
                    [ 'name' => 'StripTags' ],
                    [ 'name' => 'StringTrim' ],
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                    ],
                ],
            ] )
        );

        $form->addInputFilter( $fieldsInputFilter, 'fields' );

        //end updating default form
        return $form;
    }

    /**
     * @param $form
     * @param $model
     */
    public function processForm( $form, $model )
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $results    = [ ];
        $old_data   = $model->split( $form->getValidationGroup() );
        //Это жесть конечно и забавно, но на время сойдет :)
        $model_bind = $model->toArray();
        $fieldsAcl  = $model->getAclConfig()->fields;
        foreach ($model_bind as $_k => $_v) {
            if (substr( $_k, -4 ) == '_dtm' && $fieldsAcl[ $_k ] == 'write') {
                $model->$_k = str_replace( ' ', 'T', $_v );
            }
        }
        $this->replyTo( $model, $form );
        //Конец жести
        $request = $subject->getParams()->getController()->getRequest();
        if ($request->isPost()) {
            $form->setData( $request->getPost() );
            if ($form->isValid()) {
                $model_data = [ ];
                foreach ($form->getData() as $_k => $_data) {
                    $model_data += is_array( $_data ) ? $_data :
                        [ $_k => $_data ];
                }
                $this->configureMail( $model, $model_data, $old_data );
                try {
                    $subject->getLogicServiceVerify()
                            ->get( 'presave', $model->getModelName() )
                            ->trigger( $model->getDataModel() );
                    $subject->getGateway()->save( $model->getDataModel() );
                    $subject->getLogicServiceVerify()
                            ->get( 'mailsend', 'User' )
                            ->trigger( $this->getSubject()
                                            ->getAclServiceVerify()
                                            ->getAclServiceVerify()
                                            ->getUser() );
                } catch ( \Exception $ex ) {
                    $results[ 'message' ]
                        = 'Send mail problems happened: ' . $ex->getMessage();
                }
                if (!isset( $results[ 'message' ] )
                    || !strlen( $results[ 'message' ] )
                ) {
                    //$subject->getLogicServiceVerify()->get( 'post'
                    //. $viewConfig->mode,
                    //$model->getModelName() )
                    //->trigger( $model->getDataModel() );
                    $url = $subject->getBackUrl();
                    if ($url == null || $url == '/') {
                        $url = $subject->getParams()->getController()->url()
                                       ->fromRoute( $form->getRoute(),
                                           $form->getActionParams() );
                    }
                    $subject->setRedirect( $subject->refresh( $model->getModelName()
                                                              .
                                                              ' data was successfully saved',
                        $url ) );

                    return;
                }
            }
        } else {
            $form->bind( $model );
        }
        $form->prepare();
        $results[ 'form' ] = $form;
        $subject->setData( $results );
    }

    public function configureMail( $model, $data, $old_data )
    {

        /* Get Lead or patient or... information from model */
        $defaultRecipient_id = $this->getSubject()->getParam('recipient',0 );

        if ($defaultRecipient_id){
            $dataModel = $this->getSubject()
                ->getGatewayServiceVerify()
                ->get('Email')
                ->findOne(['model_id'=>$defaultRecipient_id])->data;

            $params[$dataModel] =
            $params['Contacts']
                = $this->getSubject()
                ->getGatewayServiceVerify()
                ->get($dataModel)
                ->findOne(['_id'=>$defaultRecipient_id])->toArray();
        }

        $params['User']=$this->getSubject()->getUser()->toArray();


        /* Parse title and text as twig template */
        if (isset($params)){

            $data[ 'title' ] = $this ->getSubject()
                ->getTwigServiceVerify()
                ->getParseString($data[ 'title' ],$params);

            $data[ 'text' ] = $this ->getSubject()
                ->getTwigServiceVerify()
                ->getParseString($data[ 'text' ],$params);
        }

        $mail               = $model->getDataModel();

        $send_setting       =
            $this->getSubject()->getGatewayService()->get( 'MailSendSetting' )
                 ->find( [ '_id' => $data[ 'from' ] ] )->current();
        $header             = [
            'from'         => $send_setting->email,
            'to'           => $data[ 'to' ],
            'message-id'   => 'send',
            'content-type' => 'text/html',
            'subject'      => $data[ 'title' ],
        ];
        $mail->protocol_ids = [ $data[ 'from' ] ];
        $mail->text         = $data[ 'text' ];
        $mail->title        = $data[ 'title' ];
        if (!empty( $mail->header )) {
            $header = array_merge( $mail->header, $header );
        }
        $mail->header    = $header;
        $mail->date      = date( 'Y-m-d H:i:s' );
        $mail->status_id = Status::SENDING;
        $mail->from_id   = $send_setting->user_id;

        $model->setDataModel( $mail );
    }

    public function replyTo( $model, $form )
    {
        $dataModel         = $model->getDataModel();
        $replyMessageQuery = $this->getSubject()->getQueryServiceVerify()
                                  ->get( 'MailDetail.reply' )->process();
        $chainQuery        = $this->getSubject()->getQueryServiceVerify()
                                  ->get( 'Mail.reply' )->process();

        $replyMessage =
            $this->getSubject()->getGatewayService()->get( 'MailDetail' )
                 ->find( $replyMessageQuery->getWhere() )->current();
        if (isset( $replyMessage )) {
            $chainWhere          = $chainQuery->getWhere();
            $chainWhere[ '_id' ] = $replyMessage->chain_id;
            $chain               =
                $this->getSubject()->getGatewayService()->get( 'Mail' )
                     ->find( $chainWhere )->current();
            $dataModel->title    = 'RE: ' . $chain->title;
            $references          =
                isset( $replyMessage->header[ 'references' ] ) ?
                    $replyMessage->header[ 'references' ] : [ ];
            $references[ ]       = $replyMessage->header[ 'message-id' ];
            $header              = [
                'in-reply-to' => $replyMessage->header[ 'message-id' ],
                'references'  => $references,

            ];
            $dataModel->header   = $header;
            if (!count( $form->getFieldsets()[ 'fields' ]->getElements()[ 'to' ]->getValueOptions() )) {
                $temp = $replyMessage->type == 'inbox' ?
                    $replyMessage->header[ 'from' ] :
                    $replyMessage->header[ 'to' ];
                $form->getFieldsets()[ 'fields' ]->getElements()[ 'to' ]->setValue( $temp );
                $options = [ ];
                foreach ($temp as $address) {
                    $options[ $address ] = $address;
                }
                $options[ 'test' ] = 'test';
                $form->getFieldsets()[ 'fields' ]->getElements()[ 'to' ]->setValueOptions( $options );
            }
        }
    }

    /**
     * Find Model Name by email id
     * @param string $defaultRecipient_id
     * @return string Model Name
     */
    private function getRecipientModelName($defaultRecipient_id=null){

        return $dataModel = $this->getSubject()
            ->getGatewayServiceVerify()
            ->get('Email')
            ->findOne(['model_id'=>$defaultRecipient_id])->data;
    }
}
