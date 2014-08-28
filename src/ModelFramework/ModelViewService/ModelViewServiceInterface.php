<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:26 PM
 */

namespace ModelFramework\ModelViewService;

interface ModelViewServiceInterface
{

    /**
     * @param string $modelName
     * @param        $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function getModelView( $modelName, $viewName );

    /**
     * @param string $modelName
     * @param        $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName );

}