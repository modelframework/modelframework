<?php
/**
 * Class LogicConfigsService
 * @package ModelFramework\LogicConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicConfigsService;

use ModelFramework\DataModel\Custom\ConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\DataModel\Custom\LogicConfigData;
use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;
use ModelFramework\Utility\Arr;

class LogicConfigsService implements LogicConfigsServiceInterface, GatewayServiceAwareInterface, SystemConfigAwareInterface
{

    use GatewayServiceAwareTrait, SystemConfigAwareTrait;


    /**
     * @return array
     */
    public function getSystemLogicConfig()
    {
        return $this->getConfigPart( 'system' );
    }

    /**
     * @return array
     */
    public function getCustomLogicConfig()
    {
        return $this->getConfigPart( 'custom' );
    }

    /**
     * @param string $keyName
     *
     * @return null|ConfigData|\ModelFramework\DataModel\DataModelInterface
     * @throws \Exception
     */
    protected function getConfigFromDb( $keyName )
    {
        $configData =  new LogicConfigData();
        $configData = $this->getGatewayServiceVerify()->get( $configData -> getModelName(), $configData )
                           ->findOne( [ 'key' => $keyName ] );
        if ( $configData == null )
        {
            $configArray = Arr::getDoubtField( $this->getCustomLogicConfig(), $keyName, null );
            if ( $configArray == null )
            {
                return null;
//                throw new \Exception( ' unknown config for model ' . $keyName );
            }
//            $configData = new LogicConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( $configData -> getModelName(), $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param string $keyName
     *
     * @return ConfigData
     * @throws \Exception
     */
    public function getLogicConfig( $keyName )
    {
        $configArray = Arr::getDoubtField( $this->getSystemLogicConfig(), $keyName, null );

        if ( $configArray == null )
        {
            $configData = $this->getConfigFromDb( $keyName );
        }
        else
        {
            $configData = new LogicConfigData();
            $configData->exchangeArray( $configArray );
        }

        if ( $configData == null )
        {
            return null;
//            throw new \Exception( 'Can\'t find configuration for the ' . $keyName . 'model' );
        }

        return $configData;
    }

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function get( $modelName )
    {
        return $this->getLogicConfig( $modelName );
    }

}