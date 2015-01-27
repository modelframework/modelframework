<?php
/**
 * Class ModelConfigParserService
 *
 * @package ModelFramework\ModelConfigParserService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService\ModelConfigParserService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;
use ModelFramework\Utility\Arr;

class ModelConfigParser2Service
    implements ModelConfigParserServiceInterface,
               FieldTypesServiceAwareInterface, ConfigServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ConfigServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return array
     * @throws \Exception
     */
    public function getModelConfig($modelName)
    {
        $cd = $this->getConfigServiceVerify()
            ->getByObject($modelName, new ModelConfig());
        if ($cd == null) {
            throw new \Exception('Please fill ModelConfig for the ' . $modelName
                . '. I can\'t work on');
        }

        return $this->pullModelConfig($cd)->getParsedModelConfig();
    }

    /**
     * @param ModelConfig $cm
     *
     * @return array
     */
    public function pullModelConfig(ModelConfig $modelConfig)
    {

        $modelConfigParser = new ModelConfigParser();
        $modelConfigParser->setFieldTypesService($this->getFieldTypesServiceVerify());
        $modelConfigParser->setModelConfig($modelConfig);
        $modelConfigParser->init()->notify();

        return $modelConfigParser;
    }

    /**
     * Returns array with all registered models names
     *
     * @return array
     */
    public function getAllModelNames()
    {
        $models = [
            'User',
            'Mail',
            'Lead',
            'Patient',
            'Account',
            'Document',
            'Product',
            'Pricebook',
            'PricebookDetail',
            'Activity',
            'Quote',
            'QuoteDetail',
            'Order',
            'OrderDetail',
            'Invoice',
            'InvoiceDetail',
            'Payment',
            'EventLog',
            'Doctor',
        ];

        return $models;
    }


    /**
     * @param $model
     *
     * @return array
     * @throws \Exception
     */
    public function getAvailableIndexes($model)
    {
        $modelConfig = $this->getModelConfig($model);

        $indexes = [];
        foreach ($modelConfig['fields'] as $_key => $_field) {
            if ($_key == '_id' || $_field['datatype'] == 'array'
                || $_field['type'] == 'source'
            ) {
                continue;
            }
            /**
             * if we want to index all text field in text search
             */
            /**
             * if ($_field['type'] == 'field' && $_field['datatype'] == 'string') {
             * $indexes = Arr::put2ArrayKey($indexes, 'text', [$_key => 'text']);
             * }
             */
            if ($_key == 'title') {
                $indexes = Arr::put2ArrayKey($indexes, 'text',
                    [$_key => 'text']);
            }
            /**
             * end
             */
            if ($_field['fieldtype'] == 'textarea') {
                continue;
            }

            $indexes = Arr::put2ArrayKey(
                $indexes,
                'idx_' . $_key,
                [$_key => 1]
            );

        }

        return $indexes;
    }
}
