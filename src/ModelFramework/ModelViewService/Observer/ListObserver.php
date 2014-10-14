<?php
/**
 * Class ListObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use ModelFramework\Utility\Arr;

class ListObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigDataVerify();
        $this->order( $subject );
//        $result[ 'permission' ]   = 1;
        $result[ 'search_query' ] = $searchQuery = $subject->getParam( 'q', '' );
        if ( $searchQuery )
        {
            $result[ 'params' ][ 'q' ] = $searchQuery;
        }
//        # :TODO: add permissions query
//        if ( $permission == Auth::OWN )
//        {
//            $field = [ 'owner_id' => (string) $this->user()->id() ];
//        }
        $permissionQuery = [ ];
        $_where          = $viewConfig->query;
        $_dataWhere      = $permissionQuery + $_where;
        if ( empty( $searchQuery ) )
        {
            $_where = $_dataWhere;
        }
    else
        {
            $_where = [
                '$and' => [ $_dataWhere, [ '$text' => [ '$search' => $searchQuery ] ] ]
            ];
        }
        $result[ 'paginator' ] =
            $subject
                ->getGatewayVerify()
                ->getPages( $subject->fields(), $_where, $subject->getData()[ 'order' ] );
        if ( $result[ 'paginator' ]->count() > 0 )
        {
            $result[ 'paginator' ]->setCurrentPageNumber( $subject->getParam( 'page', 1 ) )
                                  ->setItemCountPerPage( $viewConfig->rows );
        }
        $result[ 'rows' ]   = [ 5, 10, 25, 50, 100 ];
        $result[ 'params' ] = [
            'action' => $viewConfig->mode,
            'model'  => $viewConfig->model,
            'sort'   => $subject->getParams()->fromRoute( 'sort', null ),
            'desc'   => (int) $subject->getParams()->fromRoute( 'desc', 0 )
        ];
        $subject->setData( $result );
    }

    protected function order( \SplSubject $subject )
    {
        $result[ 'order' ] = [ ];
        $s                 = (int) $subject->getParam( 'desc', 0 );
        $sort              = $subject->getParam( 'sort', '' );
        if ( $sort != '' )
        {
            $result[ 'order' ][ $sort ] = ( $s == 1 ) ? 'desc' : 'asc';
        }
        if ( !in_array( $sort, $subject->fields() ) )
        {
            $defaults = $subject->getViewConfigDataVerify()->params;

            $result[ 'order' ] =
                Arr::addNotNull( $result[ 'order' ], 'sort', Arr::getDoubtField( $defaults, 'sort', null ) );
            $result[ 'order' ] =
                Arr::addNotNull( $result[ 'order' ], 'desc', Arr::getDoubtField( $defaults, 'desc', null ) );

        }
        $subject->setData( $result );
    }

}