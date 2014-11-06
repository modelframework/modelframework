<?php
/**
 * Interface DataMappingServiceInterface
 * @package ModelFramework\DataMappingService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataMappingService;

interface DataMappingServiceInterface
{

    /**
     * @param $mappingName
     *
     * @return DataMapping
     * @throws \Exception
     */
    public function getDataMapping( $mappingName );
}