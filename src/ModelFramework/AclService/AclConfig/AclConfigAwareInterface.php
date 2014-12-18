<?php
/**
 * Class AclConfigAwareInterface
 * @package namespace ModelFramework\AclService\AclConfig
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\AclService\AclConfig;

interface AclConfigAwareInterface
{

    /**
     * @param AclConfig $aclConfig
     *
     * @return $this
     */
    public function setAclData( AclConfig $aclConfig );

    /**
     * @return AclConfig
     */
    public function getAclData();

    /**
     * @return AclConfig
     * @throws \Exception
     */
    public function getAclDataVerify();
}