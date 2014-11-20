<?php
/**
 * Class ViewConfigsService
 * @package ModelFramework\ViewConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewConfigsService;

use ModelFramework\DataModel\Custom\ViewConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Model\Status;

class ViewConfigsService implements ViewConfigsServiceInterface, GatewayServiceAwareInterface, SystemConfigAwareInterface
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

    protected function getConfigFromDb( $modelName, $viewName )
    {

        $viewConfigData = $this->getGatewayServiceVerify()->getGateway( 'ModelView', new ViewConfigData() )->findOne(
            [ 'model' => $modelName, 'mode' => $viewName ]
        );
        if ( $viewConfigData == null )
        {
            $configArray = Arr::getDoubtField( $this->getConfigPart('custom'), $this->getKeyName( $modelName, $viewName ), null );

            if ( $configArray == null )
            {
                throw new \Exception( ' unknown view config for model ' . $modelName . ' in the view ' . $viewName );
            }
            $viewConfigData = new ViewConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( 'ConfigData', $viewConfigData )->save( $viewConfigData );
        }

        return $viewConfigData;
    }

    /**
     * @param $modelName
     * @param $viewName
     *
     * @return ViewConfigData
     * @throws \Exception
     */
    public function getViewConfigData( $modelName, $viewName )
    {

        $viewConfigData = $this->getConfigFromDb( $modelName, $viewName );
        if ( $viewConfigData == null )
        {
            $viewConfigArray =
                Arr::getDoubtField( $this->getConfigPart('system'), $this->getKeyName( $modelName, $viewName ), null );
            if ( $viewConfigArray !== null )
            {
                $viewConfigData = new ViewConfigData( $viewConfigArray );
            }
            else
            {
                throw new \Exception( 'Unknown view config for ' . $modelName . '.' . $viewName );
            }
        }

        return $viewConfigData;
    }

    public function get( $modelName, $viewName )
    {
        return $this->getViewConfigData( $modelName, $viewName );
    }
}