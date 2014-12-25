<?php
/**
 * Class ConditionObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class ConditionObserver extends AbstractConfigObserver
{

    public function process( $model, $key, $value )
    {
        if($this->getSubject()->getAuthServiceVerify()->getUser()->id() != $model->owner_id)
        {
            return;
        }
        $modelName = $model->getModelName();
        $status_id = $model->$key;
        if($status_id == $value[0])
        {
            $model->$key = $value[1];
        }
        $this->getSubject()->getGatewayServiceVerify()->get($modelName)->save($model);
    }

}