<?php
/**
 * Class DataMapping
 * @package ModelFramework\DataModel\Custom
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

class DataMapping extends DataModel
{

    public $_model = 'DataMapping';
    public $_label = 'Data Mapping';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'     => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'key'    => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'name'    => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'source'  => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
        'targets' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ],
    ];
    protected $_joins = [ ];
    public $_unique = [ 'model' ];

    public $_id = '';
    public $key = '';
    public $name = '';
    public $source = '';
    public $targets = [ ];

}
