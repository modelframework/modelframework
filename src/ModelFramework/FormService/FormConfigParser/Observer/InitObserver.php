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

        $modelConfig = $subject->getModelConfigVerify();
        // init
        $formConfig    = [
            'name'            => $modelConfig->model . 'Form',
            'group'           => 'form',
            'type'            => 'form',
            'options'         => [
                'label'  => $modelConfig->model . ' information',
            ],
            'attributes'      => [
                'class'  => 'validate',
                'method' => 'post',
                'name'   => $modelConfig->model . 'form',
            ],
            // , 'action' => 'reg'
            'fieldsets'       => [],
            'elements'        => [],
            'filters'         => [],
            'validationGroup' => [],
        ];

        $subject->addParsedConfig( $formConfig );

    }
}
