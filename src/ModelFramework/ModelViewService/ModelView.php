<?php

namespace ModelFramework\ModelViewService;

use ModelFramework\DataModel\AclDataModel;
use ModelFramework\DataModel\Custom\ModelConfigAwareInterface;
use ModelFramework\DataModel\Custom\ModelConfigAwareTrait;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareInterface;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareTrait;
use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use ModelFramework\Utility\Arr;

class ModelView
    implements ModelViewInterface, ViewConfigDataAwareInterface, ModelConfigAwareInterface, GatewayAwareInterface,
               ParamsAwareInterface
{

    use ViewConfigDataAwareTrait, ModelConfigAwareTrait, GatewayAwareTrait, ParamsAwareTrait;

    private $_plugin = null;
    private $_data = [ ];

    protected function getData()
    {
        return $this->_data;
    }

    protected function setData( array $data )
    {
        $this->_data += $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function  init()
    {
        if ( $this->getViewConfigDataVerify()->mode == 'list' )
        {
            $this->_plugin = new ViewListPlugin();
        }
    }

    public function fields()
    {
        return $this->getViewConfigDataVerify()->fields;
    }

    public function labels()
    {
        return $this->getModelConfigVerify()[ 'labels' ];
    }

//    public function process()
//    {
//        $this->_plugin->process();
//    }

    public function getParam( $name, $default = '' )
    {
        $param = $this->getParamsVerify()->fromQuery( $name, $default );
        if ( $param === $default )
        {
            $param = $this->getParamsVerify()->fromRoute( $name, $default );
        }

        return $param;
    }

    protected function getAclModelVerify()
    {
        $model = $this->getGatewayVerify()->model();
        if ( $model == null || !$model instanceof AclDataModel )
        {
            throw new \Exception( 'AclModel does not set in Gateway ' . $this->getGatewayVerify()->getTable() );
        }

        return $model;
    }

    protected function checkPermissions()
    {
        $model    = $this->getAclModelVerify();
        $_aclData = $model->getAclDataVerify();
        if ( !is_array( $_aclData->permissions ) || !in_array( 'r', $_aclData->permissions ) )
        {
            throw new \Exception( 'reading is not allowed' );
        }

        return true;
    }

    protected function order()
    {
        $our[ 'order' ] = [ ];

        $s                       = (int) $this->getParam( 'desc', 0 );
        $sort                    = $this->getParam( 'sort', '' );
        $our[ 'order' ][ $sort ] = ( $s == 1 ) ? 'desc' : 'asc';

        if ( !in_array( $sort, $this->fields() ) )
        {
            $defaults = $this->getViewConfigDataVerify()->params;

            $our[ 'order' ] = Arr::addNotNull( $our[ 'order' ], 'sort', Arr::getDoubtField( $defaults, 'sort', null ) );
            $our[ 'order' ] = Arr::addNotNull( $our[ 'order' ], 'desc', Arr::getDoubtField( $defaults, 'desc', null ) );

        }

        $this->setData( $our );
    }


    public function process()
    {
        $this->checkPermissions();
        $this->order();

        prn( $this->getData() );
    }

}