<?php
/**
 * Class FormServiceProxyCached
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;

class FormServiceProxyCached implements FormServiceAwareInterface, CacheServiceAwareInterface, FormServiceInterface
{

    use CacheServiceAwareTrait, FormServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function get( $modelName )
    {
        return $this->getForm( $modelName );
    }

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function getForm( $modelName )
    {
        return $this->getCacheService()->getCachedObjMethod( $this->getFormService(), 'getForm', [ $modelName ] );
    }

}