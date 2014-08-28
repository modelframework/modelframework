<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:18 AM
 */

namespace ModelFramework\ModelConfigParserService;

interface ModelConfigParserServiceInterface
{

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getModelConfig( $modelName );
} 