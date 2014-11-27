<?php

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

/**
 * Class ConfigData
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class LogicConfigData extends DataModel
{

    public $_model = 'LogicConfigData';
    public $_label = 'Logic Config Data';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'   => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'model' => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'rules' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];

    public $_id = '';
    public $model = '';
    public $rules = [ ];

}