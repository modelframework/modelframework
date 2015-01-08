<?php
/**
 * Class WidgetObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;

class WidgetObserver
    implements \SplObserver
{
    /**
     * @param \SplSubject|View $subject
     */
    public function update(\SplSubject $subject)
    {
        $viewConfig = $subject->getViewConfigVerify();
        $query      =
            $subject->getQueryServiceVerify()
                    ->get($viewConfig->query)
                    ->setParams($subject->getParams())
                    ->process();
        $subject->setData($query->getData());

        $data = $subject->getData();

        foreach ([ 'actions', 'links' ] as $datapartam) {
            foreach ($data[ $datapartam ] as $key => $link) {
                foreach ([ 'routeparams', 'queryparams' ] as $keyparams) {
                    foreach ($link[ $keyparams ] as $paramkey => $param) {
                        if ($param{0} == ':') {
                            $data[ $datapartam ][ $key ][ $keyparams ][ $paramkey ] =
                                $subject->getParam(substr($param, 1), '');
                        }
                    }
                }
            }
        }

        $subject->setData([ 'actions' => $data['actions'], 'links' => $data['links'] ]);

        $result[ 'paginator' ] =
            $subject
                ->getGatewayVerify()
                ->getPages($subject->fields(), $query->getWhere(), $query->getOrder());

        if ($result[ 'paginator' ]->count() > 0) {
            $result[ 'paginator' ]->setCurrentPageNumber($subject->getParam('page', 1))
                                  ->setItemCountPerPage($viewConfig->rows);
        }

//        if ( $viewConfig->document=='Notes')
//        {
//            prn( $viewConfig, $query->getWhere(), $result['paginator']->getCurrentItems()->toArray());
//        }


        $subject->getLogicServiceVerify()->get('prelist', $viewConfig->model)
                ->trigger($result[ 'paginator' ]->getCurrentItems());
        $subject->getLogicServiceVerify()->get('postlist', $viewConfig->model)
                ->trigger($result[ 'paginator' ]->getCurrentItems());

//        $subject->getLogicServiceVerify()->trigger( 'prelist', $result[ 'paginator' ]->getCurrentItems() );
//        $subject->getLogicServiceVerify()->trigger( 'postlist', $result[ 'paginator' ]->getCurrentItems() );

        $result[ 'rows' ] = [ 5, 10, 25, 50, 100 ];

        $result[ 'params' ] = [
            'data' => strtolower($viewConfig->model),
            'view' => $viewConfig->mode,
        ];

        $subject->setData($result);
    }
}
