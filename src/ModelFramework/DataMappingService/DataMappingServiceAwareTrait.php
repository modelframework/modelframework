<?php
/**
 * Trait DataMappingServiceAwareTrait
 * @package ModelFramework\DataMappingService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMappingService;

trait DataMappingServiceAwareTrait
{

    private $_dataMappingService = null;


    /**
     * @param DataMappingServiceInterface $dataMappingService
     *
     * @return $this
     */
    public function setDataSchemaService( DataMappingServiceInterface $dataMappingService )
    {
        $this->_dataSchemaService = $dataMappingService;

        return $this;
    }

    /**
     * @return DataMappingServiceInterface
     */
    public function getDataMappingService()
    {
        return $this->_dataMappingService;

    }

    /**
     * @return DataMappingServiceInterface
     * @throws \Exception
     */
    public function getDataMappingServiceVerify()
    {
        $_dataMappingService = $this->getDataMappingService();
        if ( $_dataMappingService == null || !$_dataMappingService instanceof DataMappingServiceInterface )
        {
            throw new \Exception( 'DataMappingService does not set in the DataMappingServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_dataMappingService;
    }

} 