<?php

/**
 * Class ViewService
 * @package ModelFramework\ViewService
 */

namespace ModelFramework\QueryService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\QueryService\QueryConfig\QueryConfig;

class QueryService
    implements QueryServiceInterface, ConfigServiceAwareInterface
{

    use ConfigServiceAwareTrait;

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function get( $queryName )
    {
        return $this->getQuery( $queryName );
    }

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function getQuery( $queryName )
    {

        // this object will deal with all view of model stuff
        $query = new Query();

        // we want modelView get to know what to show and how
        $queryConfig = $this->getConfigServiceVerify()->getByObject( $queryName,  new QueryConfig() );

        if ( $queryConfig == null )
        {
            throw new \Exception('Please fill QueryConfig for the ' . $queryName. '. I can\'t get it out');
        }
        $query->setQueryConfig( $queryConfig );

        $query->init();

        return $query;
    }

} 