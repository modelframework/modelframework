<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 6:09 PM
 */

namespace ModelFramework\ViewBoxConfigsService;

trait ViewBoxConfigsServiceAwareTrait
{

    private $_ViewBoxConfigsService = null;


    /**
     * @param ViewBoxConfigsServiceInterface $ViewBoxConfigsService
     *
     * @return $this
     */
    public function setViewBoxConfigsService( ViewBoxConfigsServiceInterface $ViewBoxConfigsService )
    {
        $this->_ViewBoxConfigsService = $ViewBoxConfigsService;

        return $this;
    }

    /**
     * @return ViewBoxConfigsServiceInterface
     */
    public function getViewBoxConfigsService()
    {
        return $this->_ViewBoxConfigsService;

    }

    /**
     * @return ViewBoxConfigsServiceInterface
     * @throws \Exception
     */
    public function getViewBoxConfigsServiceVerify()
    {
        $_ViewBoxConfigsService = $this->getViewBoxConfigsService();
        if ( $_ViewBoxConfigsService == null || !$_ViewBoxConfigsService instanceof ViewBoxConfigsServiceInterface )
        {
            throw new \Exception( 'ViewBoxConfigsService does not set in the ViewBoxConfigsServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_ViewBoxConfigsService;
    }

} 