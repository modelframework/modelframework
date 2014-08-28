<?php

namespace ModelFramework\ModelViewService;


interface ModelViewServiceAwareInterface {

    /**
     * @param ModelViewServiceInterface $modelViewService
     *
     * @return $this
     */
    public function setModelViewService( ModelViewServiceInterface $modelViewService );

    /**
     * @return ModelViewServiceInterface
     */
    public function getModelViewService();

    /**
     * @return ModelViewServiceInterface
     * @throws \Exception
     */
    public function getModelViewServiceVerify();

}