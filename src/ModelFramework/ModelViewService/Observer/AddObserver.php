<?php
/**
 * Class AddObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use Wepo\Lib\Acl;

class AddObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        prn( 'Add observer' );

        $viewConfig = $subject->getViewConfigDataVerify();

        $modelName  = $viewConfig->model;

        prn( $subject->getData(), $modelName );

//        $id = (string) $subject->getParams()->fromRoute( 'id', '0' );
        $id = (string) $subject->getParam( 'id', '0' );
        prn( $id );
        $modelGateway = $subject->getGatewayServiceVerify()->get( $modelName );
        $modelGateway =  $subject->getGateway();
        if ( $id == '0' )
        {
            // :FIXME: check create permission
            $model = $modelGateway->model();
            $mode  = Acl::MODE_CREATE;
        }
        else
        {
            // :FIXME: add security filter
            $model = $modelGateway->get( $id );
            $mode  = Acl::MODE_EDIT;
        }

        $formService = $subject->getFormServiceVerify();

        prn('subject', $subject);
        $modelConfig = $subject->getModelConfig();
        $aclModel = $subject->getGateway()->model();
        $aclData = $aclModel->getAclData();

        prn($modelConfig, $aclModel, $aclData);

        $form = $formService -> createFormWithConfig( $modelConfig, $aclData );

//          $form = $subject->getModelServiceVerify()->get( $modelName, $model, $mode );
        prn( 'AddObserver', $model, $mode );

        $form = $subject->getFormServiceVerify()->get( $model, $mode );

        prn('form', $form);
        exit();

        $form->setRoute( strtolower( $modelName ) )->setAction( $this->params()->fromRoute( 'action', 'edit' ) );
        if ( $id != '0' )
        {
            $form->setActionParams( [ 'id' => $id ] );
        }

        $results = [ ];
        try
        {
            $old_data = $model->split( $form->getValidationGroup() );

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
            return $this->redirect()->toRoute( $form->getRoute(), array( 'action' => 'list' ) );
        }
        $request = $this->getRequest();
        if ( $request->isPost() )
        {
            $form->addInputFilter( $model->getInputFilter() );
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

                $this->trigger( 'presave', $model );
                if ( !isset( $results[ 'message' ] ) || !strlen( $results[ 'message' ] ) )
                {
                    try
                    {
                        $this->table( $modelName )->save( $model );
                    }
                    catch ( \Exception $ex )
                    {
                        $results[ 'message' ] = 'Invalid input data.' . $ex->getMessage();
                    }
                }
                if ( !isset( $results[ 'message' ] ) || !strlen( $results[ 'message' ] ) )
                {
                    $this->trigger( 'postsave', $model );
                    $url = $this->getBackUrl();
                    if ( $url == null || $url == '/' )
                    {
                        $actionParams = [ 'action' => $form->getBackAction() ];
                        if ( $form->getActionParams() !== null )
                        {
                            $actionParams += $form->getActionParams();
                        }
                        $url = $this->url()->fromRoute( $form->getRoute(), $actionParams );
                    }

                    return $this->refresh( $modelName . ' data was successfully saved', $url );
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
            $form->getFieldsets()[ 'saurl' ]->get( 'back' )->setValue( $this->params()->fromQuery( 'back', 'home' ) );
        }

        return $results;
    }

}