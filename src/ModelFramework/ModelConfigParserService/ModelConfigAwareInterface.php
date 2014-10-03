<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:18 AM
 */

namespace ModelFramework\ModelConfigParserService;

interface ModelConfigAwareInterface
{

    /**
     * @param array $modelConfig
     *
     * @return mixed
     */
    public function setModelConfig( array $modelConfig );

    /**
     * @return array
     */
    public function getModelConfig();

    /**
     * @return array
     */
    public function getModelConfigVerify();
}