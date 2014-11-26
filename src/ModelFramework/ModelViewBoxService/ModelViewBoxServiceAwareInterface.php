<?php

namespace ModelFramework\ModelViewBoxService;


interface ModelViewBoxServiceAwareInterface {

    /**
     * @param ModelViewBoxServiceInterface $modelViewService
     *
     * @return $this
     */
    public function setModelViewBoxService( ModelViewBoxServiceInterface $modelViewService );

    /**
     * @return ModelViewBoxServiceInterface
     */
    public function getModelViewBoxService();

    /**
     * @return ModelViewBoxServiceInterface
     * @throws \Exception
     */
    public function getModelViewBoxServiceVerify();

}