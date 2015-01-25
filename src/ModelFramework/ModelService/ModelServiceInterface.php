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
     * Returns array with all registered models names
     * @return array
     */
    public function getAllModelNames();
}
