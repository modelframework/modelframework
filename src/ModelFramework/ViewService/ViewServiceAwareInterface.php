<?php

namespace ModelFramework\ViewService;


interface ViewServiceAwareInterface {

    /**
     * @param ViewServiceInterface $modelViewService
     *
     * @return $this
     */
    public function setViewService( ViewServiceInterface $modelViewService );

    /**
     * @return ViewServiceInterface
     */
    public function getViewService();

    /**
     * @return ViewServiceInterface
     * @throws \Exception
     */
    public function getViewServiceVerify();

}