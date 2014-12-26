<?php
/**
 * Class ConstantObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;


use ModelFramework\AclService\AclDataModel;

class ConstantObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        $model->$key = $value;
    }


}