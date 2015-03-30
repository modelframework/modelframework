<?php
/**
 * Class UniqidObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class UniqidObserver extends AbstractConfigObserver
{
    public function process($model, $key, $value)
    {
        if (empty($model->$key)) {
            $model->$key = time().mt_rand(0,9).mt_rand(0,9);
        }
    }
}
