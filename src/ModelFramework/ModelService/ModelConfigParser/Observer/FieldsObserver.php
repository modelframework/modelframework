<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\ModelService\ModelConfigParser\Observer;

use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;
use ModelFramework\ModelService\ModelField\ModelField;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class FieldsObserver implements \SplObserver
{

    public function update(\SplSubject $subject)
    {
        /** @var ModelConfigParser $subject */

        $modelConfig = $subject->getModelConfig();

        $config = [];
        // process fields
        foreach ($modelConfig->fields as $field_name => $field_conf) {
            $config = array_merge_recursive($config,
                $this->createField($field_name, $field_conf));
        }

        $subject->addParsedConfig($config);

    }

    protected function createField($name, $config)
    {
        $modelField = new ModelField();
        $modelField
            ->setName($name)
            ->chooseStrategy($config['type'])
            ->setFieldConfig($config);
//            ->init()
//            ->parse()
//        ;
        return $modelField->getParsedFieldConfig();
    }

    /**
     * @param string $name
     * @param array  $conf
     *
     * @return array
     */
    protected function createFieldOld($name, $conf)
    {

        $type       = $conf['type'];
        $_fieldconf = $this->getField($type);

        $_fieldsets          = [];
        $_joins              = [];
        $_fieldconf['label'] = isset($conf['label']) ? $conf['label']
            : ucfirst($name);
        $_labels             = [];

        if (in_array($type, ['static_lookup', 'lookup'])) {
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
        } else {
            if (isset($conf['group'])) {
                $_fieldsets[$conf['group']]['elements'][$name]
                                     = $_fieldconf['label'];
                $_fieldconf['group'] = $conf['group'];
            }
            //FIXME this does not work for lookup fields, only for source fields. Need update.
            $_fieldconf['default'] = isset($conf['default']) ? $conf['default']
                : '';
            $_fieldconf['source']  = $name;
            $_fields               = [$name => $_fieldconf];
            $_labels               = [$name => $_fieldconf['label']];

            $_utility = $this->getFieldPart($conf['type'], 'utility');
            if (count($_utility)) {
                $_fields = array_merge($_fields, $_utility);
            }
        }
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
