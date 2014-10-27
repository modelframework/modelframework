<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.10.14
 * Time: 15:52
 */

namespace ModelFramework\FormConfigParserService;


class FormConfigParserService
 implements FormConfigParserServiceInterface, FieldTypesServiceAwareInterface, ModelConfigsServiceAwareInterface
{
    use FieldTypesServiceAwareTrait, ModelConfigsServiceAwareTrait;

} 