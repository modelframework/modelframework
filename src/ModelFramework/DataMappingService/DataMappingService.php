<?php
/**
 * Class DataMappingService
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMappingService;

use ModelFramework\DataModel\Custom\DataMapping;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Arr;

class DataMappingService implements DataMappingServiceInterface, GatewayServiceAwareInterface
{

    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_dataSchemas = [ ];

    /**
     * @var array
     */
    protected $_dbConfig = [ ];

    /**
     * @param array $systemConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setSystemConfig( $systemConfig )
    {
        if ( !is_array( $systemConfig ) )
        {
            throw new \Exception( 'SystemConfig must be an array' );
        }
        $this->_dbConfig = $systemConfig;

        return $this;
    }

    protected function getConfigFromDb( $mappingName )
    {

        $dataSchema = $this->getGatewayServiceVerify()->getGateway( 'DataMapping', new DataMapping() )->findOne(
            [ 'name' => $mappingName ]
        );
        if ( $dataSchema == null )
        {
            $configArray = Arr::getDoubtField( $this->_dbConfig, $mappingName, null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown config for the mapping ' . $mappingName );
            }
//            prn( 'DataMAppingService', $configArray );
            $dataMapping = new DataMapping( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( 'ConfigData', $viewConfigData )->save( $viewConfigData );
        }

        return $dataMapping;
    }

    /**
     * @param $mappingName
     *
     * @return DataMapping
     * @throws \Exception
     */
    public function getDataMapping( $mappingName )
    {

        $dataMapping = $this->getConfigFromDb( $mappingName );

        return $dataMapping;
    }

    public function get( $mappingName )
    {
        return $this->getDataMapping( $mappingName );
    }
}