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

class ConvertObserver implements \SplObserver
{

    /**
     * @param \SplSubject|View $subject
     */
    public function update( \SplSubject $subject )
    {
        $result     = [ ];
        $request    = $subject->getParams()->getController()->getRequest();
        $viewConfig = $subject->getViewConfigVerify();
        $modelName  = $viewConfig->model;
        $route      = strtolower( $modelName );
        $id         = (string) $subject->getParams()->fromRoute( 'id', 0 );
        if ( !$id )
        {
            return $subject->redirect()->toRoute( $route );
        }
        $object                       = $subject->getGatewayServiceVerify()->get( $modelName )->get( $id );
        $convertConfig                =
            $subject->getConfigServiceVerify()->getByObject( $modelName, new DataMappingConfig() );
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
        $result[ 'route' ] = $route;
        $result[ 'id' ]    = $id;
        $subject->setData( $result );
        if ( $request->isPost() )
        {
            foreach ( $result[ 'convertedObjects' ] as $object )
            {
//                $subject->getParams()->getController()->trigger( 'presave', $object );
                $subject->getGatewayServiceVerify()->get( $object->getModelName() )->save( $object );
//                $subject->getParams()->getController()->trigger( 'postsave', $object );
            }
            $url = $subject->getParams()->getController()->getBackUrl();
            if ( $url == null || $url == '/' )
            {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute( $route, [ 'action' => 'list' ] );
            }
            $subject->setRedirect( $subject->getParams()->getController()->refresh( $modelName .
                                                                                    ' data was successfully converted',
                                                                                    $url ) );

            return;
        }

        return;
    }

}