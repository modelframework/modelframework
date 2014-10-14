<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 5:09 PM
 */

namespace ModelFramework\ViewConfigsService;

use ModelFramework\DataModel\Custom\ViewConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Model\Status;

class ViewConfigsService implements ViewConfigsServiceInterface, GatewayServiceAwareInterface
{

    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_viewConfig = [

    ];

    /**
     * @var array
     */
    protected $_systemConfig = [

        'list' => [
            'rows' => [ 5, 10, 25, 50, 100 ]
        ],

    ];

    /**
     * @var array
     */
    protected $_dbConfig = [

        'Lead.list' => [
            'observers' => ['ListObserver'],
            'name'   => 'Lead.list',
            'custom' => 0,
            'model'  => 'Lead',
            'mode'   => 'list',
            'query'  => [
                'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
            ],
            'fields' => [
                'owner_login',
                'fname',
                'lname',
                'phone',
                'mobile',
                'email',
                'birth_date',
                'changer_login',
                'changed_dtm',
                'created_dtm',
                'status_status'
            ],
            'params' => [ 'rows'=>10, 'sort'=>'created_dtm', 'desc'=>1, 'q' => '' ],
            'groups' => [ ],
            'rows' => 10,
        ],
        'Lead.view' => [
            'observers' => ['ViewObserver'],
            'name'   => 'Lead.view',
            'custom' => 0,
            'model'  => 'Lead',
            'mode'   => 'view',
            'query'  => [
                'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
            ],
            'fields' => [
                'owner_login',
                'fname',
                'lname',
                'phone',
                'mobile',
                'email',
                'birth_date',
                'changer_login',
                'changed_dtm',
                'created_dtm',
                'status_status'
            ],
            'params' => [ 'rows'=>10, 'sort'=>'created_dtm', 'desc'=>1, 'q' => '' ],
            'groups' => [ ],
            'rows' => 10,
        ]

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
            $configArray = Arr::getDoubtField( $this->_dbConfig, $this->getKeyName( $modelName, $viewName ), null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown config for model ' . $modelName . ' view ' . $viewName );
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
                Arr::getDoubtField( $this->_viewConfig, $this->getKeyName( $modelName, $viewName ), null );
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