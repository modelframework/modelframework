<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

interface ModelConfigAwareInterface
{
    /**
     * @param array $modelConfig
     *
     * @return $this
     */
    public function setModelConfig(array $modelConfig);

    /**
     * @return array
     */
    public function getModelConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getModelConfigVerify();
}
