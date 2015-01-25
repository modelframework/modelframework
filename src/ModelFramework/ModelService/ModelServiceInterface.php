<?php

namespace ModelFramework\ModelService;

use ModelFramework\DataModel\DataModel;

interface ModelServiceInterface
{
    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function get($modelName);

    /**
     * @param string $modelName
     *
     * @return DataModel
     */
    public function getModel($modelName);

    /**
     * @param string $model
     *
     * @return array
     */
    public function makeIndexes($model);
}
