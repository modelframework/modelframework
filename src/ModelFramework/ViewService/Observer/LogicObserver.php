<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class LogicObserver
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

        $models = $subject->getGateway()->find($query->getWhere());

        foreach ($models as $model) {
            $subject->getLogicServiceVerify()->get($viewConfig->mode, $viewConfig->model)->trigger($model);
        }

        $subject->setRedirect($subject->refresh($viewConfig->title.' successfull', 'http://wepo.loc/common/mail/index.html'));
    }
}
