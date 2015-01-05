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
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormConfigParserService\FormConfigParserServiceAwareInterface;
use ModelFramework\FormConfigParserService\FormConfigParserServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use Wepo\Lib\Acl;

class FormService implements FormServiceInterface, FieldTypesServiceAwareInterface, ConfigServiceAwareInterface,
                             ModelConfigParserServiceAwareInterface, AclServiceAwareInterface,
                             GatewayServiceAwareInterface, AuthServiceAwareInterface,
                             FormConfigParserServiceAwareInterface
{

    use ModelConfigParserServiceAwareTrait, FieldTypesServiceAwareTrait, ConfigServiceAwareTrait, AclServiceAwareTrait, GatewayServiceAwareTrait, AuthServiceAwareTrait, FormConfigParserServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function get( DataModelInterface $model, $mode, array $fields = [ ] )
    {
        return $this->getForm( $model, $mode, $fields );
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function getForm( DataModelInterface $model, $mode, array $fields = [ ] )
    {
        return $this->createForm( $model, $mode, $fields );
    }


    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function createForm( DataModelInterface $model, $mode, array $fields = [ ] )
    {
        $configData = $this->getPermittedConfig( $model, $mode );

        if ( count($fields) )
        {
            $configFields = $configData->fields;
            $configData->fields = [];
            foreach ( $fields as $fieldName )
            {
                if ( isset( $configFields[$fieldName]))
                {
                    $configData->fields[$fieldName] = $configFields[$fieldName];
                }
            }
        }

        $cf         = $this->getFormConfigParserServiceVerify()->getFormConfig( $configData );
        $form       = new DataForm();

        return $form->parseconfig( $cf );
    }


    /**
     * @param $model
     * @param $mode
     *
     * @return DataModelInterface|null
     * @throws \Exception
     */
    public function getPermittedConfig( $model, $mode )
    {
        $fieldPermissions = $this->getFieldPermissions( $model, $mode );

        $cd = $this->getConfigServiceVerify()->getByObject( $model->getModelName(), new ModelConfig() );
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

    /**
     * @param $model
     * @param $mode
     *
     * @return array
     * @throws \Exception
     */
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