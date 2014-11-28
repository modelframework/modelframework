<?php

namespace ModelFramework\LogicService;


use ModelFramework\DataModel\DataModelInterface;

interface LogicServiceInterface
{

    /**
     * @param string $eventName
     * @param array|DataModelInterface $model
     *
     * @return DataLogic
     */
    public function get( $eventName, $model );

    /**
     * @param string $eventName
     * @param array|DataModelInterface $model
     *
     * @return DataLogic
     */
    public function trigger( $eventName, $model );

}