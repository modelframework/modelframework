<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\DataModel\Custom;


trait ModelConfigAwareTrait {

    private $_modelConfig = null;

    /**
     * @param array $modelConfig
     *
     * @return $this
     */
    public function setModelConfig(array $modelConfig )
    {
        $this->_modelConfig = $modelConfig;
        return $this;
    }

    /**
     * @return array
     */
    public function getModelConfig()
    {
        return $this->_modelConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getModelConfigVerify()
    {
        $modelConfig = $this->getModelConfig();
        if ( $modelConfig == null || !is_array($modelConfig))
        {
            throw new \Exception('ModelConfig does not set in ModelView');
        }
        return $this->getModelConfig();
    }
} 