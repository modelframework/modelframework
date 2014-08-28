<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 10:51 AM
 */

namespace ModelFramework\FieldTypesService;


class FiledTypesService implements  FieldTypesServiceInterface {
    /**
     * @var array
     */
    protected $_fieldTypes = [
        'text'     => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 3, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name'  => 'field',
                    'type'  => 'text',
                    'class' => ''
                ],
                'options'    => [
                    'label'            => 'Label',
                    'label_attributes' => [ 'class' => 'Label' ]
                ]
            ]
        ],
        'password'     => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 3, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name'  => 'field',
                    'type'  => 'password',
                    'class' => ''
                ],
                'options'    => [
                    'label'            => 'Label',
                    'label_attributes' => [ 'class' => 'Label' ]
                ]
            ]
        ],
        'array'    => [
            'field'       => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
            'inputFilter' => [ ],
            'formElement' => [ ]
        ],
        'textarea' => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 1, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name'  => 'field',
                    'type'  => 'textarea',
                    'class' => ''
                ],
                'options'    => [
                    'label' => 'Label'
                ]
            ]
        ],
        'integer'  => [
            'field'       => [ 'type' => 'field', 'datatype' => 'int', 'default' => '' ],
            'inputFilter' => [
                'name'     => 'field',
                'required' => false,
                'filters'  => [ [ 'name' => 'Int' ] ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name'  => 'field',
                    'type'  => 'text',
                    'class' => ''
                ],
                'options'    => [
                    'label_attributes' => [ 'class' => 'required' ],
                    'label'            => 'Label'
                ]
            ]
        ],
        'date'     => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 10,
                            'max'      => 20
                        ]
                    ],
                    [
                        'name'    => 'Date',
                        'options' => [ 'format' => 'Y-m-d' ]
                    ],
                    [
                        'name'    => 'Between',
                        'options' => [
                            'min' => '1940-01-01',
                            'max' => '2114-01-24'
                        ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name' => 'birth_date',
                    'type' => 'date',
                    'min'  => '1960-01-01',
                    'max'  => '2244-01-29',
                ],
                'options'    => [ 'label' => 'Label' ]
            ]
        ],
        'datetime' => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 20
                        ]
                    ],
                    //                    [
                    //                        'name'    => 'Date',
                    //                        'options' => [ 'format' => "d.m.Y H:i" ]
                    //                    ],
                    [
                        'name'    => 'Between',
                        'options' => [
                            'min' => '1940-01-01 00:00:00',
                            'max' => '2114-01-24 00:00:00'
                        ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element\\DateTimeLocal',
                'attributes' => [
                    'type' => 'datetime-local',
                    'name' => 'call_start_dtm'
                ],
                'options'    => [ 'label' => 'Start call' ]
            ]
        ],
        'email'    => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 3, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name' => 'login',
                    'type' => 'text',
                ],
                'options'    => [
                    'label' => 'Account Name',
                ]
            ]
        ],
        'phone'    => [
            'field'       => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 7, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\\Form\\Element',
                'attributes' => [
                    'name' => 'phone',
                    'type' => 'text',
                ],
                'options'    => [
                    'label' => 'Phone',
                ]
            ]
        ],
        'lookup'   => [
            'field'       => [ 'type' => 'source', 'datatype' => 'string', 'default' => '', 'alias' => 'lookup' ],
            'inputFilter' => [
                'name'       => 'field',
                'required'   => false,
                'filters'    => [ [ 'name' => 'StripTags' ], [ 'name' => 'StringTrim' ] ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [ 'encoding' => 'UTF-8', 'min' => 1, 'max' => 100 ]
                    ]
                ]
            ],
            'formElement' => [
                'type'       => 'Zend\Form\Element\Select',
                'name'       => 'field',
                'attributes' => [
                    'id' => 'field'
                ],
                'options'    => [
                    'label'         => 'Field',
                    'value_options' => [ 0 => 'Please select ... ' ]
                ]
            ]
        ],
    ];

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getInputFilter( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'inputFilter' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getInputFilter' );
        }

        return $this->_fieldTypes[ $type ][ 'inputFilter' ];
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getField( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'field' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getField' );
        }

        return $this->_fieldTypes[ $type ][ 'field' ];
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getFormElement( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'formElement' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getFormElement' );
        }

        return $this->_fieldTypes[ $type ][ 'formElement' ];
    }

    /**
     * @param string $modelName
     *
     * @return mixed
     */
    public function getUtilityFields( $modelName = '' )
    {
        return [
            'fields'  =>
                [
                    '_id' => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '', 'label' => 'ID' ],
                    'acl' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ], 'label' => 'acl' ],
                ],
            'filters' => [ '_id' => $this->getInputFilter( 'text' ) ],
        ];
    }

} 