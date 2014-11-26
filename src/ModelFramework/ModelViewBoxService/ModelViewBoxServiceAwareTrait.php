<?php

namespace ModelFramework\ModelViewBoxService;

trait ModelViewBoxServiceAwareTrait
{

    /**
     * @var ModelViewBoxServiceInterface
     */
    private $_modelViewBoxService = null;

    /**
     * @param ModelViewBoxServiceInterface $modelViewBoxService
     *
     * @return $this
     */
    public function setModelViewBoxService( ModelViewBoxServiceInterface $modelViewBoxService )
    {
        $this->_modelViewBoxService = $modelViewBoxService;

    }

    /**
     * @return ModelViewBoxServiceInterface
     */
    public function getModelViewBoxService()
    {
        return $this->_modelViewBoxService;
    }

    /**
     * @return ModelViewBoxServiceInterface
     * @throws \Exception
     */
    public function getModelViewBoxServiceVerify()
    {
        $_modelViewBoxService =  $this->getModelViewBoxService();
        if ( $_modelViewBoxService == null || ! $_modelViewBoxService instanceof ModelViewBoxServiceInterface )
        {
            throw new \Exception( 'ModelViewBoxService does not set in the ModelViewBoxServiceAware instance of ' .
                                  get_class( $this ) );
        }
        return $_modelViewBoxService;
    }
} 