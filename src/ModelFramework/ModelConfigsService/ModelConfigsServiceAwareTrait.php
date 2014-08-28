<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 8:54 PM
 */

namespace ModelFramework\ModelConfigsService;


trait ModelConfigsServiceAwareTrait {

    private $_modelConfigsService = null;
    /**
     * @param ModelConfigsServiceInterface $modelConfigsService
     *
     * @return $this
     */
    public function setModelConfigsService(ModelConfigsServiceInterface $modelConfigsService)
    {
        $this->_modelConfigsService = $modelConfigsService;
    }

    /**
     * @return ModelConfigsServiceInterface
     */
    public function getModelConfigsService()
    {
        return $this->_modelConfigsService;
    }


    /**
     * @return ModelConfigsServiceInterface
     * @throws \Exception
     */
    public function getModelConfigsServiceVerify()
    {
        $_modelConfigsService = $this->getModelConfigsService();
        if ( $_modelConfigsService == null || !$_modelConfigsService instanceof ModelConfigsServiceInterface)
        {
            throw new \Exception( 'ModelConfigsService does not set in the ModelConfigsServiceAware instance of ' .
                                  get_class( $this ) );
        }
        return $_modelConfigsService;
    }

} 