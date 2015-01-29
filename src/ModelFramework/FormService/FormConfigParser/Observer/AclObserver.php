<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:40
 */

namespace ModelFramework\FormService\FormConfigParser\Observer;

use ModelFramework\FormService\FormConfigParser\FormConfigParser;

class AclObserver implements \SplObserver
{

    public function update(\SplSubject $subject)
    {
        /** @var FormConfigParser $subject */
        $config = [];
        // add primary key _id

        $config['fields'] = [
            '_acl' => [
                'type'      => 'field',
                'fieldtype' => 'array',
                'datatype'  => 'array',
                'default'   => [],
                'label'     => 'acl',
                'source'    => '_acl',
            ]
        ];

        $subject->addParsedConfig($config);

    }

}
