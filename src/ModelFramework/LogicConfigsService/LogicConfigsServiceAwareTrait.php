<?php
/**
 * Class LogicConfigsServiceAwareTrait
 * @package ModelFramework\LogicConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicConfigsService;

trait LogicConfigsServiceAwareTrait
{

    private $_logicConfigsService = null;

    /**
     * @param LogicConfigsServiceInterface $logicConfigsService
     *
     * @return $this
     */
    public function setLogicConfigsService( LogicConfigsServiceInterface $logicConfigsService )
    {
        $this->_logicConfigsService = $logicConfigsService;
    }

    /**
     * @return LogicConfigsServiceInterface
     */
    public function getLogicConfigsService()
    {
        return $this->_logicConfigsService;
    }


    /**
     * @return LogicConfigsServiceInterface
     * @throws \Exception
     */
    public function getLogicConfigsServiceVerify()
    {
        $_logicConfigsService = $this->getLogicConfigsService();
        if ( $_logicConfigsService == null || !$_logicConfigsService instanceof LogicConfigsServiceInterface )
        {
            throw new \Exception( 'LogicConfigsService does not set in the LogicConfigsServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_logicConfigsService;
    }

}