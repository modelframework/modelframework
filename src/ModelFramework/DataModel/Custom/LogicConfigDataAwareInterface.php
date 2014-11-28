<?php
/**
 * Class LogicConfigDataAwareInterface
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

interface LogicConfigDataAwareInterface
{

    /**
     * @param LogicConfigData $logicConfig
     *
     * @return $this
     */
    public function setLogicConfigData( LogicConfigData $logicConfig );

    /**
     * @return LogicConfigData
     */
    public function getLogicConfigData();

    /**
     * @return LogicConfigData
     * @throws \Exception
     */
    public function getLogicConfigDataVerify();
}