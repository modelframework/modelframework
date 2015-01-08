<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:44 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

interface AclDataAwareInterface
{
    /**
     * @param DataModelInterface $aclData
     *
     * @return $this
     */
    public function setAclData(DataModelInterface $aclData);

    /**
     * @return DataModelInterface
     */
    public function getAclData();

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataVerify();
}
