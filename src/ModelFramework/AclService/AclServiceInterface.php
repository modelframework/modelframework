<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 5:51 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

interface AclServiceInterface
{

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclModel( $modelName );

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get( $modelName );

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclData( $modelName );

} 