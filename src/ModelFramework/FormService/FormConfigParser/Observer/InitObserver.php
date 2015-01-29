<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class InitObserver implements \SplObserver
{
    public function update(\SplSubject $subject) {
        /** @var FormConfigParser $subject */

        $modelConfig = $subject->getFormConfig();

        // init
        $config = [
            'fields'    => [],
            'joins'     => [],
            //            'unique'       => [ ],
            'adapter'   => $modelConfig->adapter,
            'model'     => $modelConfig->model,
            'label'     => $modelConfig->label,
            'table'     => $modelConfig->table,
            'fieldsets' => [],
            'unique'    => $modelConfig->unique,
        ];

        $subject->addParsedConfig( $config );

    }
}
