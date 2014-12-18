<?php
/**
 * Class LogicServiceProxyCached
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use Zend\Mvc\Controller\Plugin\Params;

class LogicServiceProxyCached
    implements LogicServiceInterface, LogicServiceAwareInterface, CacheServiceAwareInterface, ParamsAwareInterface
{

    use LogicServiceAwareTrait, CacheServiceAwareTrait;

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $model
     *
     * @return DataLogic
     */
    public function get( $eventName, $model )
    {
        return $this->getCacheService()->getCachedObjMethod( $this->getLogicService(), 'get', [ $eventName, $model ] );
    }

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $model
     *
     * @return DataLogic
     */
    public function trigger( $eventName, $model )
    {
        prn($this->getLogicService(),$eventName,$model);
        return $this->getCacheService()
                    ->getCachedObjMethod( $this->getLogicService(), 'trigger', [ $eventName, $model ] );
    }

    /**
     * @param $event
     *
     * @return mixed
     */
    public function dispatch( $event )
    {
        return $this->getLogicService()->dispatch( $event );
    }

    /**
     * @param Params $params
     *
     * @return $this
     */
    public function setParams( Params $params )
    {
        return $this->getLogicService()->setParams( $params );
    }

    /**
     * @return Params
     */
    public function getParams()
    {
        return $this->getLogicService()->getParams();
    }

    /**
     * @return Params
     * @throws \Exception
     */
    public function getParamsVerify()
    {
        return $this->getLogicService()->getParamsVerify();
    }
} 