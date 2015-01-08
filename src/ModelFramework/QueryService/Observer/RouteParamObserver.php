<?php
/**
 * Class RouteParamObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class RouteParamObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $data = [
            'params' => [],
        ];

        $where = [ ];
        foreach ($this->getRootConfig() as $field => $param) {
            $where[ $field ] = $subject->getParam($param, 'null');
            if ($where[ $field ] !== null) {
                $data['params'][$param] = $where[ $field ];
            }
        }

        $subject->setData($data);
        $subject->setWhere($where);
    }
}
