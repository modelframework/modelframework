<?php
/**
 * Class FormService
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigsService\ConfigsServiceAwareInterface;
use ModelFramework\ConfigsService\ConfigsServiceAwareTrait;
use ModelFramework\DataModel\Custom\ModelConfig;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormConfigParserService\FormConfigParserServiceAwareInterface;
use ModelFramework\FormConfigParserService\FormConfigParserServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use Wepo\Lib\Acl;

class FormService implements FormServiceInterface, FieldTypesServiceAwareInterface, ConfigsServiceAwareInterface,
                             ModelConfigParserServiceAwareInterface, AclServiceAwareInterface,
                             GatewayServiceAwareInterface, AuthServiceAwareInterface,
                             FormConfigParserServiceAwareInterface
{

    use ModelConfigParserServiceAwareTrait, FieldTypesServiceAwareTrait, ConfigsServiceAwareTrait, AclServiceAwareTrait, GatewayServiceAwareTrait, AuthServiceAwareTrait, FormConfigParserServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     *
     * @return DataForm
     */
    public function get( DataModelInterface $model, $mode )
    {
        return $this->getForm( $model, $mode );
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     *
     * @return DataForm
     */
    public function getForm( DataModelInterface $model, $mode )
    {
        return $this->createForm( $model, $mode );
    }


    /**
     * @param DataModelInterface $model
     * @param        string      $mode
     *
     * @return DataFormInterface
     */
    public function createForm( DataModelInterface $model, $mode )
    {
        $configData = $this->getPermittedConfig( $model, $mode );
        $cf         = $this->getFormConfigParserServiceVerify()->getFormConfig( $configData );
        $form       = new DataForm();

        return $form->parseconfig( $cf );
    }


    public function getPermittedConfig( $model, $mode )
    {
        $fieldPermissions = $this->getFieldPermissions( $model, $mode );

        $cd = $this->getConfigsServiceVerify()->get( 'ModelConfig', $model->getModelName(), new ModelConfig() );
//        $cd = $this->getModelConfigsServiceVerify()->get( $model->getModelName() );

        $allowedFields = [ ];
        foreach ( $cd->fields as $k => $v )
        {
            if ( in_array( $k, $fieldPermissions ) )
            {
                $allowedFields[ $k ] = $v;
            }
        }
        $cd->fields = $allowedFields;

        return $cd;
    }

    public function getFieldPermissions( $model, $mode )
    {
        $user = $this->getAuthServiceVerify()->getUser();
        $acl  = $model->getAclData();
        if ( $acl )
        {
            $modelPermissions = $acl->permissions;
            $groups           = $user->groups;
            $groups[ ]        = $user->_id;
            if ( is_array( $model->acl ) )
            {
                foreach ( $groups as $group_id )
                {
                    foreach ( $model->acl as $_acl )
                    {
                        if ( !empty( $_acl[ 'role_id' ] ) && $_acl[ 'role_id' ] == $group_id )
                        {
                            $modelPermissions = array_merge( $modelPermissions, $_acl[ 'permissions' ] );
                        }
                    }
                }
            }
            $modelPermissions = array_unique( $modelPermissions );
            if ( !in_array( $mode, $modelPermissions ) )
            {
                throw new \Exception( "This action is not allowed for you" );
            }
            $fieldPermissions = [ ];
            $fieldModes       = Acl::getFieldPerms( $mode );
            foreach ( $acl->fields as $k => $v )
            {
                if ( in_array( $v, $fieldModes ) )
                {
                    $fieldPermissions[ ] = $k;
                }
            }
        }
        else
        {
            throw new \Exception( "Incorrect acl data is in your account" );
        }

        return $fieldPermissions;
    }

}