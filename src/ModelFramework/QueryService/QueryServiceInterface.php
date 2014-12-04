<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:26 PM
 */

namespace ModelFramework\QueryService;

interface QueryServiceInterface
{

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function getQuery( $queryName );

    /**
     * @param string $queryName
     *
     * @return Query|QueryInterface
     * @throws \Exception
     */
    public function get( $queryName );

}