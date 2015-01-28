<?php

namespace ModelFramework\ModelService;

use ModelFramework\DataModel\DataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;

/**
 * Class ModelService
 *
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelService
    implements ModelServiceInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface
{

    use ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function get($modelName)
    {
        return $this->getModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel($modelName)
    {
        return $this->createModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    protected function createModel($modelName)
    {
        $parsedModelConfig = $this->getModelConfigParserServiceVerify()
            ->getModelConfig($modelName);
        $model             = new DataModel();
        $model->setParsedModelConfig($parsedModelConfig);
        return $model;
    }

    /**
     * @param string $model
     *
     * @return bool
     * @throws \Exception
     */
    public function makeIndexes($model)
    {
        if ($this->getGatewayService() === null) {
            return false;
        }
        $indexes = $this->getModelConfigParserServiceVerify()
            ->getAvailableIndexes($model);
        $this->getGatewayServiceVerify()->get($model)->createIndexes($indexes);
        return [];
    }
}
