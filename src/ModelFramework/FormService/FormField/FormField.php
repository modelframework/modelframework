<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:21
 */

namespace ModelFramework\FormService\FormField;

use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormField\FieldConfig\ParsedFieldConfigAwareInterface;
use ModelFramework\FormService\FormField\FieldConfig\ParsedFieldConfigAwareTrait;
use ModelFramework\FormService\FormField\Strategy\DefaultStrategy;
use ModelFramework\FormService\FormField\Strategy\EmailStrategy;
use ModelFramework\FormService\FormField\Strategy\FieldStrategy;
use ModelFramework\FormService\FormField\Strategy\LookupStrategy;
use ModelFramework\FormService\FormField\Strategy\FormFieldStrategyInterface;

class FormField
    implements FormFieldInterface, ParsedFieldConfigAwareInterface,
               FormFieldStrategyInterface, FieldTypesServiceAwareInterface
{

    use ParsedFieldConfigAwareTrait, FieldTypesServiceAwareTrait;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var FormFieldStrategyInterface
     */
    private $strategy = null;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        $this->getStrategy()->setName($name);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getStrategy()->getType();
    }

    /**
     * @param FormFieldStrategyInterface $strategy
     *
     * @return $this
     */
    protected function setStrategy(FormFieldStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @return FormFieldStrategyInterface
     */
    protected function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function chooseStrategy($type)
    {
        switch ($type) {
            case 'lookup':
            case 'jlookup':
            case 'static_lookup':
                $this->setStrategy(new LookupStrategy());
                break;
            case 'email':
                $this->setStrategy(new EmailStrategy());
                break;
            default:
                $this->setStrategy(new FieldStrategy());
        }
        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setFieldConfig($config)
    {
        $this->getStrategy()->setFieldConfig($config);
        return $this;
    }


    /**
     * @return FieldConfig\FieldConfigInterface
     */
    public function getFieldConfig()
    {
        return $this->getStrategy()->getFieldConfig();
    }

    /**
     * @return FieldConfig\FieldConfigInterface
     */
    public function getFieldConfigVerify()
    {
        return $this->getStrategy()->getFieldConfigVerify();
    }

    /**
     * @param array $aConfig
     *
     * @return FieldConfig\FieldConfigInterface
     * @throws \Exception
     */
    public function parseFieldConfigArray(array $aConfig)
    {
        return $this->getStrategy()->parseFieldConfigArray($aConfig);
    }

    /**
     * @param array|FieldTypeInterface $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setFieldType($aConfig)
    {
        return $this->getStrategy()->setFieldType($aConfig);
    }

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldTypeArray(array $aConfig)
    {
        return $this->getStrategy()->parseFieldTypeArray($aConfig);
    }

    /**
     * @return FieldTypeInterface
     */
    public function getFieldType()
    {
        return $this->getStrategy()->getFieldType();
    }

    /**
     * @return FieldTypeInterface
     * @throws \Exception
     */
    public function getFieldTypeVerify()
    {
        return $this->getStrategy()->getFieldTypeVerify();
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->setFieldType(
            $this
                ->getFieldTypesServiceVerify()
                ->getField($this->getType())
        );
        $this->getStrategy()->init();
        return $this;
    }


    /**
     * @return $this
     */
    public function parse()
    {
//        $config = $this->getStrategy()->parse();
        $this->addParsedConfig($this->getStrategy()->parse());
        return $this;
    }

}
