<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class TriggerObserver extends AbstractConfigObserver
{
    use SubjectAwareTrait;

    public function process($model, $key, $value)
    {
        $action    = $value;
        $srcConfig = $this->getSubject()->getModelServiceVerify()->getParsedModelConfig($model->getModelName())->joins;

        foreach ($srcConfig as $join) {
            prn($join);
            if (isset($join[ 'on' ][ $key ])) {
                echo $trgModelName   = $join[ 'model' ];
                $trgSearchField = $join[ 'on' ][ $key ];
                $trgModelGW     = $this->getSubject()->getGatewayService()->get($trgModelName);
                prn($trgModelGW);
                $trgModel       = $trgModelGW->find([ $trgSearchField => $model->$key ])->current();
                $logic = $this->getSubject()->getLogicService()->get($action, $trgModel->getModelName());
                $logic->trigger($trgModel);
            }
        }
    }
}
