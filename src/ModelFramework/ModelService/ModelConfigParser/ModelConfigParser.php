<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:07
 */

namespace ModelFramework\ModelService\ModelConfigParser;

use ModelFramework\ModelService\ModelConfig\ModelConfigAwareInterface;
use ModelFramework\ModelService\ModelConfig\ModelConfigAwareTrait;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareInterface;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareTrait;
use ModelFramework\ModelService\ModelConfigParser\Observer\AclObserver;
use ModelFramework\ModelService\ModelConfigParser\Observer\FieldsObserver;
use ModelFramework\ModelService\ModelConfigParser\Observer\GroupsObserver;
use ModelFramework\ModelService\ModelConfigParser\Observer\IdObserver;
use ModelFramework\ModelService\ModelConfigParser\Observer\InitObserver;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\SplSubject\SplSubjectTrait;

class ModelConfigParser
    implements ModelConfigAwareInterface, \SplSubject, ParsedModelConfigAwareInterface
{

    use ModelConfigAwareTrait, SplSubjectTrait, ParsedModelConfigAwareTrait;

    private $allowed_observers = [];

    public function init()
    {
        $this->attach( new InitObserver() );
        $this->attach( new GroupsObserver() );
        $this->attach( new IdObserver() );
        $this->attach( new AclObserver() );
        $this->attach( new FieldsObserver() );

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
                  = 'ModelFramework\ModelService\ModelConfigParer\Observer\\'
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
