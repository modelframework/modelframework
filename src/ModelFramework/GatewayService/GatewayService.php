<?php
/**
 * Class GatewayService
 * @package ModelFramework\GatewayService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\GatewayService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Zend\Db\ResultSet\ResultSet;

use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Obj;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class GatewayService extends GatewayServiceRaw
    implements ModelServiceAwareInterface
{

    use ModelServiceAwareTrait;

    /**
     * @param string    $name
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

        return parent::getGateway( $name, $model );
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