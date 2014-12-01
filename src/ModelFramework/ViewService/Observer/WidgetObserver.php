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

class WidgetObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {

        $data = $subject->getData();

//        prn( 'WidgetData', $data,  $subject->getGatewayVerify()->model()->getDataModel() );

//        if ( !isset( $data[ 'model' ] ) )
//        {
//            $id = (string) $subject->getParams()->fromRoute( 'id', 0 );
//            if ( $id != 0 )
//            {
//                $result              = [ ];
//                $result[ 'widgets' ] = [ ];
//                $model               = $subject->getGatewayVerify()->findOne( [ '_id' => $id ] );
//
//                if ( !$model )
//                {
//                    throw new \Exception( 'Data not found' );
//                }
//                $result[ 'model' ]          = $model;
//                $result[ 'params' ][ 'id' ] = $id;
//                $result[ 'title' ]          = $subject->getViewConfigDataVerify()->title . ' ' . $model->title;
//
//                $subject->setData( $result );
//            }
//            else
//            {
//                $model = $subject->getGatewayVerify()->model();
//            }
//        }
//        else
//        {
//            $model = $data[ 'model' ];
//        }

        $subject->setData( $this->getWidget( $subject, null ) );

    }

    public function widgets( View $subject, $model )
    {
        $viewConfig = $subject->getViewConfigDataVerify();
        $result     = [ ];

        $pageName            = strtolower( $viewConfig->document );
        $result[ 'widgets' ] = [ ];
        $widgetConfigs       =
            $subject->getGatewayServiceVerify()->get( 'Widget' )
                    ->find( [ 'path' => $pageName ], [ 'output_order' => 'asc' ] );

        if ( !count( $widgetConfigs ) )
        {
            return [ ];
        }
        foreach ( $widgetConfigs as $wConf )
        {
            //FIXME EMAIL WIDGET
            if ( $wConf->data_model == 'Mail' ) continue;
//            if ( $wConf->data_model == 'EventLog' ) continue;
            $result[ 'widgets' ][ $wConf->name ] = $this->getWidget( $subject, $wConf, $model );
        }

        $subject->setData( $result );
    }

    /**
     * @param $subject
     * @param $inModel
     *
     * @return array
     * @throws \Exception
     */
    public function getWidget( View $subject, $inModel )
    {
        /**
         * @var View $subject
         */

        $conf = $subject->getData();

        if ( $inModel instanceof AclDataModel )
        {
            $inModel = $inModel->getDataModel();
        }

        $where              = $conf[ 'query' ];
        $model              = $subject->getGatewayServiceVerify()->get( $conf['model'] )->model();

        $result             = [ ];
        $result[ 'fields' ] = $conf[ 'fields' ];
        $result[ 'labels' ] = [ ];

        foreach ( $conf[ 'fields' ] as $field )
        {
            if ( isset( $conf[ 'labels' ][ $field ] ) )
            {
                $result[ 'fieldsLabels' ][ $field ] = $conf[ 'labels' ][ $field ];
            }
        }

        foreach ( $where as $_f => $_v )
        {
            if ( is_array( $_v ) )
            {
                foreach ( $_v as $_key => $_value )
                {
                    if ( $_value{0} == ':' && isset( $_v[ $inModel->getModelName() ] ) )
                    {
                        $_m                                            = substr( $_value, 1 );
                        $where[ $_f . "." . $inModel->getModelName() ] = [ $model->$_f( $inModel->{$_m} ) ];
                        unset( $where[ $_f ] );
                    }
                }
            }
            elseif ( $_v == ':_id' )
            {
                $where[ $_f ] = $inModel->id();
            }
            elseif ( $_v{0} == ':' )
            {
                $_m           = substr( $_v, 1 );
                $where[ $_f ] = $model->$_f( $inModel->{$_m} );
            }
            elseif ( $_v{0} == '!' )
            {
                //FIXME
                $func = substr( $_v, 1 );
                unset( $where[ $_f ] );
                $_f = substr( $_f, 2 );
                if ( method_exists( $this, $func ) )
                {
                    $where[ $_f ] = $this->$func();
                    unset( $where[ $_f ] );
                }
                //
            }
        }

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
                    if ( !isset( $link[ $paramKey] ) )
                    {
                        $link[ $paramKey ] = [ ];
                    }
                    foreach ( $link[ $paramKey ] as $_key => $_v )
                    {
                        if ( $_v{0} == ':' )
                        {
                            $_m                        = substr( $_v, 1 );
                            $link[ $paramKey ][ $_key ] = (string) $inModel->{$_m};
                        }
                    }
                }
                $result[ 'links' ][ $modelkey ] = $link;
            }
        }
        $result[ 'data' ]  =
            $subject->getGatewayServiceVerify()->get( $conf['model'] )->find( $where, $conf[ 'order' ], $conf[ 'limit' ] );
        $result[ 'model' ] = strtolower( $conf['model'] );

//        prn( $result['data']->toArray() );
//        if ( $modelName == 'Document' )
//        {
//            prn( $modelName, $where );
//        }


        return $result;
    }

    public function curdate()
    {
        return date( 'Y-m-d\TH(idea)' );
    }

}