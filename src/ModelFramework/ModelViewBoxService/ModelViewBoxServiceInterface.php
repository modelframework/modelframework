<?php

namespace ModelFramework\ModelViewBoxService;

interface ModelViewBoxServiceInterface
{

    /**
     * @param string $modelName
     * @param        $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function getModelViewBox( $modelName, $viewName );

    /**
     * @param string $modelName
     * @param        $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName );

}