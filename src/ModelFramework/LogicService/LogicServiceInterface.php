<?php
/**
 * Class LogicServiceInterface
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\DataModel\DataModelInterface;

interface LogicServiceInterface
{

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $eventObject
     *
     * @return DataLogic
     */
    public function get( $eventName, $eventObject );

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $eventObject
     *
     * @return DataLogic
     */
    public function trigger( $eventName, $eventObject );

}