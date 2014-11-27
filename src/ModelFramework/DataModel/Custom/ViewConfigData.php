<?php
/**
 * Class ViewConfigData
 * @package ModelFramework\ViewConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

class ViewConfigData extends DataModel
{

    public $_model = 'ModelView';
    public $_label = 'Model View';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'       => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'document'      => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'title'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'observers' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'custom'    => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 0 ],
        'model'     => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'mode'      => [ 'type' => 'field', 'datatype' => 'string', 'default' => 'list' ],
        'template'  => [ 'type' => 'field', 'datatype' => 'string', 'default' => 'common/index.twig' ],
        'query'     => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'fields'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'params'    => [
            'type'    => 'field', 'datatype' => 'array',
            'default' => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ]
        ],
        'groups'    => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'rows'      => [ 'type' => 'field', 'datatype' => 'integer', 'default' => 10 ],
        'actions'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],

    ];
    protected $_joins = [ ];
    public $_unique = [ 'model' ];

    public $_id = '';
    public $document = '';
    public $custom = 0;
    public $model = '';
    public $mode = '';
    public $template = 'common/index.twig';
    public $query = [ ];
    public $fields = [ ];
    public $params = [ ];
    public $groups = [ ];
    public $actions = [ ];
    public $rows = 10;

}
