<?php
/**
 * Class ConcatenationObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class ConcatenationObserver extends AbstractObserver
{

    public function process( $model, $key, $value )
    {
        $model->$key = '';
        foreach ( $value as $value_key )
        {
            if ( !isset( $model->$value_key ) )
            {
                throw new \Exception( 'Field ' . $value_key . ' does not exist in model ' .
                                      $model->getModelName() );
            }
            $model->$key = $model->$key . ' ' . $model->$value_key;
        }
    }

}