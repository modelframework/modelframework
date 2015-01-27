<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class GatewayService extends GatewayServiceRaw
    implements ModelServiceAwareInterface, ModelConfigParserServiceAwareInterface
{
    use ModelServiceAwareTrait, ModelConfigParserServiceAwareTrait;

    /**
     * @param string             $name
     * @param DataModelInterface $model
     * @param array              $modelConfig
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway($name, DataModelInterface $model = null, array $modelConfig = [])
    {
        if ($model == null) {
            $model = $this->getModel($name);
            $modelConfig = $this->getModelConfigParserServiceVerify()->getModelConfig($name);
        }
        $gw = parent::getGateway('', $model);
        $gw->setParsedModelConfig($modelConfig);

        return $gw;
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel($modelName)
    {
        return $this->getModelServiceVerify()->get($modelName);
    }
}
