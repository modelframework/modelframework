<?php

namespace ModelFramework\ModelViewService;

trait ModelViewServiceAwareTrait
{

    /**
     * @var ModelViewServiceInterface
     */
    private $_modelViewService = null;

    /**
     * @param ModelViewServiceInterface $modelViewService
     *
     * @return $this
     */
    public function setModelViewService( ModelViewServiceInterface $modelViewService )
    {
        $this->_modelViewService = $modelViewService;

    }

    /**
     * @return ModelViewServiceInterface
     */
    public function getModelViewService()
    {
        return $this->_modelViewService;
    }

    /**
     * @return ModelViewServiceInterface
     * @throws \Exception
     */
    public function getModelViewServiceVerify()
    {
        $_modelViewService =  $this->getModelViewService();
        if ( $_modelViewService == null || ! $_modelViewService instanceof ModelViewServiceInterface )
        {
            throw new \Exception( 'ModelViewService does not set in the ModelViewServiceAware instance of ' .
                                  get_class( $this ) );
        }
        return $_modelViewService;
    }
} 