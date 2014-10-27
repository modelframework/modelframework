<?php
/**
 * Class FormConfigParserService
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareInterface;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareTrait;

class FormConfigParserService
    implements FormConfigParserServiceInterface, FieldTypesServiceAwareInterface, ModelConfigsServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ModelConfigsServiceAwareTrait;

    public function getFormConfig( $modelName )
    {
        $cd = $this->getModelConfigsServiceVerify()->get( $modelName );
        prn( 'FormConfigParser', $modelName, $cd );

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

//        prn( 'Form Config', $formConfig );
//
//        foreach ( $modelConfig[ 'fieldsets' ] as $fieldSet )
//        {
//            prn( $fieldSet );
//        }
//
//        exit;

        $_fsGroups = [ ];
        foreach ( $cd->fields as $field_name => $field_conf )
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

        foreach ( $cd->groups as $_grp => $_fls )
        {

            if ( is_numeric( $_grp ) )
            {
                $_grp = $_fls;
                $_lbl = $cd->model . ' information';
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

//            foreach ( $cd -> fields as $field_name => $field_conf )
//            {
//                if ( $field_conf[ 'group' ] == $_grp )
//                {
//                    $fsconfig = array_merge_recursive( $fsconfig, $this -> createFormElement( $field_name, $field_conf ) );
//
//                    $formConfig[ 'validaionGroup' ][ $_grp ][] = $field_name;
//                }
//            }

            $cfs          = new \Wepo\Model\ConfigForm();
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

        $cf = new \Wepo\Model\ConfigForm();
        $cf->exchangeArray( $formConfig );
        $form = Obj::create( '\\Wepo\\Lib\\WepoForm' );


        prn($form->parseconfig( $cf, $fss ));
        exit;
        return $form->parseconfig( $cf, $fss );
    }

    protected function createFormElement( $name, $conf )
    {
        $type = $conf[ 'type' ];
//        $_elementconf                         = $this->_fieldtypes[ $type ][ 'formElement' ];
        $_elementconf                         = $this->getFieldTypesServiceVerify()->getFormElement( $type );
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