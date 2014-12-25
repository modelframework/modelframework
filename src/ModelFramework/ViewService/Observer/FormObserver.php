<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use Wepo\Lib\Acl;

class FormObserver extends  AbstractObserver
{

    public function process( $model )
    {
        $form = $this->initForm();
        $this->processForm( $form, $this->getModel() );
    }

    public function initForm()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        if ( $viewConfig->mode == 'insert' )
        {
            $mode = Acl::MODE_CREATE;
        }

        if ( $viewConfig->mode == 'update' )
        {
            $mode = Acl::MODE_EDIT;
        }

        $form = $subject->getFormServiceVerify()->get( $this->getModel(), $mode );

        $form->setRoute( 'common' );
        $form->setActionParams( [ 'data' => strtolower( $viewConfig->model ), 'view' => $viewConfig->mode ] );

        if ( $this->getModel()->id() !== '' )
        {
            $form->setActionParams( [ 'id' => $this->getModel()->id() ] );
        }

        if ( isset( $form->getFieldsets()[ 'saurl' ] ) )
        {
            $form->getFieldsets()[ 'saurl' ]->get( 'back' )->setValue( $subject->getParams()
                                                                               ->fromQuery( 'back', 'home' ) );
        }

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
                $subject->getLogicServiceVerify()->get( 'pre' . $viewConfig->mode, $model->getModelName() )
                        ->trigger( $model->getDataModel() );
                try
                {
                    $subject->getGateway()->save( $model );
                }
                catch ( \Exception $ex )
                {
                    $results[ 'message' ] = 'Invalid input data.' . $ex->getMessage();
                }
                if ( !isset( $results[ 'message' ] ) || !strlen( $results[ 'message' ] ) )
                {
                    $subject->getLogicServiceVerify()->get( 'post' . $viewConfig->mode, $model->getModelName() )
                            ->trigger( $model->getDataModel() );
                    $url = $subject->getBackUrl();
                    if ( $url == null || $url == '/' )
                    {
                        $url = $subject->getParams()->getController()->url()
                                       ->fromRoute( $form->getRoute(), $form->getActionParams() );
                    }
                    $subject->setRedirect( $subject->refresh( $model->getModelName() . ' data was successfully saved',
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

        $subject->setData( $results );
    }

}