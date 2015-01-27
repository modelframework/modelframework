<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\ModelService\ModelField\Strategy;

use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfig;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigAwareInterface;
use ModelFramework\ModelService\ModelField\FieldConfigAwareTrait;

class DefaultStrategy
    implements ModelFieldStrategyInterface, FieldConfigAwareInterface
{

    use FieldConfigAwareTrait;

    /**
     * @param array $aConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFieldConfigArray(array $aConfig)
    {
        $fieldConfig = new FieldConfig();
        $fieldConfig->exchangeArray($aConfig);
        return $fieldConfig;
    }
}
