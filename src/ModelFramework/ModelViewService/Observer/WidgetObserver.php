<?php
/**
 * Class WidgetObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use ModelFramework\ModelViewService\ModelView;

class WidgetObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {

        $data = $subject->getData();

        if ( !isset( $data[ 'model' ] ) )
        {
            $id = (string) $subject->getParams()->fromRoute( 'id', 0 );
            if ( $id != 0 )
            {
                $result              = [ ];
                $result[ 'widgets' ] = [ ];
                $model               = $subject->getGatewayVerify()->findOne( [ '_id' => $id ] );

                if ( !$model )
                {
                    throw new \Exception( 'Data not found' );
                }
                $result[ 'model' ]          = $model;
                $result[ 'params' ][ 'id' ] = $id;
                $result[ 'title' ]          = $subject->getViewConfigDataVerify()->title . ' ' . $model->title;

                $subject->setData( $result );
            }
            else
            {
                $model = $subject->getGatewayVerify()->model();
            }
        }
        else
        {
            $model = $data[ 'model' ];
        }

        $this->widgets( $subject, $model );

    }

    public function widgets( \SplSubject $subject, $model )
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
            if ( $wConf->data_model == 'EventLog' ) continue;
            $result[ 'widgets' ][ $wConf->name ] = $this->getWidget( $subject, $wConf, $model );
        }

        $subject->setData( $result );
    }

    public function getWidget( $subject, $conf, $inModel )
    {
        /**
         * @var ModelView $subject
         */

        $conf      = $conf->toArray();
        $modelName = $conf[ 'data_model' ];
        $modelConfig = $subject->getModelConfigParserServiceVerify()->getModelConfig( $modelName );
        $where              = $conf[ 'where' ];
        $model              = $subject->getGatewayServiceVerify()->get( $modelName )->model();
        $result             = [ ];
        $result[ 'fields' ] = $conf[ 'fields' ];
        $result[ 'labels' ] = [  ];
        foreach ( $conf['fields'] as $field )
        {
            if ( isset($modelConfig['labels'][ $field ]) )
            {
                $result[ 'labels' ][ $field ] = $modelConfig['labels'][ $field ];
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
        if ( isset( $conf[ 'action' ] ) )
        {
            foreach ( $conf[ 'action' ] as $action => $config )
            {
                foreach ( $config as $_key => $_val )
                {
                    if ( $_val{0} == ":" )
                    {
                        $_m              = substr( $_val, 1 );
                        $config[ $_key ] = (string) $inModel->$_m;
                    }
                }
                $result[ 'action' ][ $action ] = $config;
            }
        }
        if ( isset( $conf[ 'model_link' ] ) )
        {
            foreach ( $conf[ 'model_link' ] as $modelkey => $link )
            {
                if ( !isset( $link[ 'params' ] ) )
                {
                    $link[ 'params' ] = [ ];
                }
                foreach ( $link[ 'params' ] as $_key => $_v )
                {
                    if ( $_v{0} == ':' )
                    {
                        $_m                        = substr( $_v, 1 );
                        $link[ 'params' ][ $_key ] = (string) $inModel->{$_m};
                    }
                }
                $result[ 'model_link' ][ $modelkey ] = $link;
            }
        }
        $result[ 'data' ]      =
            $subject->getGatewayServiceVerify()->get( $modelName )->find( $where, $conf[ 'order' ], $conf[ 'limit' ] );
        $result[ 'model' ]     = strtolower( $modelName );

        return $result;
    }

    public function curdate()
    {
        return date( 'Y-m-d\TH(idea)' );
    }

}