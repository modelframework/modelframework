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

class ListObserver1
    implements \SplObserver
{

    private $_subject = null;

    public function setSubject( \SplSubject $subject )
    {
        $this->_subject = $subject;
    }

    public function getSubject( )
    {
        return $this->_subject;
    }

    /**
     * @param \SplSubject|View $subject
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $viewConfig = $subject->getViewConfigVerify();
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



        prn($viewConfig->query);
        prn($subject->getQueryServiceVerify()->get($viewConfig->query));
        exit;


        $_where = $this->processWhere( $_where );
//        prn($_where);

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


//        prn($result['paginator']->getCurrentItems());
        $subject->getLogicServiceVerify()->trigger( 'prelist', $result['paginator']->getCurrentItems() );


//        $subject->getLogicServiceVerify()->trigger( 'prelist', $subject
//            ->getGatewayVerify()->model()->getDataModel() );



        $result[ 'rows' ]   = [ 5, 10, 25, 50, 100 ];
        $result[ 'params' ] = [
            'data' => lcfirst( $viewConfig->model ),
            'view' => $viewConfig->mode,
            'sort' => $subject->getParams()->fromRoute( 'sort', null ),
            'desc' => (int) $subject->getParams()->fromRoute( 'desc', 0 )
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
            $defaults = $subject->getViewConfigVerify()->params;

            $result[ 'order' ] =
                Arr::addNotNull( $result[ 'order' ], 'sort', Arr::getDoubtField( $defaults, 'sort', null ) );
            $result[ 'order' ] =
                Arr::addNotNull( $result[ 'order' ], 'desc', Arr::getDoubtField( $defaults, 'desc', null ) );

        }
        $subject->setData( $result );
    }


    private function processWhere($where)
    {
        foreach ( $where as $_f => $_v )
        {
            if ( is_array( $_v ) )
            {
                /*
                foreach ( $_v as $_key => $_value )
                {
                    if ( $_value{0} == ':' && ( $_d =  $this->getSubject()->getParam( substr( $_value, 1 ), null) !== null ) )
                    {
                        $_m                                            = substr( $_value, 1 );
                        $where[ $_f . "." . $inModel->getModelName() ] = [ $model->$_f( $inModel->{$_m} ) ];
                        unset( $where[ $_f ] );
                    }
                }
                */
            }
            elseif ( $_v{0} == ':' )
            {
                $_m           = substr( $_v, 1 );
                $_d =  $this->getSubject()->getParam( substr( $_v, 1 ), null) ;
                if ( $_d !== null )
                {
                    $where[ $_f ] = $_d;
                }
            }
//            elseif ( $_v{0} == '!' )
//            {
//                //FIXME
//                $func = substr( $_v, 1 );
//                unset( $where[ $_f ] );
//                $_f = substr( $_f, 2 );
//                if ( method_exists( $this, $func ) )
//                {
//                    $where[ $_f ] = $this->$func();
//                    unset( $where[ $_f ] );
//                }
//                //
//            }
        }

        return $where;
    }

}