<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use Wepo\Lib\Acl;

class ConvertObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        prn('Convert Observer');
        $convertConfig = $subject -> getDataMappingServiceVerify()-> get('Lead');
        prn($convertConfig);
        exit();
        $viewConfig = $subject->getViewConfigDataVerify();

        $modelName  = $viewConfig->model;
        $route      = strtolower($viewConfig->model);

        $id         = (string) $subject->getParams()->fromRoute( 'id', 0 );
        if ( !$id )
        {
            return $this->redirect()->toRoute( $route );
        }
        $lead = $this->table( $transport )->get( $id );

        $model = $subject->getGateway()->get( $id );
        $mode  = Acl::MODE_EDIT;

        prn('ConvertObserver', $modelName);
        exit;
        $form = $subject->getFormServiceVerify()->get( $model, $mode );
        $form       = $this->form( 'ContactForm' );
        $contact    = $this->model( 'Contact' );
        $transport  = 'Lead';
        $transport2 = 'Contact';
        $transport3 = 'Client';


        if ( $lead->status_id == Status::CONVERTED )
        {
            return $this->refresh( 'That lead already has been converted', $this->url()->fromRoute( $route ) );
        }
        $contact->exchangeArray( $lead->toArray() );
        $form->setAttribute( 'action', $this->url()->fromRoute( $route, array( 'action' => 'convert', 'id' => $id ) ) );

        $clientHash =
            $this->truetableHash( 'Client', '_id', 'name', [ 'status_id' => [ Status::NEW_, Status::NORMAL ] ], true,
                                  [ 'rrrrrrrrrrrrrrrrrrrrrrrr' => 'New account' ] );

        $form->getFieldsets()[ 'fields' ]->get( 'client_id' )->setOptions(
            array(
                'label'         => 'Client',
                'value_options' => $clientHash,
            )
        );
        $form->bind( $contact );
        $request = $this->getRequest();
        if ( $request->isPost() )
        {
            $form->setInputFilter( $contact->getInputFilter() );
            $form->setData( $request->getPost() );
            $data = $request->getPost( 'fields' );
            if ( $form->isValid() )
            {
                $contact->changer_id  = $this->user()->id();
                $contact->avatar      = $lead->avatar;
                $contact->changed_dtm = date( 'Y-m-d H:i:s' );
                $contact->created_dtm = date( 'Y-m-d H:i:s' );
                if ( $permission == Auth::OWN )
                {
                    $contact->owner_id = $this->user()->id();
//                  $contact -> client_id = null;  ???????????????FOR WHAT???????WHY???
                }
                if ( $data[ 'client_id' ] == 'rrrrrrrrrrrrrrrrrrrrrrrr' )
                {
                    $client = $this->model( 'Client' );
                    $client->exchangeArray( $contact->toArray() );
                    $client->name      = $data[ 'account' ];
                    $client->status_id = Status::NEW_;
                    try
                    {
                        $this->trigger( 'presave', $client );
                        $this->table( $transport3 )->save( $client );
                        $contact->client_id =
                            $this->table( $transport3 )->find( array( 'changed_dtm' => $contact->changed_dtm ) )
                                 ->current()->id();
                    }
                    catch ( Exception $ex )
                    {

                        $results[ 'message' ] = 'Error.';
                    }
                }
                try
                {
                    $contact->status_id = Status::NEW_;
                    $this->trigger( 'presave', $contact );
                    $this->table( $transport2 )->save( $contact );
                    $this->trigger( 'presave', $lead );
                    $lead->status_id = Status::CONVERTED;
                    $this->trigger( 'presave', $lead );
                    $this->table( $transport )->save( $lead );
                }
                catch ( \Exception $ex )
                {
                    $results[ 'message' ] = 'Error.';
                }

                return $this->refresh( $transport . ' data was successfully converted',
                                       $this->url()->fromRoute( 'lead', array( 'action' => 'list' ) ) );
            }
        }
        $form->prepare();
        $results[ 'form' ]       = $form;
        $results[ 'permission' ] = $permission;
        $results[ 'saurl' ]      = '?back=' . $this->generateLabel();
        $results[ 'saurlback' ]  = $this->getSaurlBack( $this->params()->fromQuery( 'back', 'home' ) );
        $results[ 'user' ]       = $this->user();

        return $results;


        $id         = (string) $subject->getParam( 'id', '0' );
        if ( $id == '0' )
        {
            // :FIXME: check create permission
            $model = $subject->getGateway()->model();
            $mode  = Acl::MODE_CREATE;
        }
        else
        {
            // :FIXME: add security filter
            $model = $subject->getGateway()->get( $id );
            $mode  = Acl::MODE_EDIT;
        }
        $form = $subject->getFormServiceVerify()->get( $model, $mode );
        $form->setRoute( strtolower( $modelName ) )->setAction( $subject->getParams()->fromRoute( 'action', 'edit' ) );
        if ( $id != '0' )
        {
            $form->setActionParams( [ 'id' => $id ] );
        }
        $results = [ ];
        try
        {
            prn( 'AddObserver123' );
            $old_data = $model->split( $form->getValidationGroup() );
            prn( 'AddObserver', $old_data );
            //Это жесть конечно и забавно, но на время сойдет :)
            $model_bind = $model->toArray();
            foreach ( $model_bind as $_k => $_v )
            {
                if ( substr( $_k, -4 ) == '_dtm' )
                {
                    $model->$_k = str_replace( ' ', 'T', $_v );
                }
            }
            //Конец жести
        }
        catch ( \Exception $ex )
        {
//            return $subject->getParams()->getController()->redirect()
//                           ->toRoute( $form->getRoute(), array( 'action' => 'list' ) );
        }
        $request = $subject->getParams()->getController()->getRequest();
        if ( $request->isPost() )
        {
//            $form->addInputFilter( $model->getInputFilter() );
            $form->setData( $request->getPost() );
            if ( $form->isValid() )
            {
                $model_data = array();
                foreach ( $form->getData() as $_k => $_data )
                {
                    $model_data += is_array( $_data ) ? $_data : array( $_k => $_data );
                }
                $model->merge( $model_data );
                $model->merge( $old_data );

                $subject->getParams()->getController()->trigger( 'presave', $model->getDataModel() );
                if ( !isset( $results[ 'message' ] ) || !strlen( $results[ 'message' ] ) )
                {
                    try
                    {
                        $subject->getGateway()->save( $model );
                    }
                    catch ( \Exception $ex )
                    {
                        $results[ 'message' ] = 'Invalid input data.' . $ex->getMessage();
                    }
                }
                if ( !isset( $results[ 'message' ] ) || !strlen( $results[ 'message' ] ) )
                {
                    $subject->getParams()->getController()->trigger( 'postsave', $model->getDataModel() );
                    $url = $subject->getParams()->getController()->getBackUrl();
                    if ( $url == null || $url == '/' )
                    {
                        $actionParams = [ 'action' => $form->getBackAction() ];
                        if ( $form->getActionParams() !== null )
                        {
                            $actionParams += $form->getActionParams();
                        }
                        $url = $subject->getParams()->getController()->url()
                                       ->fromRoute( $form->getRoute(), $actionParams );
                    }
                    $subject->setRedirect( $subject->getParams()->getController()->refresh( $modelName .
                                                                                            ' data was successfully saved',
                                                                                            $url ) );

                    return;
                }
            }
        }
        else
        {
            $form->bind( $model );
        }
        $form->prepare();
        $results[ 'form' ] = $form;
        if ( isset( $form->getFieldsets()[ 'saurl' ] ) )
        {
            $form->getFieldsets()[ 'saurl' ]->get( 'back' )->setValue( $subject->getParams()
                                                                               ->fromQuery( 'back', 'home' ) );
        }
        $subject->setData( $results );
    }

}