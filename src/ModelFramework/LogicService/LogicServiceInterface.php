<?php

namespace ModelFramework\LogicService;


use ModelFramework\DataModel\DataModelInterface;

interface LogicServiceInterface
{

    /**
     * @param DataModelInterface $model
     *
     * @return DataLogic
     */
    public function get( DataModelInterface $model );

    /**
     * @param $event
     *
     * @return mixed
     */
    public function dispatch( $event );
}