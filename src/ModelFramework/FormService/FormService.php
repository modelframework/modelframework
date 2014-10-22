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
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;

class FormService implements FormServiceInterface, ModelConfigParserServiceAwareInterface, AclServiceAwareInterface
{

    use ModelConfigParserServiceAwareTrait, AclServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function get( $modelName )
    {
        return $this->getForm( $modelName );
    }

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function getForm( $modelName )
    {
        return $this->createForm( $modelName );
    }

    /**
     * @param string $modelName
     *
     * @return DataFormInterface
     */
    protected function createForm0( $modelName )
    {
        $aclModel = $this->getAclServiceVerify()->get( $modelName );
        prn( 'Form Service', $aclModel->getAclDataVerify() );

        return [ 'form' => '123' ];
//        $modelConfig     = $this->getModelConfigParserServiceVerify()->getModelConfig( $modelName );
//        $model           = new DataModel();
//        $model->_fields  = $modelConfig[ 'fields' ];
//        $model->_model   = $modelConfig[ 'model' ];
//        $model->_label   = $modelConfig[ 'label' ];
//        $model->_adapter = $modelConfig[ 'adapter' ];
//        $model->exchangeArray( [ ] );
//
//        return $model;
    }

    /**
     * @param string $modelName
     *
     * @return DataFormInterface
     */
    public function createForm( $modelName, $mode = 'e' )
    {
        $modelConfig = $this->getModelConfigParserServiceVerify()->getViewConfig( $modelName );
        $aclModel    = $this->getAclServiceVerify()->get( $modelName );
        $aclData     = $aclModel->getAclDataVerify();
        prn( 'Form Service', $aclModel -> toArray(), $aclData, $modelConfig );

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

        foreach( $modelConfig['fieldsets'] as $fieldSet)
        {
            prn($fieldSet);
        }

        exit;

        $_fsGroups = [ ];
        foreach ( $aclData->fields as $field_name => $field_conf )
        {
            $_grp = $field_conf[ 'group' ];
            if ( !isset( $_fsgroups[ $_grp ] ) )
            {
                $_fsgroups[ $_grp ] = [ ];
            }
            $_element = $this->createFormElement( $field_name, $field_conf );

            $_fsgroups[ $_grp ] += $_element;

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

            if ( isset( $_fsgroups[ $_grp ] ) )
            {
                $fsconfig[ 'elements' ] = $_fsgroups[ $_grp ];
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

    public function getPermittedConfig( $modelName, $model, $mode = Acl::MODE_READ )
    {
        $fieldPermissions = $this->getFieldPermissions( $this->user(), $modelName, $model, $mode );

        $cm = $this->getConfig( $modelName );

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

    public function getFieldPermissions( $user, $modelName, $model, $mode = Acl::MODE_READ )
    {
        $acl = $this->table( 'Acl' )->findOne( [ 'role_id' => $user->role_id, 'resource' => $modelName ] );

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
            $fieldModes       = ACL::getFieldPerms( $mode );
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