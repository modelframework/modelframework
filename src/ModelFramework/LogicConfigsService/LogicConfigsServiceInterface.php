<?php
/**
 * Class LogicConfigsServiceInterface
 * @package ModelFramework\LogicConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicConfigsService;

interface LogicConfigsServiceInterface
{

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function getLogicConfig( $modelName );

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function get( $modelName );

}