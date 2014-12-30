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
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\QueryService\QueryServiceAwareInterface;

class FormServiceProxyCached implements FormServiceAwareInterface, CacheServiceAwareInterface, FormServiceInterface
{

    use CacheServiceAwareTrait, FormServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     *
     * @return DataForm
     */
    public function get( DataModelInterface $model, $mode )
    {
        return $this->getForm( $model, $mode );
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     *
     * @return DataForm
     */
    public function getForm( DataModelInterface $model, $mode )
    {
        return $this->getCacheService()->getCachedObjMethod( $this->getFormService(), 'getForm', [ $model, $mode ] );
    }

}