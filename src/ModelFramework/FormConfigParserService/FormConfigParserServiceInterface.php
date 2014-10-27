<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.10.14
 * Time: 15:57
 */

namespace ModelFramework\FormConfigParserService;


interface FormConfigParserServiceInterface {

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getFormConfig( $modelName );
}
