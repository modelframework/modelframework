<?php
/**
 * Class LogicConfigDataAwareTrait
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

trait LogicConfigDataAwareTrait
{

    /**
     * @var LogicConfigData
     */
    private $_logicConfig = null;

    /**
     * @param LogicConfigData $logicConfig
     *
     * @return $this
     */
    public function setLogicConfigData( LogicConfigData $logicConfig )
    {
        $this->_logicConfig = $logicConfig;
    }

    /**
     * @return LogicConfigData
     *
     */
    public function getLogicConfigData()
    {
        return $this->_logicConfig;
    }

    /**
     * @return LogicConfigData
     * @throws \Exception
     */
    public function getLogicConfigDataVerify()
    {
        $logicConfig = $this->getLogicConfigData();
        if ( $logicConfig == null || !$logicConfig instanceof LogicConfigData )
        {
            throw new \Exception( 'Logic Config Data does not set set in DataLogic' );
        }

        return $logicConfig;
    }
}