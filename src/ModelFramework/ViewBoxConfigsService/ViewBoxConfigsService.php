<?php
/**
 * Class ViewBoxConfigsService
 * @package ModelFramework\ViewBoxConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewBoxConfigsService;

use ModelFramework\DataModel\Custom\ViewBoxConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;
use ModelFramework\Utility\Arr;

class ViewBoxConfigsService
    implements ViewBoxConfigsServiceInterface, GatewayServiceAwareInterface, SystemConfigAwareInterface
{

    use GatewayServiceAwareTrait, SystemConfigAwareTrait;

    /**
     * @var array
     */
    protected $_systemParams = [

        'list' => [
            'rows' => [ 5, 10, 25, 50, 100 ]
        ],

    ];

    protected function getKeyName( $modelName, $viewName )
    {
        return $modelName . '.' . $viewName;
    }

    /**
     * @param string $documentName
     * @param string $viewName
     *
     * @return ViewBoxConfigData|\ModelFramework\DataModel\DataModelInterface
     * @throws \Exception
     */
    protected function getConfigFromDb( $documentName, $viewName )
    {

        $_viewBoxConfigData = new ViewBoxConfigData();

        $viewBoxConfigData = $this->getGatewayServiceVerify()
                                  ->getGateway( $_viewBoxConfigData->getModelName(), $_viewBoxConfigData )
                                  ->findOne( [ 'document' => $documentName, 'mode' => $viewName ] );

        if ( $viewBoxConfigData == null )
        {
            $configArray =
                Arr::getDoubtField( $this->getConfigPart( 'custom' ), $this->getKeyName( $documentName, $viewName ),
                                    null );

            if ( $configArray == null )
            {
                throw new \Exception( ' unknown view config for document ' . $documentName . ' in the view ' .
                                      $viewName );
            }
            $viewBoxConfigData = new ViewBoxConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( $_viewBoxConfigData->getModelName(), $_viewBoxConfigData )->save( $viewBoxConfigData );
        }

        return $viewBoxConfigData;
    }

    /**
     * @param string $documentName
     * @param string $viewName
     *
     * @return ViewBoxConfigData
     * @throws \Exception
     */
    public function getViewBoxConfigData( $documentName, $viewName )
    {

        $ViewBoxConfigData = $this->getConfigFromDb( $documentName, $viewName );
        if ( $ViewBoxConfigData == null )
        {
            $ViewBoxConfigArray =
                Arr::getDoubtField( $this->getConfigPart( 'system' ), $this->getKeyName( $documentName, $viewName ),
                                    null );
            if ( $ViewBoxConfigArray !== null )
            {
                $ViewBoxConfigData = new ViewBoxConfigData( $ViewBoxConfigArray );
            }
            else
            {
                throw new \Exception( 'Unknown view config for ' . $documentName . '.' . $viewName );
            }
        }

        return $ViewBoxConfigData;
    }

    public function get( $documentName, $viewName )
    {
        return $this->getViewBoxConfigData( $documentName, $viewName );
    }
}