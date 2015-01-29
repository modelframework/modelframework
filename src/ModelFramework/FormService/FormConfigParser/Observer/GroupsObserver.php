<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class GroupsObserver implements \SplObserver
{

    public function update(\SplSubject $subject)
    {
        /** @var FormConfigParser $subject */

        $modelConfig = $subject->getFormConfig();

        $config = [];
        // process groups
        foreach ($modelConfig->groups as $_grp => $_fls) {
            if (is_numeric($_grp)) {
                $_grp = $_fls;
                $_baseFieldSet = $_grp == 'fields';
                $_fls = [
                    'label' => $modelConfig->model . ' information'
                ];
            } else {
                $_baseFieldSet
                    = isset($_fls ['base']) && $_fls ['base'] == true;
            }
            $_fls['elements'] = [];
            $_fls['base']     = [$_baseFieldSet];

            $config ['fieldsets'] [$_grp] = $_fls;
        }

        $subject->addParsedConfig($config);

    }

}
