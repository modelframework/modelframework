<?php
/**
 * Class LogicConfigsService
 * @package ModelFramework\LogicConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicConfigsService;

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
     * @param string $modelName
     *
     * @return ConfigData|\ModelFramework\DataModel\DataModelInterface
     * @throws \Exception
     */
    protected function getConfigFromDb( $modelName )
    {
        $configData = $this->getGatewayServiceVerify()->get( 'LogicData', new LogicConfigData() )
                           ->findOne( [ 'model' => $modelName ] );
        if ( $configData == null )
        {
            $configArray = Arr::getDoubtField( $this->getCustomLogicConfig(), $modelName, null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown config for model ' . $modelName );
            }
            $configData = new LogicConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( 'ConfigData', $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function getLogicConfig( $modelName )
    {
        $configArray = Arr::getDoubtField( $this->getSystemLogicConfig(), $modelName, null );

        if ( $configArray == null )
        {
            $configData = $this->getConfigFromDb( $modelName );
        }
        else
        {
            $configData = new LogicConfigData();
            $configData->exchangeArray( $configArray );
        }

        if ( $configData == null )
        {
            throw new \Exception( 'Can\'t find configuration for the ' . $modelName . 'model' );
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