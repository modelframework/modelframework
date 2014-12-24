<?php
/**
 * Class FormConfigParserService
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModel;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormConfigParserService\StaticDataConfig\StaticDataConfig;
use ModelFramework\FormService\ConfigForm;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;

class FormConfigParserService
    implements FormConfigParserServiceInterface, FieldTypesServiceAwareInterface,
               GatewayServiceAwareInterface, ConfigServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, GatewayServiceAwareTrait, ConfigServiceAwareTrait;

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

    public function getFormConfig( $cd )
    {
        $modelName     = $cd->model;
        $formConfig    = [
            'name'            => $modelName . 'Form',
            'group'           => 'form',
            'type'            => 'form',
            'options'         => [ ],
            'attributes'      => [ 'method' => 'post', 'name' => $modelName . 'form' ], // , 'action' => 'reg'
            'fieldsets'       => [ ],
            'elements'        => [ ],
            'filters'         => [ ],
            'validationGroup' => [ ]
        ];
        $_fsGroups     = [ ];
        $_filterGroups = [ ];
        foreach ( $cd->fields as $field_name => $field_conf )
        {
            $_grp = $field_conf[ 'group' ];
            if ( !isset( $_fsGroups[ $_grp ] ) )
            {
                $_fsGroups[ $_grp ]     = [ ];
                $_filterGroups[ $_grp ] = [ ];
            }
            $_field = $this->createFormElement( $field_name, $field_conf );

            $_fsGroups[ $_grp ] += $_field[ 'elements' ];
            $_filterGroups[ $_grp ] += $_field[ 'filters' ];

            foreach ( array_keys( $_field[ 'elements' ] ) as $_k )
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
                if ( !isset( $formConfig[ 'filters' ][ $_grp ] ) )
                {
                    $formConfig[ 'filters' ][ $_grp ] = [ ];
                }
                $fsconfig[ 'elements' ] = $_fsGroups[ $_grp ];
                $formConfig[ 'filters' ][ $_grp ] += $_filterGroups[ $_grp ];
            }
            $fssConfigs[ $_grp ]                = $fsconfig;
            $formConfig[ 'fieldsets' ][ $_grp ] = [ 'type' => $modelName . 'Fieldset' ];
            if ( $_baseFieldSet == true )
            {
                $formConfig[ 'fieldsets' ][ $_grp ] = [ 'options' => [ 'use_as_base_fieldset' => true ] ];
            }
        }
        $formConfig[ 'fieldsets_configs' ] = $fssConfigs;
        $ufs                               = $this->getUtilityFieldsetsConfigs();
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
        $filter                               = $this->getFieldTypesServiceVerify()->getInputFilter( $type );
        $filter[ 'name' ]                     = $name;
        $_elementconf[ 'options' ][ 'label' ] = isset( $conf[ 'label' ] ) ? $conf[ 'label' ] : ucfirst( $name );
        if ( $type == 'lookup' )
        {
            $name .= '_id';
            $filter[ 'name' ] = $name;
            $_lall            =
                $this->getGatewayServiceVerify()->get( $conf[ 'model' ] )->find( [ ], $conf[ 'fields' ] );
            $_options         = [ ];
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
        if ( $type == 'static_lookup' )
        {
            $name .= '_id';
            $filter[ 'name' ] = $name;
            $_lall            = $this->getConfigService()->get( 'StaticDataSource', $conf[ 'model' ],
                                                                new StaticDataConfig() );
            $_options         = [ ];
            foreach ( $_lall->options as $_key => $_lrow )
            {
                $_llabel = $_lrow[ $_lall->attributes[ 'select_field' ] ];
                $_lvalue = $_key;

                $_options[ $_lvalue ] = $_llabel;
            }
            if ( isset( $conf[ 'default' ] ) )
            {
                $_elementconf[ 'options' ][ 'value_options' ] = $_options;
//                $_elementconf[ 'attributes' ][ 'value' ]      = $conf[ 'default' ];
            }
            else
            {
                $_elementconf[ 'options' ][ 'value_options' ] += $_options;
            }
            $_elementconf[ 'options' ][ 'label' ] = $conf[ 'fields' ][ $_lall->attributes[ 'select_field' ] ];
//            prn($_elementconf);
//            exit;
//            $_elementconf[ 'options' ][ 'value_options' ] += $_options;
//            prn($_lall);
//            exit;
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

        $result = [ 'filters' => [ $name => $filter ], 'elements' => [ $name => $_elementconf ] ];

//        $result = [ $name => $_elementconf ];

        return $result;
    }

} 