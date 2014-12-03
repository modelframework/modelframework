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
use ModelFramework\DataModel\Custom\LogicConfigAwareInterface;
use ModelFramework\DataModel\Custom\LogicConfigAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;

class Logic extends AbstractService
    implements GatewayServiceAwareInterface, ModelConfigParserServiceAwareInterface, LogicConfigAwareInterface, AuthServiceAwareInterface,
               \SplSubject
{

    use ModelServiceAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, LogicConfigAwareTrait,  AuthServiceAwareTrait;

    /**
     * @var array|DataModel|null
     */
    private $_eventObject = null;

    protected $allowed_observers = [
        'FillJoinsObserver', 'ChangerObserver', 'OwnerObserver', 'ConstantObserver', 'ConcatenationObserver', 'DateObserver'
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
            if ( is_numeric( $observer  ) )
            {
                $observer = $obConfig;
                $obConfig = null;
            }
            if ( !in_array( $observer, $this->allowed_observers ) )
            {
                throw new \Exception( $observer . ' is not allowed in ' . get_class( $this ) );
            }
            $observerClassName = 'ModelFramework\LogicService\Observer\\' . $observer;
            $_obs = new $observerClassName();
            if ( !empty( $obConfig ) )
            {
                $_obs -> setConfig( $obConfig );
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
    public  function getEventObject()
    {
        return $this->_eventObject;
    }

    public function process()
    {
        $this->notify();
    }

}