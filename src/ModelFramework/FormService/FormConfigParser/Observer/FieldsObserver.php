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
use ModelFramework\FormService\ModelField\ModelField;
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

        $modelConfig = $subject->getFormConfig();

        $config = [];
        // process fields
        foreach ($modelConfig->fields as $field_name => $field_conf) {
            $config = Arr::merge($config, $this->createField($field_name, $field_conf));
        }

        $subject->addParsedConfig($config);

    }

    protected function createField($name, $config)
    {
        $subject = $this->getSubject();

        $modelField = new ModelField();
        $modelField
            ->chooseStrategy($config['type'])
            ->setFieldTypesService($subject->getFieldTypesServiceVerify())
            ->setName($name)
            ->setFieldConfig($config)
            ->init()
            ->parse();

        return $modelField->getParsedFieldConfig();
    }

}
