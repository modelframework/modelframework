<?php
/**
 * Class ConfigsServiceAwareTrait
 * @package ModelFramework\ConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigsService;

trait ConfigsServiceAwareTrait
{

    private $_configsService = null;

    /**
     * @param ConfigsServiceInterface $configsService
     *
     * @return $this
     */
    public function setConfigsService( ConfigsServiceInterface $configsService )
    {
        $this->_configsService = $configsService;
    }

    /**
     * @return ConfigsServiceInterface
     */
    public function getConfigsService()
    {
        return $this->_configsService;
    }


    /**
     * @return ConfigsServiceInterface
     * @throws \Exception
     */
    public function getConfigsServiceVerify()
    {
        $_configsService = $this->getConfigsService();
        if ( $_configsService == null || !$_configsService instanceof ConfigsServiceInterface )
        {
            throw new \Exception( 'ConfigsService does not set in the ConfigsServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_configsService;
    }

}