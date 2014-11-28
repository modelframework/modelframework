<?php
/**
 * Class ConfigsServiceAwareInterface
 * @package ModelFramework\ConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigsService;

interface ConfigsServiceAwareInterface
{

    /**
     * @param ConfigsServiceInterface $configsService
     *
     * @return $this
     */
    public function setConfigsService( ConfigsServiceInterface $configsService );

    /**
     * @return ConfigsServiceInterface
     */
    public function getConfigsService();

    /**
     * @return ConfigsServiceInterface
     * @throws \Exception
     */
    public function getConfigsServiceVerify();

}