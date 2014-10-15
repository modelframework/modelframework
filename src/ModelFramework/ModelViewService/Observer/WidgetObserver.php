<?php
/**
 * Class ListObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use Wepo\Model\Table;

class WidgetObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigDataVerify();
        $result     = [ ];
//        $result[ 'widgets' ] = $this->widgets( $viewConfig->model, $model );
        $pageName            = strtolower( $viewConfig->model );
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
            $result[ 'widgets' ][ $wConf->name ] = $this->getWidget( $subject, $wConf, $subject->getAclModelVerify() );
        }
        $subject->setData( $result );
    }

    public function getWidget( $subject, $conf, $inModel )
    {
        $result = [ ];

        $conf = $conf->toArray();

        $modelName = $conf[ 'data_model' ];
        $where     = $conf[ 'where' ];
        $model     = $subject->getGatewayServiceVerify()->get( $modelName )->model();
        $fieldType = $subject->getGatewayServiceVerify()->get( 'Field' )->model();
        prn( 'Widget observer', $fieldType );
        exit;
        $tableId = Table::getTableId( $modelName );
        $result += $this->fields( [
                                      'table_id' => $fieldType->table_id( $tableId ),
                                      'visible'  => $fieldType->visible( 1 ),
                                      'target'   => 'widget'
                                  ], [ 'order' => 'asc' ] );
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
            elseif ( $_v{0} == ':' )
            {
                $_m           = substr( $_v, 1 );
                $where[ $_f ] = $model->$_f( $inModel->{$_m} );
            }
        }

        if ( isset( $conf[ 'action' ] ) )
        {
            foreach ( $conf[ 'action' ] as $action => $config )
            {
                $result[ 'action' ][ $action ] = $config;
            }
        }
        if ( isset( $conf[ 'model_link' ] ) )
        {
            foreach ( $conf[ 'model_link' ] as $link )
            {
                if ( !isset( $link[ 'params' ] ) )
                {
                    continue;
                }
                foreach ( $link[ 'params' ] as $_key => $_v )
                {
                    if ( $_v{0} == ':' )
                    {
                        $_m = substr( $_v, 1 );
//                        $link[ 'params' ][ $_key ] = $model->$_m( $inmodel->{$_m} );
                        $link[ 'params' ][ $_key ] = (string) $inmodel->{$_m};
                    }
                }
                $result[ 'model_link' ][ ] = $link;
            }
        }
        $result[ 'data' ]  = $this->table( $modelName )->find( $where, $conf[ 'order' ], $conf[ 'limit' ] );
        $result[ 'model' ] = strtolower( $modelName );

        return $result;
    }

}