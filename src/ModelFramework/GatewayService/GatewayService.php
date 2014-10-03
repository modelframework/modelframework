<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class GatewayService extends GatewayServiceRaw
    implements ModelServiceAwareInterface, ModelConfigParserServiceAwareInterface
{

    use ModelServiceAwareTrait, ModelConfigParserServiceAwareTrait;

    /**
     * @param string             $name
     * @param DataModelInterface $model
     *
     * @return null|MongoGateway
     * @throws \Exception
     */
    public function getGateway( $name, DataModelInterface $model = null )
    {
        if ( $model == null )
        {
            $model = $this->getModel( $name );
        }
        $gw = parent::getGateway( $name, $model );
        $gw->setModelConfig( $this->getModelConfigParserServiceVerify()->getModelConfig( $name ) );

        return $gw;
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel( $modelName )
    {
        return $this->getModelServiceVerify()->get( $modelName );
    }

}