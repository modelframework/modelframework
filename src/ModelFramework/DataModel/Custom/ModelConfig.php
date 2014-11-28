<?php

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

/**
 * Class ConfigData
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelConfig extends DataModel
{
    public $_model = 'ModelConfig';
    public $_label = 'Model Config';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'     => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'label'   => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'adapter' => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'model'   => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'fields'  => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'groups'  => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'unique'  => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];
    protected $_joins = [ ];
    public $_unique   = [ 'model' ];

    public $_id     = '';
    public $label   = '';
    public $adapter = '';
    public $model   = '';
    public $fields  = [ ];
    public $groups  = [ ];
    public $unique  = [ ];

}