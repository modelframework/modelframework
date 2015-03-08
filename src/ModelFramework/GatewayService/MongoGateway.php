<?php

namespace ModelFramework\GatewayService;

use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareInterface;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfigAwareTrait;
use MongoZend\Db\TableGateway\MongoGateway as MongoZendGateway;

class MongoGateway extends MongoZendGateway
    implements GatewayInterface, ParsedModelConfigAwareInterface
{

    use ParsedModelConfigAwareTrait;

    /**
     * @param DataModelInterface|array $model
     *
     * @return array|bool|int|mixed
     * @throws \Exception
     */
    public function save($model)
    {
        if ($model instanceof DataModelInterface) {
            if ( !$this->isUnique($model)) {
                throw new \Exception('Data is not unique');
            }
            $data = $model->getArrayCopy();
            $result = parent::save($data);
            $model->_id = $data['_id'];
            return $result;
        } elseif (is_array($model)) {
            return parent::save($model);
        }

        return 0;
    }

    /**
     * @param DataModelInterface $model
     *
     * @return bool|mixed
     */
    public function isUnique(DataModelInterface $model)
    {
        $modelConfig = $this->getParsedModelConfig();
        if ($modelConfig !== null) {
            foreach ($modelConfig->unique as $_unique) {
                $_data = [];
                foreach ((array)$_unique as $_key) {
                    $_data[$_key] = $model->$_key;
                }
                $check = $this->find($_data);
                if ($check->count() > 0
                    && $check->current()->id() != $model->id()
                ) {
                    return false;
                }
            }
        }

        return true;
    }

}
