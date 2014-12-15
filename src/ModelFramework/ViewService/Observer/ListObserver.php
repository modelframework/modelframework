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

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

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

        $subject->getLogicServiceVerify()->trigger( 'prelist',  $result[ 'paginator' ]->getCurrentItems() );
        $subject->getLogicServiceVerify()->trigger( 'postlist', $result[ 'paginator' ]->getCurrentItems() );

//        $subject->getLogicServiceVerify()->trigger( 'prelist', $subject
//            ->getGatewayVerify()->model()->getDataModel() );

        $result[ 'rows' ] = [ 5, 10, 25, 50, 100 ];
        $data = $subject->getData();

        foreach ( [ 'actions', 'links' ] as $datapartam )
        {
            foreach ( $data[ $datapartam ] as $key => $link )
            {
                foreach ( [ 'routeparams', 'queryparams' ] as $keyparams )
                {
                    foreach ( $link[ $keyparams ] as $paramkey => $param )
                    {
                        if ( $param{0} == ':' )
                        {
                            $data[ $datapartam ][ $key ][ $keyparams ][ $paramkey ] =
                                $subject->getParam( substr( $param, 1 ), '' );
                        }
                    }

                }
            }
        }

        $subject->setData( $data );
        $result[ 'params' ] = [
            'data' => strtolower( $viewConfig->model ),
            'view' => $viewConfig->mode,
            //  'sort' => $subject->getParams()->fromRoute( 'sort', null ),
            //  'desc' => (int) $subject->getParams()->fromRoute( 'desc', 0 )
        ];

        $subject->setData( $result );
    }

}