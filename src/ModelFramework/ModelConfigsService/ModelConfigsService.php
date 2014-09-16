<?php

namespace ModelFramework\ModelConfigsService;

use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\DataModel\Custom\ConfigData;
use ModelFramework\Utility\Arr;

/**
 * Class ModelConfigsService
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelConfigsService implements ModelConfigsServiceInterface, GatewayServiceAwareInterface
{

    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_systemConfig = [ ];

    /**
     * @var array
     */
    protected $_customConfig = [ ];

    public function setSystemConfig( $systemConfig )
    {
        $this->_systemConfig = isset( $systemConfig[ 'system' ] ) ? $systemConfig[ 'system' ] : [ ];
        $this->_customConfig = isset( $systemConfig[ 'custom' ] ) ? $systemConfig[ 'custom' ] : [ ];
    }

    protected function getConfigFromDb( $modelName )
    {
        $configData = $this->getGatewayServiceVerify()->get( 'ConfigData', new ConfigData() )
                           ->findOne( [ 'model' => $modelName ] );
        if ( $configData == null )
        {
            $configArray = Arr::getDoubtField( $this->_customConfig, $modelName, null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown config for model ' . $modelName );
            }
            $configData = new ConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
            $this->getGatewayServiceVerify()->get( 'ConfigData', $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function getModelConfig( $modelName )
    {
        $configArray = Arr::getDoubtField( $this->_systemConfig, $modelName, null );

        if ( $configArray == null )
        {
            $configData = $this->getConfigFromDb( $modelName );
        }
        else
        {
            $configData = new ConfigData();
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
        return $this->getModelConfig( $modelName );
    }

}