<?php
/**
 * Class ViewBoxConfigData
 * @package ModelFramework\ViewBoxConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\DataModel\Custom;

use ModelFramework\DataModel\DataModel;

class ViewBoxConfigData extends DataModel
{

    public $_model = 'ModelViewBox';
    public $_label = 'Model ViewBox';
    public $_adapter = 'wepo_company';

    public $_fields = [
        '_id'      => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '' ],
        'document' => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'title'    => [ 'type' => 'field', 'datatype' => 'string', 'default' => '' ],
        'blocks'   => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ] ]
    ];

    protected $_joins = [ ];
    public $_unique = [ 'document' ];

    public $_id = '';
    public $document = '';
    public $title = '';
    public $blocks = [ ];

}