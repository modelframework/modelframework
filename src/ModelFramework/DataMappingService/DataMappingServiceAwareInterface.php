<?php
/**
 * Interface DataMappingServiceAwareInterface
 * @package ModelFramework\DataMappingService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMappingService;

interface DataMappingServiceAwareInterface
{

    /**
     * @param DataMappingServiceInterface $dataMappingService
     *
     * @return $this
     */
    public function setDataMappingService( DataMappingServiceInterface $dataMappingService );

    /**
     * @return DataMappingServiceInterface
     */
    public function getDataMappingService();

    /**
     * @return DataMappingServiceInterface
     * @throws \Exception
     */
    public function getDataMappingServiceVerify();

} 