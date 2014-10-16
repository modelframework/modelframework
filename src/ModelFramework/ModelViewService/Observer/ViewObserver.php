<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use Wepo\Model\Table;

class ViewObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
//        $viewConfig          = $subject->getViewConfigDataVerify();
        $id                  = (string) $subject->getParams()->fromRoute( 'id', 0 );
        $result              = [ ];
        $result[ 'widgets' ] = [ ];
        $model               = $subject->getGatewayVerify()->findOne( [ '_id' => $id ] );
        prn( $subject->getGatewayVerify()->model() );
        if ( !$model )
        {
            throw new \Exception( 'Data not found' );
        }
        $result[ 'model' ]          = $model;
        prn('MODEL', $model->toArray());
        $result[ 'params' ][ 'id' ] = $id;
        $this->widgets( $subject, $model );
        $subject->setData( $result );
    }

    public function widgets( \SplSubject $subject, $model )
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
            $result[ 'widgets' ][ $wConf->name ] = $this->getWidget( $subject, $wConf, $model );
        }
        $subject->setData( $result );
    }

    public function getWidget( $subject, $conf, $inModel )
    {
        prn('111');
        $result = [ ];

        $conf = $conf->toArray();

        $modelName = $conf[ 'data_model' ];
        $where     = $conf[ 'where' ];
        $model     = $subject->getGatewayServiceVerify()->get( $modelName )->model();
        $fieldType = $subject->getGatewayServiceVerify()->get( 'Field' )->model();
        $tableId = Table::getTableId( $modelName );
        prn( 'Widget observer', $fieldType, $conf );
        $result = [];
        $result['fields'] = $conf['fields'];
        $result['labels'] = ['subject' => 'Subject', 'description' => 'Description'];

//        exit;
//        public function fields( $fields, $order = array() )
//    {
//        $result[ 'fields' ] = [ 'id' ];
//        foreach ( $this->table( 'Field' )->find( $fields, $order ) as $_field )
//        {
//            $result[ 'fields' ][ ]                                                           = $_field->field;
//            $result[ 'labels' ][ empty( $_field->alias ) ? $_field->field : $_field->alias ] = $_field->label;
//        }
//
//        return $result;
//    }
//        $result += $this->fields( [
//                                      'table_id' => $fieldType->table_id( $tableId ),
//                                      'visible'  => $fieldType->visible( 1 ),
//                                      'target'   => 'widget'
//                                  ], [ 'order' => 'asc' ] );
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
        prn('123');
        if ( isset( $conf[ 'model_link' ] ) )
        {
            prn('if 111');
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
                        prn('if 111',$inModel, $_m);
                        $link[ 'params' ][ $_key ] = (string) $inModel->{$_m};
                    }
                }
                $result[ 'model_link' ][ ] = $link;
            }
        }
        $result[ 'data' ]  = $this->table( $modelName )->find( $where, $conf[ 'order' ], $conf[ 'limit' ] );
        $result[ 'model' ] = strtolower( $modelName );

        $subject->setData( $result );
    }

}