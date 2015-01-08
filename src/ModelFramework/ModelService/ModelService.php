<?php

namespace ModelFramework\ModelService;

use ModelFramework\DataModel\DataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;

/**
 * Class ModelService
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelService
    implements ModelServiceInterface, ModelConfigParserServiceAwareInterface
{
    use ModelConfigParserServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function get($modelName)
    {
        return $this->getModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel($modelName)
    {
        return $this->createModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    protected function createModel($modelName)
    {
        $modelConfig     = $this->getModelConfigParserServiceVerify()->getModelConfig($modelName);
        $model           = new DataModel();
        $model->_fields  = $modelConfig[ 'fields' ];
        $model->_model   = $modelConfig[ 'model' ];
        $model->_table   = $modelConfig[ 'table' ];
        $model->_label   = $modelConfig[ 'label' ];
        $model->_adapter = $modelConfig[ 'adapter' ];
        $model->exchangeArray([ ]);

        return $model;
    }
}
