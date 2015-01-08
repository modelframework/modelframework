<?php
/**
 * Class FormConfigParserServiceProxyCached
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class FormConfigParserServiceProxyCached
    implements FormConfigParserServiceAwareInterface, CacheServiceAwareInterface, FormConfigParserServiceInterface
{
    use CacheServiceAwareTrait, FormConfigParserServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function get($modelName)
    {
        return $this->getFormConfig($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getFormConfig($modelName)
    {
        return $this->getCacheService()->getCachedObjMethod($this->getFormConfigParserServiceVerify(), 'getFormConfig', [ $modelName ]);
    }
}
