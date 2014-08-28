<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 7:03 PM
 */

namespace ModelFramework\ModelViewService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class ModelViewServiceProxyCached
    implements ModelViewServiceInterface, CacheServiceAwareInterface, ModelViewServiceAwareInterface
{

    use CacheServiceAwareTrait, ModelViewServiceAwareTrait;


    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function getModelView( $modelName, $viewName )
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod( $this->getModelViewServiceVerify(), 'getModelView', [ $modelName, $viewName ] );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName )
    {
        return $this->getModelView( $modelName, $viewName );
    }

} 