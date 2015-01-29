<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:07
 */

namespace ModelFramework\FormService\FormConfigParser;

use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfig\FormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\FormConfigAwareTrait;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareTrait;
use ModelFramework\FormService\FormConfigParser\Observer\AclObserver;
use ModelFramework\FormService\FormConfigParser\Observer\FieldsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\GroupsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\IdObserver;
use ModelFramework\FormService\FormConfigParser\Observer\InitObserver;
use ModelFramework\Utility\SplSubject\SplSubjectTrait;

class FormConfigParser
    implements FormConfigAwareInterface, \SplSubject,
               ParsedFormConfigAwareInterface, FieldTypesServiceAwareInterface
{

    use FormConfigAwareTrait, SplSubjectTrait, ParsedFormConfigAwareTrait, FieldTypesServiceAwareTrait;

    private $allowed_observers = [];

    public function init()
    {
        $this->attach(new InitObserver());
        $this->attach(new GroupsObserver());
        $this->attach(new IdObserver());
        $this->attach(new AclObserver());

        $fieldsObserver = new FieldsObserver();
        $fieldsObserver->setFieldTypesService($this->getFieldTypesServiceVerify());

        $this->attach($fieldsObserver);

        foreach (
            $this->getFormConfigVerify()->observers as $observer =>
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
