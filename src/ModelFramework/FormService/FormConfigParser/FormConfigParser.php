<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:07
 */

namespace ModelFramework\FormService\FormConfigParser;

use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareTrait;
use ModelFramework\FormService\FormConfigParser\Observer\FieldsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\GroupsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\InitObserver;
use ModelFramework\ModelService\ModelConfig\ModelConfigAwareInterface;
use ModelFramework\Utility\SplSubject\SplSubjectTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfigAwareTrait;

class FormConfigParser
    implements \SplSubject, ParsedFormConfigAwareInterface,
               FieldTypesServiceAwareInterface, ModelConfigAwareInterface, AclConfigAwareInterface
{

    use SplSubjectTrait, ParsedFormConfigAwareTrait, FieldTypesServiceAwareTrait, ModelConfigAwareTrait, AclConfigAwareTrait;

    private $allowed_observers = [];


    private $_limitFields = [];

    /*
     * @param array $fields
     *
     * @return $this
     */
    public function setLimitFields(array $fields = [])
    {
        $this->_limitFields = $fields;
        return $this;
    }

    /*
     * @param array $fields
     *
     * @return $this
     */
    public function getLimitFields()
    {
        return $this->_limitFields;
    }

    public function init()
    {
        $this->setParsedFormConfig();
        $this->attach(new InitObserver());
        $this->attach(new GroupsObserver());
//        $this->attach(new IdObserver());
//        $this->attach(new AclObserver());

        $fieldsObserver = new FieldsObserver();
        $fieldsObserver->setFieldTypesService($this->getFieldTypesServiceVerify());
        $this->attach($fieldsObserver);

        foreach (
            $this->getModelConfigVerify()->observers as $observer =>
            $obConfig
        ) {
            if (is_numeric($observer)) {
                $observer = $obConfig;
                $obConfig = null;
            }
            if ( !in_array($observer, $this->allowed_observers)) {
                throw new \Exception($observer . ' is not allowed in ' .
                    get_class($this));
            }
            $observerClassName
                  = 'ModelFramework\FormService\FormConfigParer\Observer\\'
                . $observer;
            $_obs = new $observerClassName();
            if ( !empty($obConfig) && $_obs instanceof ConfigAwareInterface) {
                $_obs->setRootConfig($obConfig);
            }
            $this->attach($_obs);
        }

        return $this;
    }

}
