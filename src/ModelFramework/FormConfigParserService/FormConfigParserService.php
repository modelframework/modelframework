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
use ModelFramework\FormService\ConfigForm;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareInterface;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareTrait;
use ModelFramework\Utility\Obj;

class FormConfigParserService
    implements FormConfigParserServiceInterface, FieldTypesServiceAwareInterface, ModelConfigsServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ModelConfigsServiceAwareTrait;

    protected $_utilityFieldsetsConfigs = [
        'ButtonFieldset' => [
            'name'       => 'ButtonFieldset',
            'group'      => 'button',
            'type'       => 'fieldset',
            'options'    => [ ],
            'attributes' => [
                'name'  => 'button',
                'class' => 'buttons',
            ],
            'fieldsets'  => [ ],
            'elements'   => [
                'submit' => [
                    'type'       => 'Zend\Form\Element',
                    'attributes' => [
                        'value' => 'Save',
                        'name'  => 'submit',
                        'type'  => 'submit'
                    ],
                    'options'    => [ ]
                ]
            ]
        ],
        'SuUrlFieldset'  => [
            'name'       => 'SuUrlFieldset',
            'group'      => 'saurl',
            'type'       => 'fieldset',
            'options'    => [ ],
            'attributes' => [
                'name' => 'saurl',
            ],
            'fieldsets'  => [ ],
            'elements'   => [
                'back' => [
                    'type'       => 'Zend\Form\Element',
                    'attributes' => [
                        'name' => 'back',
                        'type' => 'hidden'
                    ],
                    'options'    => [ ]
                ]
            ]
        ]
    ];

    public function getUtilityFieldsetsConfigs()
    {
        return $this->_utilityFieldsetsConfigs;
    }

    public function getFormConfig( $modelName )
    {
        $cd = $this->getModelConfigsServiceVerify()->get( $modelName );
        prn( 'FormConfigParser', $modelName, $cd );

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
        $_fsGroups  = [ ];
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

        $fssConfigs = [ ];

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

//            $cfs          = new \Wepo\Model\ConfigForm();
//            $fieldset     = Obj::create( '\\Wepo\\Lib\\WepoFieldset' );
            $fssConfigs[ $_grp ] = $fsconfig;

            $formConfig[ 'fieldsets' ][ $_grp ] = [ 'type' => $modelName . 'Fieldset' ];
            if ( $_baseFieldSet == true )
            {
                $formConfig[ 'fieldsets' ][ $_grp ] = [ 'options' => [ 'use_as_base_fieldset' => true ] ];
            }
        }
        $formConfig[ 'fieldsets_configs' ] = $fssConfigs;

        $ufs = $this->getUtilityFieldsetsConfigs();
        foreach ( $ufs as $fieldset )
        {
            $formConfig[ 'fieldsets' ][ $fieldset[ 'name' ] ]         = [ 'type' => $fieldset[ 'name' ] ];
            $formConfig[ 'fieldsets_configs' ][ $fieldset[ 'name' ] ] = $fieldset;
        }
        $cf = new ConfigForm();
        $cf->exchangeArray( $formConfig );

        return $cf;
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
            prn( 'createFormElement', $conf[ 'model' ], $conf[ 'fields' ] );
//            prn('createFormElement', $this->getModelConfigsServiceVerify()->get());
//            exit;

//            $_lall    = $this->table( $conf[ 'model' ] )->find( [ ], $conf[ 'fields' ] );
            $_options = [ ];
//            foreach ( $_lall as $_lrow )
//            {
//                $_llabel = '';
//                $_lvalue = $_lrow->id();
//                foreach ( array_keys( $conf[ 'fields' ] ) as $_k )
//                {
//                    if ( strlen( $_llabel ) )
//                    {
//                        $_llabel .= ' ';
//                    }
//                    $_llabel .= $_lrow->$_k;
//                }
//                $_options[ $_lvalue ] = $_llabel;
//            }
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


//    protected function getUtilityFieldsets( $modelName )
//    {
//        $fs = [ ];
//
//        $fs[ ] = new \Wepo\Form\ButtonFieldset();
//        $fs[ ] = new \Wepo\Form\SaUrlFieldset();
//
//        return $fs;
//    }

} 