<?php

namespace ModelFramework\ViewBoxConfigsService;


interface ViewBoxConfigsServiceAwareInterface {

    /**
     * @param ViewBoxConfigsServiceInterface $viewConfigsService
     *
     * @return $this
     */
    public function setViewBoxConfigsService( ViewBoxConfigsServiceInterface $viewConfigsService );

    /**
     * @return ViewBoxConfigsServiceInterface
     */
    public function getViewBoxConfigsService();

    /**
     * @return ViewBoxConfigsServiceInterface
     * @throws \Exception
     */
    public function getViewBoxConfigsServiceVerify();


} 