<?php
/**
 * Class ListObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\Utility\Arr;
use ModelFramework\ViewService\View;
use Wepo\Lib\Acl;

class ListObserver
    implements \SplObserver
{

    /**
     * @param \SplSubject|View $subject
     */
    public function update( \SplSubject $subject )
    {

        $viewConfig = $subject->getViewConfigVerify();

//        prn( $subject -> getGatewayVerify() -> model()-> getDataModel () );

        $model = $subject->getGatewayVerify()->model();

//        prn( $subject -> getFormServiceVerify() -> getPermittedConfig($model, Acl::MODE_READ ) );
//        prn( $subject -> getFormServiceVerify() -> getFieldPermissions($model, Acl::MODE_READ ) );

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

//        prn( $subject -> getData() );

        $subject->setData( $query->getData() );

        $result[ 'paginator' ] =
            $subject
                ->getGatewayVerify()
                ->getPages( $subject->fields(), $query->getWhere(), $query->getOrder() );
        if ( $result[ 'paginator' ]->count() > 0 )
        {
            $result[ 'paginator' ]->setCurrentPageNumber( $subject->getParam( 'page', 1 ) )
                                  ->setItemCountPerPage( $viewConfig->rows );
        }

        prn('gw', $subject->getGatewayVerify()->findOne(
                $query->getWhere()
            )
        );
//        prn($subject->getGatewayVerify()->findOne($query->getWhere()));

        $subject->getLogicServiceVerify()->trigger( 'prelist', $result[ 'paginator' ]->getCurrentItems() );

//        $subject->getLogicServiceVerify()->trigger( 'prelist', $subject
//            ->getGatewayVerify()->model()->getDataModel() );

        $result[ 'rows' ]   = [ 5, 10, 25, 50, 100 ];

        $result[ 'params' ] = [
            'data' => lcfirst( $viewConfig->model ),
            'view' => $viewConfig->mode,
            //            'sort' => $subject->getParams()->fromRoute( 'sort', null ),
            //            'desc' => (int) $subject->getParams()->fromRoute( 'desc', 0 )
        ];
        $subject->setData( $result );
    }

}