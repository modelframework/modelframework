<?php
/**
 * Class LogicConfigAwareTrait
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

trait LogicConfigAwareTrait
{

    /**
     * @var LogicConfig
     */
    private $_logicConfig = null;

    /**
     * @param LogicConfig $logicConfig
     *
     * @return $this
     */
    public function setLogicConfig( LogicConfig $logicConfig )
    {
        $this->_logicConfig = $logicConfig;
    }

    /**
     * @return LogicConfig
     *
     */
    public function getLogicConfig()
    {
        return $this->_logicConfig;
    }

    /**
     * @return LogicConfig
     * @throws \Exception
     */
    public function getLogicConfigVerify()
    {
        $logicConfig = $this->getLogicConfig();
        if ( $logicConfig == null || !$logicConfig instanceof LogicConfig )
        {
            throw new \Exception( 'Logic Config Data does not set set in DataLogic' );
        }

        return $logicConfig;
    }
}