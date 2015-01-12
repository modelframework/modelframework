<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class HTMLObserver
    implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get($viewConfig->query)
                    ->setParams($subject->getParams())
                    ->process();

        $subject->setData($query->getData());

        $result = [ ];
        $model  = $subject->getGatewayVerify()->findOne($query->getWhere());
        if (!$model) {
            throw new \Exception('Data not found');
        }

//        $data = $subject->getData();

        $result[ 'data' ]   = $model->text;

        $subject->setData($result);
    }
}
