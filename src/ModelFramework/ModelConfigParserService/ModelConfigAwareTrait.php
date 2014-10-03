<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 8/1/14
 * Time: 12:53 PM
 */

namespace ModelFramework\ModelConfigParserService;

trait ModelConfigAwareTrait
{

    /**
     * @var array
     */
    private $_modelConfig = null;

    /**
     * @param array $modelConfig
     *
     * @return $this
     */
    public function setModelConfig( array $modelConfig )
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
        if ( $modelConfig == null || !is_array( $modelConfig ) )
        {
            throw new \Exception( 'ModelConfig does not set in the GatewayAware instance of ' . get_class( $this ) );
        }

        return $modelConfig;
    }
} 