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
use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;

class DataMappingService implements DataMappingServiceInterface, GatewayServiceAwareInterface, SystemConfigAwareInterface
{

    use GatewayServiceAwareTrait, SystemConfigAwareTrait;

    protected function getConfigFromDb( $mappingName )
    {

        $dataSchema = $this->getGatewayServiceVerify()->getGateway( 'DataMapping', new DataMapping() )->findOne(
            [ 'name' => $mappingName ]
        );
        if ( $dataSchema == null )
        {
            $configArray = Arr::getDoubtField( $this->getSystemConfigVerify(), $mappingName, null );
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