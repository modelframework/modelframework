<?php

namespace ModelFramework\QueryService;


interface QueryServiceAwareInterface {

    /**
     * @param QueryServiceInterface $queryService
     *
     * @return $this
     */
    public function setQueryService( QueryServiceInterface $queryService );

    /**
     * @return QueryServiceInterface
     */
    public function getQueryService();

    /**
     * @return QueryServiceInterface
     * @throws \Exception
     */
    public function getQueryServiceVerify();

}