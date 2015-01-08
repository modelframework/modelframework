<?php
/**
 * Class AclService
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

class AclService
    implements AclServiceInterface, GatewayServiceAwareInterface, AuthServiceAwareInterface, ModelServiceAwareInterface,
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
            throw new \Exception(' the user does not set in AuthService');
        }

        return $user;
    }

    /**
     * @param string $modelName
     *
     * @return \ModelFramework\GatewayService\MongoGateway|null
     * @throws \Exception
     */
    public function getGateway($modelName)
    {
        $gateway = $this->getGatewayServiceVerify()->get($modelName);
        if ($gateway == null) {
            throw new \Exception($modelName.' Gateway can not be created ');
        }

        return $gateway;
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclData($modelName)
    {
        //        $aclGateway = $this->getGateway( 'Acl' );
        $user       = $this->getUser();
//        prn( 'AclService', $user->role_role );
//        prn( $this );

        $acl = $this->getConfigServiceVerify()->getByObject($modelName.'.'.$user->role_role,
                                                                     new AclConfig());
//        $acl = $aclGateway->findOne( [ 'role_id' => $user->role_id, 'resource' => $modelName ] );


//        prn($aclConfig, $modelName . '.' . $user->role_role, $acl);
        if ($acl == null) {
            throw new \Exception($modelName.' - Acl for role '.$user->role.' not found ');
        }

        return $acl;
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function get($modelName)
    {
        return $this->getAclModel($modelName);
    }

    /**
     * @param $modelName
     *
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getAclModel($modelName)
    {
        $aclData = new AclDataModel();

        $dataModel = $this->getModelServiceVerify()->get($modelName);
        $aclData->setDataModel($dataModel);

        $aclModel = $this->getAclData($modelName);
        $aclData->setAclData($aclModel);

        return $aclData;
    }
}
