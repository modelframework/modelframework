<?php
/**
 * Class Logic
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\BaseService\AbstractService;
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicConfig\LogicConfigAwareInterface;
use ModelFramework\LogicService\LogicConfig\LogicConfigAwareTrait;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

class Logic extends AbstractService
    implements GatewayServiceAwareInterface, ModelConfigParserServiceAwareInterface, LogicConfigAwareInterface,
               AuthServiceAwareInterface, ParamsAwareInterface, \SplSubject
{

    use ModelServiceAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, LogicConfigAwareTrait,
        AuthServiceAwareTrait, ParamsAwareTrait;

    /**
     * @var array|DataModel|null
     */
    private $_eventObject = null;

    protected $allowed_observers = [
        'ConcatenationObserver',
        'FillJoinsObserver',
        'ConstantObserver',
        'NewItemObserver',
        'ChangerObserver',
        'ParamsObserver',
        'CleanObserver',
        'SaveObserver',
        'UploadObserver',
        'OwnerObserver',
        'DateObserver',
        'AgeObserver',
        'MainUserObserver',
        'ConditionObserver',
        'RecycleObserver',
        'AclObserver'
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

    public function  init()
    {
        foreach ( $this->getLogicConfigVerify()->observers as $observer => $obConfig )
        {
            if ( is_numeric( $observer ) )
            {
                $observer = $obConfig;
                $obConfig = null;
            }
            if ( !in_array( $observer, $this->allowed_observers ) )
            {
                throw new \Exception( $observer . ' is not allowed in ' . get_class( $this ) );
            }
            $observerClassName = 'ModelFramework\LogicService\Observer\\' . $observer;
            $_obs              = new $observerClassName();
            if ( !empty( $obConfig ) && $_obs instanceof ConfigAwareInterface )
            {
                $_obs->setRootConfig( $obConfig );
            }
            $this->attach( $_obs );
        }
    }

    protected function getRules()
    {
        return $this->getLogicConfigVerify()->rules;
    }

    public function getModelName()
    {
        return $this->getLogicConfigVerify()->model;
    }

    /**
     * @param array|DataModelInterface|null $eventObject
     *
     * @return $this
     */
    public function setEventObject( $eventObject )
    {
        $this->_eventObject = $eventObject;

        return $this;
    }

    /**
     * @return array|DataModel|null
     */
    public function getEventObject()
    {
        return $this->_eventObject;
    }

    public function process()
    {
        $this->notify();
    }

}