<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:10
 */

namespace ModelFramework\ModelService\ModelField\Strategy;

interface ModelFieldStrategyInterface
{

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setFieldConfig(array $config);
}
