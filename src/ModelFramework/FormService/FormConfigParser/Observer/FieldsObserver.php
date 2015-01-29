<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;
use ModelFramework\FormService\FormField\FormField;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class FieldsObserver implements \SplObserver, SubjectAwareInterface,
                                FieldTypesServiceAwareInterface
{

    use SubjectAwareTrait, FieldTypesServiceAwareTrait;

    public function update(\SplSubject $subject)
    {
        /** @var FormConfigParser $subject */
        $this->setSubject($subject);

        $modelConfig = $subject->getModelConfigVerify();

        $config = [];
        // process fields
        foreach ($modelConfig->fields as $field_name => $field_conf) {
            $config = Arr::merge($config,
                $this->createField($field_name, $field_conf));
        }

        $subject->addParsedConfig($config);

    }

    protected function createField($name, $config)
    {
        $subject = $this->getSubject();

        $modelField = new FormField();
        $modelField
            ->chooseStrategy($config['type'])
            ->setFieldTypesService($subject->getFieldTypesServiceVerify())
            ->setName($name)
            ->setFieldConfig($config)
            ->init()
            ->parse();

        return $modelField->getParsedFieldConfig();
    }

    protected function s($name, $conf)
    {
        $type = $conf['type'];
//        $_elementConf                         = $this->_fieldtypes[ $type ][ 'formElement' ];
        $_elementConf                     = $this->getFieldTypesServiceVerify()
            ->getFormElement($type);
        $filter                           = $this->getFieldTypesServiceVerify()
            ->getInputFilter($type);
        $filter['name']                   = $name;
        $_elementConf['options']['label'] = isset($conf['label'])
            ? $conf['label'] : ucfirst($name);
        if ($type == 'lookup') {
            $name .= '_id';
            $filter['name'] = $name;
            $_where         = ['status_id' => [Status::NEW_, Status::NORMAL]];
            $_order         = $conf['fields'];
            $_fields        = array_keys($conf['fields']);
            $_mask          = null;
            if (isset($conf['query']) && strlen($conf['query'])) {
                $query   = $this->getQueryServiceVerify()->get($conf['query'])
                    ->process();
                $_where  = $query->getWhere();
                $_order  = $query->getOrder();
                $_fields = $query->getFields();

                $_mask = $query->getFormat('label');
            }

            $_lAll    = $this->getGatewayServiceVerify()->get($conf['model'])
                ->find($_where, $_order);
            $_options = [];
            foreach ($_lAll as $_lRow) {
                $_lLabel = '';
                $_lvalue = $_lRow->id();

                if ($_mask !== null && strlen($_mask)) {
                    $_vals = [];
                    foreach ($_fields as $field) {
                        $_vals[$field] = $_lRow->$field;
                    }
                    $_lLabel = vsprintf($_mask, $_vals);
                } else {
                    foreach ($_fields as $_k) {
                        if (strlen($_lLabel)) {
                            $_lLabel .= '  [ ';
                            $_lLabel .= $_lRow->$_k;
                            $_lLabel .= ' ] ';
                        } else {
                            $_lLabel .= $_lRow->$_k;
                        }
                    }
                }
                $_options[$_lvalue] = $_lLabel;
            }
            $_elementConf['options']['value_options'] += $_options;
        }

        if ($type == 'static_lookup') {
            $name .= '_id';
            $filter['name'] = $name;
            $_lAll          = $this->getConfigService()
                ->get('StaticDataSource', $conf['model'],
                    new StaticDataConfig());
            $_options       = [];
            foreach ($_lAll->options as $_key => $_lRow) {
                $_lLabel = $_lRow[$_lAll->attributes['select_field']];
                $_lvalue = $_key;

                $_options[$_lvalue] = $_lLabel;
            }
            if (isset($conf['default'])) {
                $_elementConf['options']['value_options'] = $_options;
//                $_elementConf[ 'attributes' ][ 'value' ]      = $conf[ 'default' ];
            } else {
                $_elementConf['options']['value_options'] += $_options;
            }
            $_elementConf['options']['label']
                = $conf['fields'][$_lAll->attributes['select_field']];
        }

        $_elementConf['attributes']['name'] = $name;
        if (isset($conf['required'])) {
            $_elementConf['attributes']['required'] = 'required';
            if (isset($_elementConf['options']['label_attributes']['class'])
                && strlen($_elementConf['options']['label_attributes']['class'])
            ) {
                $_elementConf['options']['label_attributes']['class'] .= ' required';
            } else {
                $_elementConf['options']['label_attributes']
                    = ['class' => 'required'];
            }
        }

        $result = [
            'filters'  => [$name => $filter],
            'elements' => [$name => $_elementConf]
        ];

//        $result = [ $name => $_elementConf ];

        return $result;

    }

}
