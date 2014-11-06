<?php
/**
 * Class DataMappingService
 * @package ModelFramework\DataSchemaService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMappingService;

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
    protected $_dbConfig = [
        'Lead' => [
            'name'    => 'Lead',
            'source'  => 'Lead',
            'targets' => [
                'Contact' => [
                    'title'         => 'title',
                    'owner_login'   => 'owner_login',
                    'owner_id'      => 'owner_id',
                    'fname'         => 'fname',
                    'lname'         => 'lname',
                    'phone'         => 'phone',
                    'mobile'        => 'mobile',
                    'email'         => 'email',
                    'birth_date'    => 'birth_date',
                    'changer_login' => 'changer_login',
                    'changer_id'    => 'changer_id',
                    'changed_dtm'   => 'changed_dtm',
                    'created_dtm'   => 'created_dtm',
                    'status_status' => 'status_status',
                    'status_id'     => 'status_id'
                ],
                'Account' => [
                    'title'         => 'title',
                    'owner_login'   => 'owner_login',
                    'owner_id'      => 'owner_id',
                    'fname'         => 'fname',
                    'lname'         => 'lname',
                    'phone'         => 'phone',
                    'mobile'        => 'mobile',
                    'email'         => 'email',
                    'birth_date'    => 'birth_date',
                    'changer_login' => 'changer_login',
                    'changer_id'    => 'changer_id',
                    'changed_dtm'   => 'changed_dtm',
                    'created_dtm'   => 'created_dtm',
                    'status_status' => 'status_status',
                    'status_id'     => 'status_id'
                ]
            ]
        ]
    ];

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