<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 6:09 PM
 */

namespace ModelFramework\ViewConfigsService;

trait ViewConfigsServiceAwareTrait
{

    private $_viewConfigsService = null;


    /**
     * @param ViewConfigsServiceInterface $viewConfigsService
     *
     * @return $this
     */
    public function setViewConfigsService( ViewConfigsServiceInterface $viewConfigsService )
    {
        $this->_viewConfigsService = $viewConfigsService;

        return $this;
    }

    /**
     * @return ViewConfigsServiceInterface
     */
    public function getViewConfigsService()
    {
        return $this->_viewConfigsService;

    }

    /**
     * @return ViewConfigsServiceInterface
     * @throws \Exception
     */
    public function getViewConfigsServiceVerify()
    {
        $_viewConfigsService = $this->getViewConfigsService();
        if ( $_viewConfigsService == null || !$_viewConfigsService instanceof ViewConfigsServiceInterface )
        {
            throw new \Exception( 'ViewConfigsService does not set in the ViewConfigsServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_viewConfigsService;
    }

} 