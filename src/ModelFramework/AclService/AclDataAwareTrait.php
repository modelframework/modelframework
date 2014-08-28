<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/24/14
 * Time: 8:48 PM
 */

namespace ModelFramework\AclService;

use ModelFramework\DataModel\DataModelInterface;

trait AclDataAwareTrait
{

    /**
     * @var DataModelInterface
     */
    private $_aclData = null;

    /**
     * @param DataModelInterface $aclData
     *
     * @return $this
     */
    public function setAclData( DataModelInterface $aclData )
    {
        $this->_aclData = $aclData;

        return $this;
    }

    /**
     * @return DataModelInterface
     */
    public function getAclData()
    {
        return $this->_aclData;
    }

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclDataVerify()
    {
        $_aclData = $this->getAclData();
        if ( $_aclData == null || !$_aclData instanceof DataModelInterface || $_aclData->getModelName() !== 'Acl' )
        {
            throw new \Exception( 'AclData does not set in the AclDataAware instance of ' . get_class( $this ) );
        }

        return $_aclData;

    }
} 