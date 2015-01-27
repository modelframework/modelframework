<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

interface ParsedModelConfigAwareInterface
{
    /**
     * @param array $parsedModelConfig
     *
     * @return $this
     */
    public function setParsedModelConfig(array $parsedModelConfig);

    /**
     * @return array
     */
    public function getParsedModelConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getParsedModelConfigVerify();
}
