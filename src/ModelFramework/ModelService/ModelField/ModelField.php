<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:21
 */

namespace ModelFramework\ModelService\ModelField;

use ModelFramework\ModelService\ModelField\FieldConfig\ParsedFieldConfigAwareInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\ParsedFieldConfigAwareTrait;
use ModelFramework\ModelService\ModelField\Strategy\DefaultStrategy;
use ModelFramework\ModelService\ModelField\Strategy\LookupStrategy;
use ModelFramework\ModelService\ModelField\Strategy\ModelFieldStrategyInterface;

class ModelField implements ModelFieldInterface, ParsedFieldConfigAwareInterface, ModelFieldStrategyInterface
{

    use ParsedFieldConfigAwareTrait;

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var ModelFieldStrategyInterface
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
     * @param ModelFieldStrategyInterface $strategy
     *
     * @return $this
     */
    protected function setStrategy(ModelFieldStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @return ModelFieldStrategyInterface
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
            case 'static_lookup':
                $this->setStrategy(new LookupStrategy());
                break;
            default:
                $this->setStrategy(new DefaultStrategy());
        }
        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setFieldConfig(array $config)
    {
        prn($this->getStrategy());
        $this->getStrategy()->setFieldConfig($config);
        return $this;
    }

    /**
     * @return $this
     */
    public function init()
    {
        return $this;
    }

    /**
     *
     */
    public function parse()
    {
        return $this;
    }

}
