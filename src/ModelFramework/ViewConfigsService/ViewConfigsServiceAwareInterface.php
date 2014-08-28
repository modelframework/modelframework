<?php

namespace ModelFramework\ViewConfigsService;


interface ViewConfigsServiceAwareInterface {

    /**
     * @param ViewConfigsServiceInterface $viewConfigsService
     *
     * @return $this
     */
    public function setViewConfigsService( ViewConfigsServiceInterface $viewConfigsService );

    /**
     * @return ViewConfigsServiceInterface
     */
    public function getViewConfigsService();

    /**
     * @return ViewConfigsServiceInterface
     * @throws \Exception
     */
    public function getViewConfigsServiceVerify();


} 