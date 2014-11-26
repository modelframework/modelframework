<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 6:05 PM
 */

namespace ModelFramework\ViewBoxConfigsService;

interface ViewBoxConfigsServiceInterface
{

    /**
     * @param $modelName
     * @param $viewName
     *
     * @return ViewBoxConfigData
     * @throws \Exception
     */
    public function getViewBoxConfigData( $modelName, $viewName );
}