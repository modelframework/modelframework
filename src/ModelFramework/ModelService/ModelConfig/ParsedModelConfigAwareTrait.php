<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\ModelService\ModelConfig;

use ModelFramework\Utility\Arr;

trait ParsedModelConfigAwareTrait
{

    private $_parsedModelConfig = null;

    /**
     * @param array $parsedModelConfig
     *
     * @return $this
     */
    public function setParsedModelConfig(array $parsedModelConfig)
    {
        $this->_parsedModelConfig = $parsedModelConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getParsedModelConfig()
    {
        return $this->_parsedModelConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getParsedModelConfigVerify()
    {
        $parsedModelConfig = $this->getParsedModelConfig();
        if ($parsedModelConfig == null || !is_array($parsedModelConfig)) {
            throw new \Exception('ParsedModelConfig is not set in '
                . get_class($this));
        }

        return $this->getParsedModelConfig();
    }

    public function addParsedConfig(array $a)
    {
        return $this->setParsedModelConfig(
            Arr::merge($this->getParsedModelConfig(), $a)
        );
    }
}
