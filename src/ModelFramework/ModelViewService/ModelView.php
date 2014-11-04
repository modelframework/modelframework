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
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\FormService\FormServiceAwareTrait;
use Wepo\Model\Table;

class ModelView
    implements ModelViewInterface, ViewConfigDataAwareInterface, ModelConfigAwareInterface,
               ModelConfigParserServiceAwareInterface, ModelServiceAwareInterface, GatewayAwareInterface,
               ParamsAwareInterface, GatewayServiceAwareInterface, FormServiceAwareInterface, \SplSubject
{

    use ViewConfigDataAwareTrait, ModelConfigAwareTrait, GatewayAwareTrait, ParamsAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait;

    private $_data = [ ];
    private $_user = null;
    private $_redirect = null;

    protected $allowed_observers = [
        'ListObserver', 'ViewObserver', 'FormObserver', 'ConvertObserver', 'RecycleObserver'
    ];
    protected $observers = [ ];

    public function attach( \SplObserver $observer )
    {
        $this->observers[ ] = $observer;
    }

    public function detach( \SplObserver $observer )
    {
        $key = array_search( $observer, $this->observers );
        if ( $key )
        {
            unset( $this->observers[ $key ] );
        }
    }

    public function notify()
    {
        foreach ( $this->observers as $observer )
        {
            $observer->update( $this );
        }
    }

    public function setRedirect( $redirect )
    {
        $this->_redirect = $redirect;
    }

    public function getRedirect()
    {
        return $this->_redirect;
    }

    public function hasRedirect()
    {
        if ( !empty( $this->_redirect ) )
        {
            return true;
        }

        return false;
    }

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

    public function setData( array $data )
    {
        $this->_data += $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function  init()
    {
        foreach ( $this->getViewConfigDataVerify()->observers as $observer )
        {
            if ( !in_array( $observer, $this->allowed_observers ) )
            {
                throw new \Exception( $observer . ' is not allowed in ' . get_class( $this ) );
            }
            $observerClassName = 'ModelFramework\ModelViewService\Observer\\' . $observer;
            $this->attach( new $observerClassName() );
        }
    }

    public function fields()
    {
        return $this->getViewConfigDataVerify()->fields;
    }

    public function labels()
    {
//        prn( 'ModelView labels', $this->getModelConfigVerify() );
//        prn( 'ModelView fields', $this->getViewConfigDataVerify() );

        return $this->getModelConfigVerify()[ 'labels' ];
    }

    public function setDataFields()
    {
        $viewConfig            = $this->getViewConfigDataVerify();
        $result                = [ ];
        $result[ 'fields' ]    = $this->fields();
        $result[ 'labels' ]    = $this->labels();
        $result[ 'modelname' ] = strtolower( $viewConfig->model );
        $result[ 'table' ]     = [ 'id' => Table::getTableId( $viewConfig->model ) ];
        $result[ 'user' ]      = $this->getUser();
        $result[ 'saurl' ]     = '?back=' . $this->generateLabel();
        $result[ 'saurlback' ] = $this->getSaUrlBack( $this->getParams()->fromQuery( 'back', 'home' ) );
        $result[ 'user' ]      = $this->getUser();

        $this->setData( $result );
    }

    public function getParam( $name, $default = '' )
    {
        $param = $this->getParamsVerify()->fromQuery( $name, $default );
        if ( $param === $default )
        {
            $param = $this->getParamsVerify()->fromRoute( $name, $default );
        }

        return $param;
    }

    public function getAclModelVerify()
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

    public function process()
    {
        $this->setUser( $this->getParams()->getController()->User() );
        $this->checkPermissions();
        $this->setDataFields();
        $this->notify();

        return $this;
    }

    public function getSaUrlBack( $backHash )
    {
        $saUrlBack = $this->getGatewayServiceVerify()->get( 'SaUrl' )->find( array( 'label' => $backHash ) );
        if ( $saUrlBack->count() > 0 )
        {
            $saUrlBack = $saUrlBack->current()->url;
        }
        else
        {
            $saUrlBack = '/';
        }

        return $saUrlBack;
    }

    public function getBackUrl()
    {
        $url   = null;
        $saUrl = $this->getParams()->fromPost( 'saurl', [ ] );
        if ( isset( $saUrl[ 'back' ] ) )
        {
            $url = $this->getSaurlBack( $saUrl[ 'back' ] );
        }

        return $url;
    }

    public function generateLabel()
    {
        $saUrlGateway = $this->getGatewayServiceVerify()->get( 'SaUrl' );
        $saUrl        = $saUrlGateway->model();
        $saUrl->url   = $this->getParams()->getController()->getRequest()->getServer( 'REQUEST_URI' );
        $checkUrl     = $saUrlGateway->findOne( [ 'url' => $saUrl->url ] );
        if ( $checkUrl )
        {
            return $checkUrl->label;
        }
        else
        {
            if ( strlen( $saUrl->url ) )
            {
                $saUrl->label = md5( $saUrl->url );
            }
            $i = 0;
            while ( ++$i < 6 && $saUrlGateway->find( [ 'label' => $saUrl->label ] )->count() )
            {
                $saUrl->label = md5( $saUrl->url . time() . ( rand() * 10000 ) );
            }
            if ( $i >= 6 )
            {
                return '/';
            }
            try
            {
                $saUrlGateway->save( $saUrl );
            }
            catch ( \Exception $ex )
            {
                $saUrl->label = '/';
            }

            return $saUrl->label;
        }
    }

}