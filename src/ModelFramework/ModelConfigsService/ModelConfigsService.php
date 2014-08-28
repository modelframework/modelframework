<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 8:53 PM
 */

namespace ModelFramework\ModelConfigsService;

use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\DataModel\Custom\ConfigData;
use ModelFramework\Utility\Arr;

class ModelConfigsService implements ModelConfigsServiceInterface, GatewayServiceAwareInterface
{

    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_modelConfig = [
//        'ModelView'          => [
//            'label'   => 'ModelView',
//            'adapter' => 'wepo_company',
//            'model'   => 'ModelView',
//            'fields'  => [
//                'name'       => [ 'type' => 'text', 'group' => 'fields', 'label' => 'ModelView Name', 'default' => '' ],
//                'custom'     => [ 'type' => 'integer', 'group' => 'fields', 'label' => 'Custom View', 'default' => '' ],
//                'model_name' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Model Name', 'default' => '' ],
//                'mode'       => [ 'type' => 'text', 'group' => 'fields', 'label' => 'View Mode', 'default' => 'list' ],
//                'query'      => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Query', 'default' => [ ] ],
//                'fields'     => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Fields', 'default' => [ ] ],
//                'params'     => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Parameters', 'default' => [ ] ],
//                'groups'     => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Parameters', 'default' => [ ] ],
//            ],
//            'groups'  => [
//                'fields' => [ 'label' => 'Model View Information', 'base' => true ],
//            ]
//        ],
//        'ConfigData'         => [
//            'label'   => 'Model Configuration',
//            'adapter' => 'wepo_company',
//            'model'   => 'ConfigData',
//            'fields'  => [
//                'label'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Model Label', 'default' => '' ],
//                'adapter' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Adapter Name', 'default' => '' ],
//                'model'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Model Name', 'default' => '' ],
//                'fields'  => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Fields', 'default' => [ ] ],
//                'groups'  => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Groups', 'default' => [ ] ],
//            ],
//            'groups'  => [
//                'fields' => [ 'label' => 'Config Data Information', 'base' => true ],
//            ]
//        ],
        'Acl'                => [
            'label'   => 'Acl Configuration',
            'adapter' => 'wepo_company',
            'model'   => 'Acl',
            'fields'  => [
                'type'        => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Type', 'default' => '' ],
                'resource'    => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Resource', 'default' => '' ],
                'role'        => [
                    'type'   => 'lookup', 'group' => 'fields', 'model' => 'Role',
                    'fields' => [ 'role' => 'Role' ]
                ],
                'permissions' => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Permissions', 'default' => [ ] ],
                'fields'      => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Fields', 'default' => [ ] ],
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Acl Data Information', 'base' => true ],
            ]

        ],
        'MainUser'           => [
            'label'   => 'Main User',
            'adapter' => 'wepo_main',
            'model'   => 'MainUser',
            'fields'  => [
                'fname'       => [
                    'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'First Name'
                ],
                'lname'       => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Last Name' ],
                'company_id'  => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Company ID' ],
                'role'        => [
                    'type'   => 'lookup', 'group' => 'fields', 'model' => 'Role',
                    'fields' => [ 'role' => 'Role' ]
                ],
                'login'       => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Login' ],
                'password'    => [
                    'type' => 'password', 'default' => '', 'group' => 'fields', 'label' => 'Password'
                ],
                'created_dtm' => [
                    'type'  => 'datetime', 'default' => '', 'group' => 'fields',
                    'label' => 'Created Date'
                ],
                'status'      => [
                    'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status',
                    'fields' => [ 'status' => 'Status' ]
                ],
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Main User Information', 'base' => true ]
            ],
            'unique'  => [ 'login' ]

        ],
        'MainCompany'        => [
            'label'   => 'Main Company',
            'adapter' => 'wepo_main',
            'model'   => 'MainCompany',
            'fields'  => [
                'company'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Company' ],
                'created_dtm' => [
                    'type'  => 'datetime', 'default' => '', 'group' => 'fields',
                    'label' => 'Created Date'
                ],
                'status'      => [
                    'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status',
                    'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Main Company Information', 'base' => true ]
            ],
            'unique'  => [ 'login' ]

        ],
        'MainDb'             => [
            'label'   => 'Main Db',
            'adapter' => 'wepo_main',
            'model'   => 'MainDb',
            'fields'  => [
                'driver'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Driver' ],
                'gateway'        => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Gateway' ],
                'dsn'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Dsn' ],
                'driver_options' => [
                    'type'  => 'array', 'default' => [ ], 'group' => 'fields',
                    'label' => 'Driver Options'
                ],
                'dbname'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Name' ],
                'name'           => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Name' ],
                'company_id'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Company ID' ],
                'username'       => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User name' ],
                'password'       => [
                    'type' => 'password', 'default' => '', 'group' => 'fields', 'label' => 'Password'
                ],
                'status'         => [
                    'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status',
                    'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Main Db Information', 'base' => true ]
            ],
            'unique'  => [ 'name' ]

        ],
        'User'               =>
            [
                'label'   => 'User',
                'adapter' => 'wepo_company',
                'model'   => 'User',
                'fields'  => [
                    'fname'       => [
                        'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'First Name'
                    ],
                    'lname'       => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Last Name' ],
                    'main_id'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Main ID' ],
                    'role'        => [
                        'type'   => 'lookup', 'group' => 'fields', 'model' => 'Role',
                        'fields' => [ 'role' => 'Role' ]
                    ],
                    'groups'      => [ 'type' => 'array', 'group' => 'fields', 'label' => 'Groups', 'default' => [ ] ],

                    'login'       => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Login' ],
                    'password'    => [
                        'type' => 'password', 'default' => '', 'group' => 'fields', 'label' => 'Password'
                    ],

                    'ip'          => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'IP' ],
                    'birth_date'  => [
                        'type'  => 'date', 'default' => '', 'group' => 'fields',
                        'label' => 'Date of Birth'
                    ],
                    'changer'     => [
                        'type'   => 'lookup', 'group' => 'fields', 'model' => 'User',
                        'fields' => [ 'login' => 'Changer' ]
                    ],
                    'changed_dtm' => [
                        'type'  => 'datetime', 'default' => '', 'group' => 'fields',
                        'label' => 'Changed Date'
                    ],
                    'created_dtm' => [
                        'type'  => 'datetime', 'default' => '', 'group' => 'fields',
                        'label' => 'Created Date'
                    ],
                    'avatar'      => [
                        'type'  => 'text', 'default' => 'user.jpg', 'group' => 'social',
                        'label' => 'Photo'
                    ],
                    'status'      => [
                        'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status',
                        'fields' => [ 'status' => 'Status' ]
                    ],
                    'newitems'    => [
                        'type'    => 'array', 'group' => 'fields', 'label' => 'New items',
                        'default' => [ ]
                    ],
                    'flink'       => [ 'type' => 'text', 'default' => '', 'group' => 'social', 'label' => 'Facebook' ],
                    'tlink'       => [ 'type' => 'text', 'default' => '', 'group' => 'social', 'label' => 'Twitter' ],
                    'llink'       => [ 'type' => 'text', 'default' => '', 'group' => 'social', 'label' => 'LinkedIn' ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Config Model Information', 'base' => true ],
                    'social' => [ 'label' => 'Social Links', 'base' => false ]
                ],
                'unique'  => [ 'login' ]
            ],
        'Test'               => [
            'adapter' => 'wepo_company',
            'model'   => 'Test',
            'fields'  => [
                'fname'      => [ 'type' => 'text', 'group' => 'fields', 'label' => 'First Name' ],
                'lname'      => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Last Name' ],
                'email'      => [ 'type' => 'email', 'group' => 'fields', 'label' => 'E-mail' ],
                'phone'      => [ 'type' => 'phone', 'group' => 'fields', 'label' => 'Phone' ],
                'price'      => [ 'type' => 'integer', 'group' => 'fields', 'label' => 'Price' ],
                'birth_date' => [ 'type' => 'date', 'group' => 'fields', 'label' => 'Birthdate' ],
                'remind_dtm' => [ 'type' => 'datetime', 'group' => 'activity', 'label' => 'RemindDate' ],
                'notes'      => [ 'type' => 'text', 'group' => 'notes', 'label' => 'Notes' ],
                'owner'      => [
                    'type' => 'lookup', 'group' => 'activity', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ]
            ],
            'groups'  => [
                'fields'   => [ 'label' => 'Test  Information', 'base' => true ],
                'activity' => [ 'label' => 'Activity Information' ],
                'notes'    => [ 'label' => 'Detail Information' ]
            ]
        ],
        'Table'              => [
            'adapter' => 'wepo_company',
            'model'   => 'Table',
            'fields'  => [
                'table' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Table Name' ],
                'label' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Label' ],
                'rows'  => [ 'type' => 'integer', 'group' => 'fields', 'label' => 'Rows' ],
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Table Information', 'base' => true ],
            ]
        ],
        'Field'              => [
            'adapter' => 'wepo_company',
            'model'   => 'Field',
            'fields'  => [
                'alias'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Alias' ],
                'field'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Field' ],
                'label'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Label' ],
                'order'   => [ 'type' => 'integer', 'group' => 'fields', 'label' => 'Order' ],
                'visible' => [ 'type' => 'integer', 'group' => 'fields', 'label' => 'Visible' ],
                'target'  => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Target' ],
                'table'   => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'table' => 'Table Name' ]
                ]
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Table Information', 'base' => true ],
            ]
        ],
        'SaUrl'              => [
            'adapter' => 'wepo_company',
            'model'   => 'SaUrl',
            'fields'  => [
                'label' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Label' ],
                'url'   => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Url' ]
            ],
            'groups'  => [
                'fields' => [ 'label' => 'SaUrl', 'base' => true ],
            ]
        ],
        'Role'               => [
            'adapter' => 'wepo_company',
            'model'   => 'Role',
            'fields'  => [
                'role' => [ 'type' => 'text', 'group' => 'fields', 'label' => 'Role' ],
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Role', 'base' => true ],
            ]
        ],
        'MailChain'               =>
            [
                'label'   => 'Mail chain',
                'adapter' => 'wepo_company',
                'model'   => 'MailChain',
                'fields'  => [
                    //text fields
                    'title'        => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Title' ],
                    'reference'    => [ 'type' => 'array', 'default' => [], 'group' => 'fields', 'label' => 'Reference' ],
                    'date'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Date' ],
                    'count'        => [ 'type' => 'integer', 'default' => 1, 'group' => 'fields', 'label' => 'Mail count' ],
//                    'order'        => [ 'type' => 'array', 'default' => [], 'group' => 'fields', 'label' => 'Mail count' ],
                    //source fields
                    'status'       => [ 'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ] ],
                    'owner'        => [ 'type'   => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ] ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Mail', 'base' => true ],
                ],
            ],
        'Mail'               =>
            [
                'label'   => 'Mail',
                'adapter' => 'wepo_company',
                'model'   => 'Mail',
                'fields'  => [
                    //system fields
                    'protocol_ids' => [ 'type'  => 'array', 'default' => [ ], 'group' => 'fields',
                                        'label' => 'Protocol ids'
                    ],
                    //header fields
                    'header'       => [ 'type'  => 'array', 'default' => [ ], 'group' => 'fields',
                                        'label' => 'Mail headers'
                    ],
                    //text fields
                    'text'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Mail text' ],
                    'title'        => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Title' ],
                    'info'         => [ 'type'  => 'array', 'default' => [ ], 'group' => 'fields','label' => 'Additional mail info' ],
                    'attachment'   => [ 'type'  => 'array', 'default' => [ ], 'group' => 'fields', 'label' => 'Attachments' ],
                    'type'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Mail type' ],
                    'date'         => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Date' ],
                    'size'         => [ 'type' => 'integer', 'default' => 0, 'group' => 'fields', 'label' => 'Mail size' ],
                    //source fields
                    'email_link'   => [ 'type'  => 'array', 'default' => [ ], 'group' => 'fields', 'label' => 'Email connections' ],
                    'chain_id'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Chain' ],
                    'status'       => [ 'type'   => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ] ],
                    'owner'        => [ 'type'   => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ] ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Mail', 'base' => true ],
                ],
            ],
        'MailSetting'        =>
            [
                'label'   => 'Mail setting',
                'adapter' => 'wepo_company',
                'model'   => 'MailSetting',
                'fields'  => [
                    'email'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Email'
                    ],
                    'setting_user'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User' ],
                    'setting_protocol' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Protocol name'
                    ],
                    'setting_host'     => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Host name'
                    ],
                    'setting_port'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Port' ],
                    'setting_security' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Security type'
                    ],
                    'pass'             => [ 'type'  => 'password', 'default' => '', 'group' => 'fields',
                                            'label' => 'Password'
                    ],
                    'type'             => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Type id'
                    ],
                    'owner_id'         => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Owner id'
                    ],
                    'user_id'          => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User id'
                    ],
                    'status_id'        => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Status id'
                    ],
                    'user'             => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User' ],
                    'owner'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Owner'
                    ],
                    'status'           => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Status'
                    ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Mail setting', 'base' => true ],
                ],
            ],
        'MailReceiveSetting' =>
            [
                'label'   => 'Mail receive setting',
                'adapter' => 'wepo_company',
                'model'   => 'MailReceiveSetting',
                'fields'  => [
                    'email'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Email'
                    ],
                    'setting_user'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User' ],
                    'setting_protocol' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Protocol name'
                    ],
                    'setting_host'     => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Host name'
                    ],
                    'setting_port'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Port' ],
                    'setting_security' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Security type'
                    ],
                    'pass'             => [ 'type'  => 'password', 'default' => '', 'group' => 'fields',
                                            'label' => 'Password'
                    ],
                    'type'             => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Type id'
                    ],
                    'owner_id'         => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Owner id'
                    ],
                    'user_id'          => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'User id'
                    ],
                    'status_id'        => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Status id'
                    ],
                    'owner'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Owner'
                    ],
                    'status'           => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Status'
                    ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Mail receive setting', 'base' => true ],
                ],
            ],
        'MailSendSetting'    =>
            [
                'label'   => 'Mail send setting',
                'adapter' => 'wepo_company',
                'model'   => 'MailSendSetting',
                'fields'  => [
                    'email'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Email'
                    ],
                    'setting_user'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User' ],
                    'setting_protocol' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Protocol name'
                    ],
                    'setting_host'     => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Host name'
                    ],
                    'setting_port'     => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Port' ],
                    'setting_security' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Security type'
                    ],
                    'pass'             => [ 'type'  => 'password', 'default' => '', 'group' => 'fields',
                                            'label' => 'Password'
                    ],
                    'type'             => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Type id'
                    ],
                    'is_default'       => [ 'type'  => 'text', 'default' => 'false', 'group' => 'fields',
                                            'label' => 'Is default'
                    ],
                    'owner_id'         => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Owner id'
                    ],
                    'user_id'          => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'User id'
                    ],
                    'status_id'        => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Status id'
                    ],
                    'owner'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Owner'
                    ],
                    'user'             => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'User' ],
                    'status'           => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'Status'
                    ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Mail send setting', 'base' => true ],
                ],
                'unique'  => [ 'email', 'setting_protocol' ]
            ],
        'Email'              =>
            [
                'adapter' => 'wepo_company',
                'model'   => 'Email',
                'fields'  => [
                    'email'            => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Email'
                    ],
                    'user_name'        => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'User name'
                    ],
                    'user_name_source' => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                            'label' => 'User source'
                    ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Email', 'base' => true ],
                ],
                'unique'  => [ 'email', 'setting_protocol' ]
            ],
        'Status'             =>
            [
                'adapter' => 'wepo_company',
                'model'   => 'Status',
                'fields'  => [
                    'status' => [ 'type' => 'text', 'default' => '', 'group' => 'fields', 'label' => 'Status'
                    ],
                    'const'  => [ 'type'  => 'text', 'default' => '', 'group' => 'fields',
                                  'label' => 'Constant'
                    ],
                ],
                'groups'  => [
                    'fields' => [ 'label' => 'Status', 'base' => true ],
                ],
                'unique'  => [ 'status' ]
            ],
        'Event'              => [
            'adapter' => 'wepo_company',
            'model'   => 'Event',
            'fields'  => [
                'type'               => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'label' ]
                ],
                'owner'              => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'table'              => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'table' ]
                ],
                'target'             => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Lead', 'fields' => [ 'login' ]
                ],
                'subject'            => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'changer'            => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'changed_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'remind_dtm'         => [ 'type' => 'datetime', 'group' => 'fields' ],
                'start_dtm'          => [ 'type' => 'datetime', 'group' => 'fields' ],
                'end_dtm'            => [ 'type' => 'datetime', 'group' => 'fields' ],
                'r_when'             => [ 'type' => 'datetime', 'group' => 'fields' ],
                'r_repeat'           => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'r_alert'            => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'rec_startdate'      => [ 'type' => 'datetime', 'group' => 'fields' ],
                'rec_enddate'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'invites'            => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'venue'              => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'notification_email' => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'description'        => [ 'type' => 'textarea', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'recurring'          => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
            ],
            'groups'  => [
                'fields'
            ],
        ],
        'Widget'             => [
            'adapter' => 'wepo_company',
            'model'   => 'Event',
            'fields'  => [
                'owner'        => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'name'         => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'path'         => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'data_model'   => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'action'       => [ 'type' => 'array', 'group' => 'fields' ],
                'model_link'   => [ 'type' => 'array', 'group' => 'fields' ],
                'where'        => [ 'type' => 'array', 'group' => 'fields' ],
                'order'        => [ 'type' => 'array', 'group' => 'fields' ],
                'limit'        => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'output_order' => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'EventLog'                => [
            'label'   => 'Event log',
            'adapter' => 'wepo_company',
            'model'   => 'EventLog',
            'fields'  => [
                'event_dtm'   => [ 'type' => 'date',   'group' => 'fields', 'label' => 'Date', 'default' => '' ],
                'target_id'   => [ 'type' => 'date',   'group' => 'fields', 'label' => 'Target', 'default' => '' ],
                'executor'    => [ 'type' => 'lookup', 'group' => 'fields', 'model'=>'User', 'fields' => ['executor' => 'login'] ],
                'table'       => [ 'type' => 'lookup', 'group' => 'fields', 'model'=>'Table', 'fields' => ['table' => 'label'] ],
                'event_id'    => [ 'type' => 'lookup', 'group' => 'fields', 'model'=>'EventType', 'fields' => [ 'event' => 'type' ] ],
            ],
            'groups'  => [
                'fields' => [ 'label' => 'Acl Data Information', 'base' => true ],
            ]

        ],
    ];

    protected $_dbConfig = [
        'Lead'     => [
            'adapter' => 'wepo_company',
            'model'   => 'Lead',
            'fields'  => [
                'owner'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ],
                'fname'       => [
                    'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1, 'label' => 'First Name'
                ],
                'lname'       => [
                    'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1, 'label' => 'Last Name'
                ],
                'phone'       => [ 'type' => 'phone', 'group' => 'address', 'max' => 100, 'label' => 'Phone' ],
                'mobile'      => [ 'type' => 'phone', 'group' => 'address', 'max' => 100, 'label' => 'Cellphone' ],
                'email'       => [ 'type' => 'email', 'group' => 'notes', 'max' => 100, 'label' => 'E-mail' ],
                'birth_date'  => [ 'type' => 'date', 'group' => 'fields', 'label' => 'Birth date' ],
                'changer'     => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Changer' ]
                ],
                'changed_dtm' => [ 'type' => 'datetime', 'group' => 'fields', 'label' => 'Changed Date' ],
                'created_dtm' => [ 'type' => 'datetime', 'group' => 'fields', 'label' => 'Created Date' ],
                'status'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields'  => [ 'label' => 'Lead  Information', 'base' => true ],
                'address' => [ 'label' => 'Address Information' ],
                'notes'   => [ 'label' => 'Notes' ]
            ]
        ],
        'Contact'  => [
            'adapter' => 'wepo_company',
            'model'   => 'Contact',
            'fields'  => [
                'owner'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ],
                'client'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Client', 'fields' => [ 'email' => 'Client' ]
                ],
                'fname'       => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'lname'       => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                //              'login'       => [ 'type' => 'text', 'group' => 'fields', 'max' => 100 ],
                'phone'       => [ 'type' => 'phone', 'group' => 'fields', 'max' => 100 ],
                'mobile'      => [ 'type' => 'phone', 'group' => 'fields', 'max' => 100 ],
                'email'       => [ 'type' => 'email', 'group' => 'fields', 'max' => 100 ],
                'birth_date'  => [ 'type' => 'date', 'group' => 'fields' ],
                'address'     => [ 'type' => 'text', 'group' => 'fields', 'max' => 256 ],
                'changer'     => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Changer' ]
                ],
                'changed_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'status'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Client'   => [
            'adapter' => 'wepo_company',
            'model'   => 'Client',
            'fields'  => [
                'owner'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ],
                'name'        => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'phone'       => [ 'type' => 'phone', 'group' => 'fields', 'max' => 100 ],
                'email'       => [ 'type' => 'email', 'group' => 'fields', 'max' => 100 ],
                'changer'     => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Changer' ]
                ],
                'changed_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'status'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Document' => [
            'adapter' => 'wepo_company',
            'model'   => 'Document',
            'fields'  => [
                'owner'              => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ],
                'document_name'      => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'document_way'       => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'document_real_name' => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'file_size'          => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'changer'            => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Changer' ]
                ],
                'changed_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'status'             => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Product'  => [
            'adapter' => 'wepo_company',
            'model'   => 'Product',
            'fields'  => [
                'owner'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Owner' ]
                ],
                'name'        => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'price'       => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'description' => [ 'type' => 'textarea', 'group' => 'fields', 'max' => 200, 'required' => 1 ],
                'changer'     => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' => 'Changer' ]
                ],
                'changed_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'status'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' => 'Status' ]
                ]
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Activity' => [
            'adapter' => 'wepo_company',
            'model'   => 'Product',
            'fields'  => [
                'type'        => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'label' ]
                ],
                'owner'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'table'       => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'table' ]
                ],
                'target'      => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Lead', 'fields' => [ 'login' ]
                ],
                'subject'     => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'changer'     => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'changed_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm' => [ 'type' => 'datetime', 'group' => 'fields' ],
                'remind_dtm'  => [ 'type' => 'datetime', 'group' => 'fields' ],
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Call'     => [
            'adapter' => 'wepo_company',
            'model'   => 'Call',
            'fields'  => [
                'type'           => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'label' ]
                ],
                'owner'          => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'table'          => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'table' ]
                ],
                'target'         => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Lead', 'fields' => [ 'login' ]
                ],
                'subject'        => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'call_type'      => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'call_purpose'   => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'call_detail'    => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'call_result'    => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'description'    => [ 'type' => 'textarea', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'bilable'        => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'call_start_dtm' => [ 'type' => 'detetime', 'group' => 'fields' ],
                'call_duration'  => [ 'type' => 'text', 'group' => 'fields' ],
                'changer'        => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'changed_dtm'    => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm'    => [ 'type' => 'datetime', 'group' => 'fields' ],
                'remind_dtm'     => [ 'type' => 'datetime', 'group' => 'fields' ],
            ],
            'groups'  => [
                'fields'
            ]
        ],
        'Task'     => [
            'adapter' => 'wepo_company',
            'model'   => 'Task',
            'fields'  => [
                'type'               => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'label' ]
                ],
                'owner'              => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'table'              => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Table', 'fields' => [ 'table' ]
                ],
                'target'             => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Lead', 'fields' => [ 'login' ]
                ],
                'subject'            => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'changer'            => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'User', 'fields' => [ 'login' ]
                ],
                'description'        => [ 'type' => 'textarea', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'priority'           => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'recurring'          => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'rec_startdate'      => [ 'type' => 'datetime', 'group' => 'fields' ],
                'rec_enddate'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'notification_email' => [ 'type' => 'text', 'group' => 'fields', 'max' => 100, 'required' => 1 ],
                'changed_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'created_dtm'        => [ 'type' => 'datetime', 'group' => 'fields' ],
                'due_dtm'            => [ 'type' => 'datetime', 'group' => 'fields' ],
                'remind_dtm'         => [ 'type' => 'datetime', 'group' => 'fields' ],
                'status'             => [
                    'type' => 'lookup', 'group' => 'fields', 'model' => 'Status', 'fields' => [ 'status' ]
                ]
            ],
            'groups'  => [
                'fields'
            ]
        ],

        //        'Order'       => [ ],
        //        'Pricebook'   => [ ],
        //        'Quote'       => [ ],
        //        'QuoteDetail' => [ ],
    ];


    protected function getConfigFromDb( $modelName )
    {
        $configData = $this->getGatewayServiceVerify()->get( 'ConfigData', new ConfigData() )
                           ->findOne( [ 'model' => $modelName ] );
        if ( $configData == null )
        {
            $configArray = Arr::getDoubtField( $this->_dbConfig, $modelName, null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown config for model ' . $modelName );
            }
            $configData = new ConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
            $this->getGatewayServiceVerify()->get( 'ConfigData', $configData )->save( $configData );
        }

        return $configData;
    }

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function getModelConfig( $modelName )
    {
        $configArray = Arr::getDoubtField( $this->_modelConfig, $modelName, null );

        if ( $configArray == null )
        {
            $configData = $this->getConfigFromDb( $modelName );
        }
        else
        {
            $configData = new ConfigData();
            $configData->exchangeArray( $configArray );
        }

        if ( $configData == null )
        {
            throw new \Exception( 'Can\'t find configuration for the ' . $modelName . 'model' );
        }

        return $configData;
    }

    /**
     * @param string $modelName
     *
     * @return Config
     * @throws \Exception
     */
    public function get( $modelName )
    {
        return $this->getModelConfig( $modelName );
    }

}