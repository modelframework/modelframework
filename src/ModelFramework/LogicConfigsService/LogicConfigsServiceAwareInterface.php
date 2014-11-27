<?php
/**
 * Class LogicConfigsServiceAwareInterface
 * @package ModelFramework\LogicConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicConfigsService;

interface LogicConfigsServiceAwareInterface
{

    /**
     * @param LogicConfigsServiceInterface $logicConfigsService
     *
     * @return $this
     */
    public function setLogicConfigsService( LogicConfigsServiceInterface $logicConfigsService );

    /**
     * @return LogicConfigsServiceInterface
     */
    public function getLogicConfigsService();

    /**
     * @return LogicConfigsServiceInterface
     * @throws \Exception
     */
    public function getLogicConfigsServiceVerify();

}