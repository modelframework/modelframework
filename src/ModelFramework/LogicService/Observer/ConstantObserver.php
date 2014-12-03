<?php
/**
 * Class ConstantObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;


class ConstantObserver extends AbstractObserver
{

    public function process( $model, $key, $value )
    {
        $model->$key = $value;
    }


}