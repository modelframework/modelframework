<?php
/**
 * Class WidgetObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\DataModel\AclDataModel;
use ModelFramework\ViewService\View;

class Widget2Observer
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {

        /**
         * @var View $subject
         */
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $subject->setData( $query->getData() );

        prn( $viewConfig, $query -> getWhere() );
        return;

        $result             = [ ];


        if ( isset( $conf[ 'actions' ] ) && is_array( $conf[ 'actions' ] ) )
        {
            foreach ( $conf[ 'actions' ] as $action => $config )
            {
                foreach ( [ 'routeparams', 'queryparams' ] as $paramKey )
                {
                    foreach ( $config[ $paramKey ] as $_key => $_val )
                    {
                        if ( $_val{0} == ":" )
                        {
                            $_m                           = substr( $_val, 1 );
                            $config[ $paramKey ][ $_key ] = (string) $inModel->$_m;
                        }
                    }
                }
                $result[ 'actions' ][ $action ] = $config;
            }
        }
        if ( isset( $conf[ 'links' ] ) )
        {
            foreach ( $conf[ 'links' ] as $modelkey => $link )
            {
                foreach ( [ 'routeparams', 'queryparams' ] as $paramKey )
                {
                    if ( !isset( $link[ $paramKey ] ) )
                    {
                        $link[ $paramKey ] = [ ];
                    }
                    foreach ( $link[ $paramKey ] as $_key => $_v )
                    {
                        if ( $_v{0} == ':' )
                        {
                            $_m                         = substr( $_v, 1 );
                            $link[ $paramKey ][ $_key ] = (string) $inModel->{$_m};
                        }
                    }
                }
                $result[ 'links' ][ $modelkey ] = $link;
            }
        }
        $result[ 'data' ]  =
            $subject->getGatewayServiceVerify()->get( $subject->getViewConfig()->model )
                    ->find( $where, $conf[ 'order' ], $conf[ 'limit' ] );
        $result[ 'model' ] = strtolower( $conf[ 'model' ] );
//        prn($result);

//        prn( $result['data']->toArray() );
//        if ( $modelName == 'Document' )
//        {
//            prn( $modelName, $where );
//        }

        return $result;
    }

}