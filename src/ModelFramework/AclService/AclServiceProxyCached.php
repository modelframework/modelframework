<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:20 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class AclServiceProxyCached implements AclServiceInterface, AclServiceAwareInterface, CacheServiceAwareInterface
{
    use AclServiceAwareTrait, CacheServiceAwareTrait;

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclModel($modelName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getAclServiceVerify(), 'getAclModel', [ $modelName ]);
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclData($modelName)
    {
        return $this->getCacheServiceVerify()
                    ->getCachedObjMethod($this->getAclServiceVerify(), 'getAclData', [ $modelName ]);
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get($modelName)
    {
        return $this->getAclModel($modelName);
    }
}
