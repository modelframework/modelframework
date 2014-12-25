<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\DataMapping\DataMappingConfig\DataMappingConfig;
use ModelFramework\ViewService\View;

class ConvertObserver extends AbstractObserver
{

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $dataModel = $this->initModel();

        prn($dataModel);
        exit();

        $form = $this->initForm();

        $this->process( $form, $this->getModel() );

        $model = $this->setModel( $dataModel );

    }


    public function update_b00bs( \SplSubject $subject )
    {
        $result                       = [ ];
        $request                      = $subject->getParams()->getController()->getRequest();
        $viewConfig                   = $subject->getViewConfigVerify();
        $modelName                    = $viewConfig->model;
        $data                         = strtolower( $modelName );
        $id                           = (string) $subject->getParams()->fromRoute( 'id', 0 );
        $object                       = $subject->getGatewayServiceVerify()->get( $modelName )->get( $id );


        $convertConfig                =
            $subject->getConfigServiceVerify()->getByObject( $modelName, new DataMappingConfig() );

        prn( $convertConfig);
        exit();

        $result[ 'convertedObjects' ] = [ ];
        foreach ( $convertConfig->targets as $_key => $_value )
        {
            $convertObject = $subject->getGatewayServiceVerify()->get( $_key )->model();
            foreach ( $_value as $_k => $_v )
            {
                $convertObject->$_v = $object->$_k;
            }
            $result[ 'convertedObjects' ][ $_key ] = $convertObject;
        }
        $result[ 'model' ] = $object;
        $result[ 'id' ]    = $id;
        $subject->setData( $result );

        prn($result[ 'convertedObjects' ]);
        exit();


        if ( $request->isPost() )
        {
            $subject->getLogicServiceVerify()->get( 'preconvert', $model->getModelName() )
                    ->trigger( $result[ 'convertedObjects' ] );

            foreach ( $result[ 'convertedObjects' ] as $object )
            {
                $subject->getGatewayServiceVerify()->get( $object->getModelName() )->save( $object );
            }

            $subject->getLogicServiceVerify()->get( 'postconvert', $model->getModelName() )
                    ->trigger( $result[ 'convertedObjects' ] );

            $url = $subject->getBackUrl();
            if ( $url == null || $url == '/' )
            {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute( 'common', [ 'data' => $data, 'view' => 'list' ] );
            }
            $subject->setRedirect( $subject->refresh( $modelName .
                                                      ' data was successfully converted',
                                                      $url ) );
        }

        return;
    }

}