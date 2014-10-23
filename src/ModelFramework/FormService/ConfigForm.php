<?php
/**
 * Class FormService
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService;

class ConfigForm
{

    public $_id             = '';
    public $name            = '';
    public $group           = '';
    public $type            = '';
    public $options         = [ ];
    public $attributes      = [ ];
    public $fieldsets       = [ ];
    public $elements        = [ ];
    public $validationGroup = [ ];

}