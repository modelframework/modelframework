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
        //        prn('trigger');
        $action    = $value;
        $srcConfig = $this->getSubject()->getModelConfigParserService()->getModelConfig($model->getModelName())[ 'joins' ];
//        prn($srcConfig, $model);
        foreach ($srcConfig as $join) {
            if (isset($join[ 'on' ][ $key ])) {
                $trgModelName   = $join[ 'model' ];
                $trgSearchField = $join[ 'on' ][ $key ];
                $trgModelGW     = $this->getSubject()->getGatewayService()->get($trgModelName);
                $trgModel       = $trgModelGW->find([ $trgSearchField => $model->$key ])->current();
//                prn($trgModelName . '.' . $action, $trgModel);
                $logic = $this->getSubject()->getLogicService()->get($action, $trgModel->getModelName());
                $logic->trigger($trgModel);
            }
        }
    }
}
