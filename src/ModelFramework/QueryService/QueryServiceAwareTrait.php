<?php

namespace ModelFramework\QueryService;

trait QueryServiceAwareTrait
{

    /**
     * @var QueryServiceInterface
     */
    private $_queryService = null;

    /**
     * @param QueryServiceInterface $queryService
     *
     * @return $this
     */
    public function setQueryService( QueryServiceInterface $queryService )
    {
        $this->_queryService = $queryService;

    }

    /**
     * @return QueryServiceInterface
     */
    public function getQueryService()
    {
        return $this->_queryService;
    }

    /**
     * @return QueryServiceInterface
     * @throws \Exception
     */
    public function getQueryServiceVerify()
    {
        $_queryService =  $this->getQueryService();
        if ( $_queryService == null || ! $_queryService instanceof QueryServiceInterface )
        {
            throw new \Exception( 'QueryService does not set in the QueryServiceAware instance of ' .
                                  get_class( $this ) );
        }
        return $_queryService;
    }
} 