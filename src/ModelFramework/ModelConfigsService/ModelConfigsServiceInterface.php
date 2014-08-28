<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 8:53 PM
 */

namespace ModelFramework\ModelConfigsService;


interface ModelConfigsServiceInterface {

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function getModelConfig( $modelName );

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function get( $modelName );

}