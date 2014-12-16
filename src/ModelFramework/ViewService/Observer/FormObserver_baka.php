<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;
use Wepo\Lib\Acl;

class FormObserver implements \SplObserver
{

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigVerify();
        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $modelName  = $viewConfig->model;
        $data       = $subject->getData();
        if ( $viewConfig->mode == 'insert' )
        {
            $mode = Acl::MODE_CREATE;
        }
        elseif ( $viewConfig->mode == 'update' )
        {
            $mode = Acl::MODE_EDIT;
        }
        else
        {
            throw new \Exception( "Wrong mode  '" . $viewConfig->mode . "' in  " . $viewConfig->key .
                                  ' View Config for the FormObserver ' );
        }
        if ( isset( $data[ 'model' ] ) )
        {
            $model = $data[ 'model' ];

        }
        else
        {
            if ( $viewConfig->mode == 'insert' )
            {
                $model = $subject->getGateway()->model();
            }
            elseif ( $viewConfig->mode == 'update' )
            {
                $model = $subject->getGateway()->findOne( $query->getWhere() );
                if ( $model == null )
                {
                    throw new \Exception( 'Data is not accessible' );
                }
            }
        }

        $form = $subject->getFormServiceVerify()->get( $model, $mode );
        $form->setRoute( 'common' );
        $form->setActionParams( [ 'data' => strtolower( $modelName ), 'view' => $viewConfig->mode ] );

        $id = (string) $subject->getParam( 'id', '0' );
        if ( $id != '0' )
        {
            $form->setActionParams( [ 'id' => $id ] );
        }

        $results  = [ ];
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
        $request = $subject->getParams()->getController()->getRequest();
        if ( $request->isPost() )
        {
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
                $subject->getLogicServiceVerify()->trigger( 'pre' . $viewConfig->mode, $model->getDataModel() );
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
                    $subject->getLogicServiceVerify()->trigger( 'post' . $viewConfig->mode,
                                                                $model->getDataModel() );
                    $url = $subject->getBackUrl();
                    if ( $url == null || $url == '/' )
                    {
                        $url = $subject->getParams()->getController()->url()
                                       ->fromRoute( $form->getRoute(), $form->getActionParams() );
                    }
                    $subject->setRedirect( $subject->refresh( $modelName . ' data was successfully saved', $url ) );

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