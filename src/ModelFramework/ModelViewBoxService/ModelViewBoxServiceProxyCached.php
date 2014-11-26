<?php

namespace ModelFramework\ModelViewBoxService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class ModelViewBoxServiceProxyCached
    implements ModelViewBoxServiceInterface, CacheServiceAwareInterface, ModelViewBoxServiceAwareInterface
{

    use CacheServiceAwareTrait, ModelViewBoxServiceAwareTrait;


    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function getModelViewBox( $modelName, $viewName )
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod( $this->getModelViewBoxServiceVerify(), 'getModelViewBox', [ $modelName, $viewName ] );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName )
    {
        return $this->getModelViewBox( $modelName, $viewName );
    }

} 