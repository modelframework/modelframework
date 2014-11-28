<?php
/**
 * Class ConfigsService
 * @package ModelFramework\ConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ConfigsService;

use ModelFramework\DataModel\Custom\ConfigData;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\DataModel\Custom\LogicConfigData;
use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;
use ModelFramework\Utility\Arr;

class ConfigsService implements ConfigsServiceInterface, GatewayServiceAwareInterface, SystemConfigAwareInterface
{

    use GatewayServiceAwareTrait, SystemConfigAwareTrait;


    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|null
     * @throws \Exception
     */
    protected function getConfigFromDb( $domain, $keyName, DataModelInterface $configObject )
    {
//        $configData = new ConfigData();
        $configData = $this->getGatewayServiceVerify()->get( $configObject->getModelName(), $configObject )
                           ->findOne( [ 'key' => $keyName ] );
        if ( $configData == null )
        {
            $configArray = Arr::getDoubtField( $this->getConfigDomainCustom( $domain ), $keyName, null );
            if ( $configArray == null )
            {
                return null;
//                throw new \Exception( ' unknown config for model ' . $keyName );
            }
            $configData = clone $configObject;
            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( $configData -> getModelName(), $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|ConfigData|DataModelInterface|null
     * @throws \Exception
     */
    public function getConfig( $domain, $keyName, DataModelInterface $configObject )
    {
        $configArray = Arr::getDoubtField( $this->getConfigDomainSystem( $domain ), $keyName, null );

        if ( $configArray == null )
        {
            $configObject = $this->getConfigFromDb( $domain, $keyName, $configObject );
        }
        else
        {
            $configObject->exchangeArray( $configArray );
        }

//        if ( $configObject == null )
//        {
//            return null;
////            throw new \Exception( 'Can\'t find configuration for the ' . $keyName . 'model' );
//        }

        return $configObject;
    }

    /**
     * @param string             $domain
     * @param string             $keyName
     * @param DataModelInterface $configObject
     *
     * @return DataModelInterface|DataModelInterface|null
     */
    public function get( $domain, $keyName, DataModelInterface $configObject )
    {
        return $this->getConfig( $domain, $keyName, $configObject );
    }


    /**
     * @param string $keyName
     *
     * @return Config
     * @throws \Exception
     */
    public function getByObject( $keyName, DataModelInterface $configObject )
    {
        return $this->getConfig( $configObject->getModelName(), $keyName, $configObject );
    }

}