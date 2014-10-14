<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 4:59 PM
 */

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

/**
 * Class ViewConfigData
 * @package ModelFramework\ViewConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ViewConfigData extends DataModel
{

    public $_model = 'ModelView';
    public $_label = 'Model View';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'    => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'name'   => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'observers'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [] ],
        'custom' => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 0 ],
        'model'  => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'mode'   => [ 'type' => 'field', 'datatype' => 'string', 'default' => 'list' ],
        'query'  => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'fields' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'params' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ 'rows'=>10, 'sort'=>'created_dtm', 'desc'=>1, 'q' => '' ] ],
        'groups' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'rows'   => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 10 ],
    ];
    protected $_joins = [ ];
    public $_unique = [ 'model' ];

    public $_id = '';
    public $name = '';
    public $custom = 0;

    public $model = '';
    public $mode = '';
    public $query = [ ];
    public $fields = [ ];
    public $params = [ ];
    public $groups = [ ];
    public $rows = 10;

}
