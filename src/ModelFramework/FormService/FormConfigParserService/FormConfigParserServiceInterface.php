<?php
/**
 * Class FormConfigParserServiceInterface
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService\FormConfigParserService;

use ModelFramework\DataModel\DataModelAwareInterface;

interface FormConfigParserServiceInterface extends DataModelAwareInterface
{
//    /**
//     * @param string $modelName
//     *
//     * @return array
//     */
//    public function getFormConfig($modelName);

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function limitFields( array $fields = []);

    /**
     * @return mixed
     */
    public function parse( );

}
