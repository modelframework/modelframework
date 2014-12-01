<?php
/**
 * Class DataLogic
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\BaseService\AbstractService;
use ModelFramework\DataModel\Custom\LogicConfigAwareInterface;
use ModelFramework\DataModel\Custom\LogicConfigAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;

class DataLogic extends AbstractService
    implements GatewayServiceAwareInterface, ModelConfigParserServiceAwareInterface, LogicConfigAwareInterface,
               \SplSubject
{

    use ModelServiceAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, LogicConfigAwareTrait;

    private $_event = null;

    protected $allowed_observers = [
        'FillJoinsObserver', 'ChangerObserver'
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

    protected function getRules()
    {
        $this->getLogicConfigDataVerify()->rules;
    }

    protected function getModelName()
    {
        $this->getLogicConfigDataVerify()->getModelName();
    }

    protected function setEvent( $event )
    {
        $this->_event = $event;

        return $this;
    }

    protected function getEvent()
    {
        return $this->_event;
    }

    public function process()
    {
        prn('Starting Process');
    }

}