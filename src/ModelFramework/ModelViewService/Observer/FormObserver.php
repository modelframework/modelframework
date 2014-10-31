<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use Wepo\Lib\Acl;

class FormObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigDataVerify();
        $modelName  = $viewConfig->model;
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