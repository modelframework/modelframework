<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:58
 */

namespace ModelFramework\ModelService\ModelField\FieldConfig;

class LookupConfig implements FieldConfigInterface
{

    public $type = '';
    public $group = '';
    public $max = 0;
    public $required = 0;
    public $label = '';
    public $model = '';
    public $query = '';
    public $fields = [];
    public $default = 0;

    /**
     * @param array $a
     *
     * @return array
     */
    public function exchangeArray(array $a)
    {
        $this->type     = (isset($a['type'])) ? $a['type'] : '';
        $this->group    = (isset($a['group'])) ? $a['group'] : '';
        $this->max      = (isset($a['max'])) ? $a['max'] : 0;
        $this->required = (isset($a['required'])) ? $a['required'] : 0;
        $this->label    = (isset($a['label'])) ? $a['label'] : '';

        $this->model    = (isset($a['model'])) ? $a['model'] : '';
        $this->query    = (isset($a['query'])) ? $a['query'] : '';
        $this->fields    = (isset($a['fields'])) ? $a['fields'] : [];
        $this->default    = (isset($a['default'])) ? $a['default'] : '';

    }

    public function s()
    {
        $type       = $conf['type'];
        $_fieldconf = $this->getField($type);

        $_fieldsets          = [];
        $_joins              = [];
        $_fieldconf['label'] = isset($conf['label']) ? $conf['label']
            : ucfirst($name);
        $_labels             = [];

        $_sign       = '_';
        $_joinfields = [];
        $_i          = 0;
        $_fields     = [];
        foreach ($conf['fields'] as $_jfield => $_jlabel) {
            if ( !$_i++) {
                $_fieldconf['alias'] = $name . $_sign . $_jfield;
            }
            $_fields[$name . $_sign . $_jfield]     = [
                'type'      => 'alias',
                'fieldtype' => 'alias',
                'datatype'  => 'string',
                'default'   => '',
                'source'    => $name . '_id',
                'label'     => $_jlabel,
                'source'    => $name,
                'group'     => isset($conf['group']) ? $conf['group']
                    : 'fields',
            ];
            $_labels[$name . $_sign . $_jfield]     = $_jlabel;
            $_joinfields[$name . $_sign . $_jfield] = $_jfield;
            if (isset($conf['group'])) {
                $_fieldsets[$conf['group']]['elements'][$name . $_sign
                . $_jfield]
                                     = $_jlabel;
                $_fieldconf['group'] = $conf['group'];
            }
        }
        $_joins[]               = [
            'model'  => $conf['model'],
            'on'     => [$name . '_id' => '_id'],
            'fields' => $_joinfields,
            'type'   => $type,
        ];
        $_fieldconf['source']   = $name;
        $_fieldconf['default']  = isset($conf['default']) ? $conf['default']
            : '';
        $_fields[$name . '_id'] = $_fieldconf;
        $_labels[$name . '_id'] = $_jlabel;
        $name .= '_id';

        $_infilter = $this->getInputFilter($type);
        if (isset($conf['required'])) {
            $_infilter['required'] = true;
        }
        $_infilter['name'] = $name;
        $_filters          = [$name => $_infilter];

        $result = [
            'labels'    => $_labels,
            'fields'    => $_fields,
            'filters'   => $_filters,
            'joins'     => $_joins,
            'fieldsets' => $_fieldsets,
        ];

        return $result;
    }

}
