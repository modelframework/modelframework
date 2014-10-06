<?php
/**
 * Class ModelView
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService;

use ModelFramework\DataModel\AclDataModel;
use ModelFramework\DataModel\Custom\ModelConfigAwareInterface;
use ModelFramework\DataModel\Custom\ModelConfigAwareTrait;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareInterface;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareTrait;
use ModelFramework\DataModel\DataModel;
use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Model\Table;

class ModelView
    implements ModelViewInterface, ViewConfigDataAwareInterface, ModelConfigAwareInterface, GatewayAwareInterface,
               ParamsAwareInterface
{

    use ViewConfigDataAwareTrait, ModelConfigAwareTrait, GatewayAwareTrait, ParamsAwareTrait;

    private $_plugin = null;
    private $_data = [ ];
    private $_user = null;

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser( DataModel $user )
    {
        $this->_user = $user;

        return $this;
    }

    public function getData()
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
        prn( 'ModelView labels', $this->getModelConfigVerify() );
        prn( 'ModelView fields', $this->getViewConfigDataVerify() );

        return $this->getModelConfigVerify()[ 'labels' ];
    }

    public function setSaUrl( $label, $backUrl )
    {
        $result                = [ ];
        $result[ 'saurl' ]     = '?back=' . $label;
        $result[ 'saurlback' ] = $backUrl;
        $this->setData( $result );

        return $this;
    }

    public function setDataFields()
    {
        $viewConfig = $this->getViewConfigDataVerify();

        $result                = [ ];
        $result[ 'fields' ]    = $this->fields();
        $result[ 'labels' ]    = $this->labels();
        $result[ 'modelname' ] = strtolower( $viewConfig->model );
        $result[ 'table' ]     = [ 'id' => Table::getTableId( $viewConfig->model ) ];
//        $result[ 'permission' ]   = 1;

        $result[ 'search_query' ] = '';
        $result[ 'user' ]         = $this->getUser();
        $result[ 'rows' ]         = [ 5, 10, 25, 50, 100 ];
        $result[ 'params' ]       = [
            'action' => 'list',
            'model'  => 'Lead',
            'sort'   => 'created_dtm',
            'desc'   => 1
        ];
//        prn( "Result", $result );
        $this->setData( $result );
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

        $s    = (int) $this->getParam( 'desc', 0 );
        $sort = $this->getParam( 'sort', '' );
        if ( $sort != '' )
        {
            $our[ 'order' ][ $sort ] = ( $s == 1 ) ? 'desc' : 'asc';
        }

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
        $this->setDataFields();

        prn( $this->getViewConfigDataVerify() );

        $viewConfig = $this->getViewConfigDataVerify();

        # :TODO: add permissions query

        $our[ 'paginator' ] =
            $this
                ->getGatewayVerify()
                ->getPages( $viewConfig->query, [ ], $this->getData()[ 'order' ] );

//        $our [ 'user' ] = $this ->

        $this->setData( $our );

        return $this;

    }

}