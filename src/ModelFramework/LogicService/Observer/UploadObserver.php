<?php
/**
 * Created by PhpStorm.
 * User: prog4
 * Date: 12/8/14
 * Time: 5:55 PM
 */

namespace ModelFramework\LogicService\Observer;

class UploadObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        $modelName = $model->getModelName();
        $id        = $model->$key;
//        $fs = $this->getServiceLocator()->get('\Wepo\Lib\FileService');
//        prn($this->getSubject());
//        exit();
        $user      =
            $this->getSubject()->getGatewayServiceVerify()->get( 'User' )->findOne( [ '_id' => $id ] );
        $newItems  = $user->newitems;
        if ( !isset( $newItems[ $modelName ] ) )
        {
            $newItems[ $modelName ] = 0;
        }
        $newItems[ $modelName ] = (int) $newItems[ $modelName ] + $value;
        $this->getSubject()->getGatewayServiceVerify()->get( 'User' )
             ->update( [ 'newitems' => $newItems ], [ '_id' => $id ] );
    }

}