<?php
/**
 * Class View
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\AclDataModel;
use ModelFramework\DataModel\Custom\ModelConfigAwareInterface;
use ModelFramework\DataModel\Custom\ModelConfigAwareTrait;
use ModelFramework\DataModel\Custom\ViewConfigAwareInterface;
use ModelFramework\DataModel\Custom\ViewConfigAwareTrait;
use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\FormService\FormServiceAwareTrait;
use Wepo\Model\Table;
use Zend\View\Model\ViewModel as ZendViewModel;

class View
    implements ViewInterface, ViewConfigAwareInterface, ModelConfigAwareInterface,
               ModelConfigParserServiceAwareInterface, ModelServiceAwareInterface, GatewayAwareInterface,
               ParamsAwareInterface, GatewayServiceAwareInterface, FormServiceAwareInterface,
               ConfigServiceAwareInterface,
               AuthServiceAwareInterface, LogicServiceAwareInterface, \SplSubject
{

    use ViewConfigAwareTrait, ModelConfigAwareTrait, ConfigServiceAwareTrait, GatewayAwareTrait, ParamsAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait;

    private $_data = [ ];
    private $_redirect = null;

    protected $allowed_observers = [
        'RowCountObserver', 'ListObserver', 'ViewObserver', 'FormObserver', 'ConvertObserver', 'RecycleObserver', 'UserObserver',
        'WidgetObserver', 'Widget1Observer'
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
        return $this->getAuthServiceVerify()->getUser();
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setData( array $data )
    {
        $this->_data = \Zend\Stdlib\ArrayUtils::merge( $this->_data, $data );
//        $this->_data += $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function  init()
    {
        foreach ( $this->getViewConfigVerify()->observers as $observer )
        {
            if ( !in_array( $observer, $this->allowed_observers ) )
            {
                throw new \Exception( $observer . ' is not allowed in ' . get_class( $this ) );
            }
            $observerClassName = 'ModelFramework\ViewService\Observer\\' . $observer;
            $this->attach( new $observerClassName() );
        }
    }

    public function fields()
    {
        return $this->getViewConfigVerify()->fields;
    }

    public function labels()
    {
        return $this->getModelConfigVerify()[ 'labels' ];
    }

    public function setDataFields()
    {
        $viewConfig            = $this->getViewConfigVerify();


        $result                = $viewConfig -> toArray( );

        $result[ 'labels' ]    = $this->labels();
        $result[ 'modelname' ] = strtolower( $viewConfig->model );
        $result[ 'table' ]     = [ 'id' => Table::getTableId( $viewConfig->model ) ];
        $result[ 'user' ]      = $this->getUser();
        $result[ 'saurlhash' ] = $this->generateLabel();
        $result[ 'saurl' ]     = '?back=' . $result[ 'saurlhash' ];
        $result[ 'saurlback' ] = $this->getSaUrlBack( $this->getParams()->fromQuery( 'back', 'home' ) );

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

    public function refresh( $message = null, $toUrl = null, $seconds = 0 )
    {
        $viewModel = new ZendViewModel( array(
                                        'message' => $message, 'user' => $this->getUser(), 'toUrl' => $toUrl,
                                        'seconds' => $seconds
                                    ) );

        return $viewModel->setTemplate( 'wepo/partial/refresh.twig' );
    }

}