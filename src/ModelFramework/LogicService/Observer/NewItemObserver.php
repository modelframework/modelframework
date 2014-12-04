<?php
/**
 * Class NewItemObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class NewItemObserver extends AbstractObserver
{

    public function process( $model, $key, $value )
    {
        $modelName = $model->getModelName();
        $id        = $model->$key;
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