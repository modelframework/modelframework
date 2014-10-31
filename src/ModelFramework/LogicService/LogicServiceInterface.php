<?php

namespace ModelFramework\LogicService;


use ModelFramework\DataModel\DataModelInterface;

interface LogicServiceInterface
{

    /**
     * @param array|DataModelInterface $model
     *
     * @return DataLogic
     */
    public function get( $model );

    /**
     * @param $event
     *
     * @return mixed
     */
    public function dispatch( $event );
}