<?php
/**
 * Class AclService
 *
 * @package ModelFramework\AclService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\AclService;

use ModelFramework\AclService\AclConfig\AclConfig;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Wepo\Model\Role;

class AclService
    implements AclServiceInterface, GatewayServiceAwareInterface,
               AuthServiceAwareInterface, ModelServiceAwareInterface,
               ConfigServiceAwareInterface
{

    use GatewayServiceAwareTrait, AuthServiceAwareTrait, ModelServiceAwareTrait, ConfigServiceAwareTrait;

    /*
     * @return DataModelInterface
     */
    public function getUser()
    {
        $user = $this->getAuthServiceVerify()->getUser();
        if ($user == null) {
            throw new \Exception( 'User is not set in AuthService' );
        }

        return $user;
    }

    /**
     * @param string $modelName
     *
     * @return \ModelFramework\GatewayService\MongoGateway|null
     * @throws \Exception
     */
    public function getGateway( $modelName )
    {
        $gateway = $this->getGatewayServiceVerify()->get( $modelName );
        if ($gateway == null) {
            throw new \Exception( $modelName . ' Gateway can not be created ' );
        }

        return $gateway;
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclData( $modelName )
    {
        $user = $this->getUser();
        $acl  = $this->getConfigServiceVerify()->getByObject( $modelName . '.' .
                                                              $user->role_title,
            new AclConfig() );
        if ($acl == null) {
            if ($user->role_id == Role::GUEST) {
                return new AclConfig();
            }
            throw new \Exception( $modelName . ' acl config for role ' .
                                  $user->role_title . ' not found ' );
        }

        return $acl;
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get( $modelName )
    {
        return $this->getAclModel( $modelName );
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclModel( $modelName )
    {
        $aclModel = new AclDataModel();

        $dataModel = $this->getModelServiceVerify()->get( $modelName );
        $aclModel->setDataModel( $dataModel );

        $aclData = $this->getAclData( $modelName );
        $aclModel->setAclData( $aclData );

        $aclModel->setUser( $this->getUser() );

        return $aclModel;
    }
}
