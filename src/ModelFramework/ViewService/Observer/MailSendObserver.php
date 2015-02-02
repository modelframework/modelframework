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

        $form->getFieldsets()[ 'fields' ]->add( array(
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'from',
            'options'    => array(
                'label'         => 'From',
                'value_options' => $mailSendSettingsOptions,
            ),
            'attributes' => array(
                'value' => $defaultOption //set selected to '1'
            )
        ) );

        //TO FIELD INITIALIZATION

        $query  =
            $this->getSubject()->getQueryServiceVerify()->get( 'Email.lookup' )
                 ->process();
        $_where = $query->getWhere();
        $res    = $this->getSubject()->getGatewayServiceVerify()->get( 'Email' )
                       ->find( $_where );

        $toOptions = [ ];

        foreach ($res as $option) {
            $toOptions[ $option->email ] =
                $option->title . ' <' . $option->email . '>';
        }
        $defaultOption = $this->getSubject()->getParam( 'to', 0 );
//        if (!isset( $toOptions[ $defaultOption ] )) {
//            $toOptions[ $defaultOption ] = $defaultOption ?: 'Please select...';
//        }
        ksort( $toOptions );

        $form->getFieldsets()[ 'fields' ]->add( array(
            'type'       => 'Zend\Form\Element\Select',
            'name'       => 'to',
            'options'    => array(
                'label'         => 'To',
                'value_options' => $toOptions,
            ),
            'attributes' => array(
                'id' => 'email',
                //                'value' => $defaultOption
            )
        ) );

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
                        'name' => 'EmailAddress',
                        //                        'options' => [
                        //                            'encoding' => 'UTF-8',
                        //                            'min'      => 1,
                        //                        ],
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
                    exit;
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
        $mail               = $model->getDataModel();
        $send_setting       =
            $this->getSubject()->getGatewayService()->get( 'MailSendSetting' )
                 ->find( [ '_id' => $data[ 'from' ] ] )->current();
        $header             = [
            'from'         => $send_setting->email,
            'to'           => [ $data[ 'to' ] ],
            'message-id'   => 'send',
            'content-type' => 'text/html',
            'subject'      => $data[ 'title' ],
        ];
        $mail->protocol_ids = [ $data[ 'from' ] ];
        $mail->text         = $data[ 'text' ];
        $mail->title        = $data[ 'title' ];
        $mail->header       = $header;
        $mail->date         = date( 'Y-m-d H:i:s' );
        $mail->status_id    = Status::SENDING;
        $mail->from_id      = $send_setting->user_id;
        $model->setDataModel( $mail );
    }
}
