<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 15.07.14
 * Time: 20:41
 */

namespace ModelFramework\LogicService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class LogicServiceProxyCached
    implements LogicServiceInterface, LogicServiceAwareInterface, CacheServiceAwareInterface
{

    use LogicServiceAwareTrait, CacheServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     *
     * @return DataLogic
     */
    public function get( DataModelInterface $model )
    {
        return $this->getCacheService()->getCachedObjMethod( $this->getLogicService(), 'get', [ $model ] );
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
} 