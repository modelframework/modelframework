<?php
/**
 * Class FormConfigParserServiceInterface
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

interface FormConfigParserServiceInterface
{
    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getFormConfig($modelName);
}
