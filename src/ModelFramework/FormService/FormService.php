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
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareInterface;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareTrait;
use Wepo\Lib\Acl;

class FormService implements FormServiceInterface, FieldTypesServiceAwareInterface, ModelConfigsServiceAwareInterface,
                             ModelConfigParserServiceAwareInterface, AclServiceAwareInterface,
                             GatewayServiceAwareInterface, AuthServiceAwareInterface
{

    use ModelConfigParserServiceAwareTrait, FieldTypesServiceAwareTrait, ModelConfigsServiceAwareTrait, AclServiceAwareTrait, GatewayServiceAwareTrait, AuthServiceAwareTrait;

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
     * @param string $modelName
     *
     * @return DataFormInterface
     */
    protected function createForm0( $model, $mode )
    {
        $modelName = $model->getModelName();
        $aclModel  = $this->getAclServiceVerify()->get( $modelName );
        prn( 'Form Service', $aclModel->getAclDataVerify(), 'Mode = ' . $mode );
        $formConfig = $this->getModelConfigParserServiceVerify()->getFormConfig( $modelName );

        return [ 'form' => '123' ];
//        $model           = new DataModel();
//        $model->_fields  = $modelConfig[ 'fields' ];
//        $model->_model   = $modelConfig[ 'model' ];
//        $model->_label   = $modelConfig[ 'label' ];
//        $model->_adapter = $modelConfig[ 'adapter' ];
//        $model->exchangeArray( [ ] );
//
//        return $model;
    }


    public function createFormWithConfig( $modelConfig, $aclData )
    {
        $cm = $this->splitPermittedConfig( $modelConfig, $aclData );

        return null;
    }


    public function splitPermittedConfig( $modelConfig, $aclData )
    {
        prn($aclData);
        exit();
//        $fieldPermissions = $this->getFieldPermissions( $model, $mode );

//        $cm = $this->getConfig( $modelName );

        $allowedFields = [ ];
        foreach ( $modelConfig->fields as $k => $v )
        {
            if ( in_array( $k, $fieldPermissions ) )
            {
                $allowedFields[ $k ] = $v;
            }
        }
        $modelConfig->fields = $allowedFields;

        return $modelConfig;
        return null;
    }



    /**
     * @param DataModelInterface $model
     * @param        string      $mode
     *
     * @return DataFormInterface
     */
    public function createForm( DataModelInterface $model, $mode )
    {
        $modelName = $model->getModelName();
        prn( $modelName );
        $cm = $this->getPermittedConfig( $model, $mode );
        exit;
        $modelConfig = $this->getModelConfigParserServiceVerify()->getViewConfig( $modelName );
        $aclModel    = $this->getAclServiceVerify()->get( $modelName );
        $aclData     = $aclModel->getAclDataVerify();
        prn( 'Form Service', $aclModel->toArray(), $aclData, $modelConfig );

//        return [ 'form' => '123' ];
//        $cm = $this->getPermittedConfig( $modelName, $model, $mode );

        $formConfig = [
            'name'            => $modelName . 'Form',
            'group'           => 'form',
            'type'            => 'form',
            'options'         => [ ],
            'attributes'      => [ 'method' => 'post', 'name' => $modelName . 'form' ], // , 'action' => 'reg'
            'fieldsets'       => [ ],
            'elements'        => [ ],
            'validationGroup' => [ ]
        ];
        $fss        = [ ];

        prn( 'Form Config', $formConfig );

        foreach ( $modelConfig[ 'fieldsets' ] as $fieldSet )
        {
            prn( $fieldSet );
        }

        exit;

        $_fsGroups = [ ];
        foreach ( $aclData->fields as $field_name => $field_conf )
        {
            $_grp = $field_conf[ 'group' ];
            if ( !isset( $_fsGroups[ $_grp ] ) )
            {
                $_fsGroups[ $_grp ] = [ ];
            }
            $_element = $this->createFormElement( $field_name, $field_conf );

            $_fsGroups[ $_grp ] += $_element;

            foreach ( array_keys( $_element ) as $_k )
            {
                $formConfig[ 'validationGroup' ][ $_grp ][ ] = $_k;
            }
        }

        foreach ( $cm->groups as $_grp => $_fls )
        {

            if ( is_numeric( $_grp ) )
            {
                $_grp = $_fls;
                $_lbl = $cm->model . ' information';
                if ( $_grp == 'fields' )
                {
                    $_baseFieldSet = true;
                }
                else
                {
                    $_baseFieldSet = false;
                }

            }
            else
            {
                $_lbl          = $_fls[ 'label' ];
                $_baseFieldSet = isset( $_fls[ 'base' ] ) && $_fls[ 'base' ] == true;
            }

            $fsconfig = [
                'name'            => $modelName . 'Fieldset',
                'group'           => $_grp,
                'type'            => 'fieldset',
                'options'         => [ 'label' => $_lbl ],
                'attributes'      => [ 'name' => $_grp, 'class' => 'table' ],
                'fieldsets'       => [ ],
                'elements'        => [ ],
                'validationGroup' => [ ]
            ];

            if ( isset( $_fsGroups[ $_grp ] ) )
            {
                $fsconfig[ 'elements' ] = $_fsGroups[ $_grp ];
            }

//            foreach ( $cm -> fields as $field_name => $field_conf )
//            {
//                if ( $field_conf[ 'group' ] == $_grp )
//                {
//                    $fsconfig = array_merge_recursive( $fsconfig, $this -> createFormElement( $field_name, $field_conf ) );
//
//                    $formConfig[ 'validaionGroup' ][ $_grp ][] = $field_name;
//                }
//            }

            $cfs          = new ConfigForm();
            $fieldset     = Obj::create( '\\Wepo\\Lib\\WepoFieldset' );
            $fss[ $_grp ] = $fieldset->parseconfig( $cfs->exchangeArray( $fsconfig ), [ ] );

            $formConfig[ 'fieldsets' ][ $_grp ] = [ 'type' => $modelName . 'Fieldset' ];
            if ( $_baseFieldSet == true )
            {
                $formConfig[ 'fieldsets' ][ $_grp ] = [ 'options' => [ 'use_as_base_fieldset' => true ] ];
            }

        }

        # add
        $utilityfs = $this->getUtilityFieldsets( $modelName );
        foreach ( $utilityfs as $fieldset )
        {
            $fss[ $fieldset->getName() ]                       = $fieldset;
            $formConfig[ 'fieldsets' ][ $fieldset->getName() ] = [ 'type' => get_class( $fieldset ) ];
        }

        $cf = new ConfigForm();
        $cf->exchangeArray( $formConfig );
        $form = Obj::create( '\\Wepo\\Lib\\WepoForm' );

        return $form->parseconfig( $cf, $fss );
    }

    protected function getUtilityFieldsets( $modelName )
    {
        $fs = [ ];

        $fs[ ] = new \Wepo\Form\ButtonFieldset();
        $fs[ ] = new \Wepo\Form\SaUrlFieldset();

        return $fs;
    }

    public function getPermittedConfig( $model, $mode )
    {
        $fieldPermissions = $this->getFieldPermissions( $model, $mode );

//        $cm = $this->getConfig( $modelName );

        $allowedFields = [ ];
        foreach ( $cm->fields as $k => $v )
        {
            if ( in_array( $k, $fieldPermissions ) )
            {
                $allowedFields[ $k ] = $v;
            }
        }
        $cm->fields = $allowedFields;

        return $cm;
    }

    public function getFieldPermissions( $model, $mode )
    {
        $modelName = $model -> getModelName();
        $user = $this -> getAuthServiceVerify()->getUser();
        $acl = $this->getGatewayServiceVerify() -> get( 'Acl' )->findOne( [ 'role_id' => $user->role_id, 'resource' => $modelName ] );
        if ( $acl )
        {
            $modelPermissions = $acl->permissions;
            $groups           = $user->groups;
            $groups[ ]        = $user->_id;
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

        prn($fieldPermissions);
        exit;

        return $fieldPermissions;
    }

    protected function createFormElement( $name, $conf )
    {
        $type                                 = $conf[ 'type' ];
        $_elementconf                         = $this->_fieldtypes[ $type ][ 'formElement' ];
        $_elementconf[ 'options' ][ 'label' ] = isset( $conf[ 'label' ] ) ? $conf[ 'label' ] : ucfirst( $name );
        if ( $type == 'lookup' )
        {
            $name .= '_id';
            //$conf[ 'fields' ] это не совесем порядок сортировки
            $_lall    = $this->table( $conf[ 'model' ] )->find( [ ], $conf[ 'fields' ] );
            $_options = [ ];
            foreach ( $_lall as $_lrow )
            {
                $_llabel = '';
                $_lvalue = $_lrow->id();
                foreach ( array_keys( $conf[ 'fields' ] ) as $_k )
                {
                    if ( strlen( $_llabel ) )
                    {
                        $_llabel .= ' ';
                    }
                    $_llabel .= $_lrow->$_k;
                }
                $_options[ $_lvalue ] = $_llabel;
            }
            $_elementconf[ 'options' ][ 'value_options' ] += $_options;
        }
        $_elementconf[ 'attributes' ][ 'name' ] = $name;
        if ( isset( $conf[ 'required' ] ) )
        {
            $_elementconf[ 'attributes' ][ 'required' ] = 'required';
            if ( isset( $_elementconf[ 'options' ][ 'label_attributes' ][ 'class' ] ) &&
                 strlen( $_elementconf[ 'options' ][ 'label_attributes' ][ 'class' ] )
            )
            {
                $_elementconf[ 'options' ][ 'label_attributes' ][ 'class' ] .= ' required';
            }
            else
            {
                $_elementconf[ 'options' ][ 'label_attributes' ] = [ 'class' => 'required' ];
            }
        }
        $result = [ $name => $_elementconf ];

        return $result;
    }

}