<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

class LinkObserver
    implements \SplObserver, SubjectAwareInterface, ConfigAwareInterface
{

    use SubjectAwareTrait;
    use ConfigAwareTrait;

    protected $linkSettings = [
        [
            'to'     => [
                'model'       => 'Mail',
                'search'      => [
                    'to',
                    'from'
                ],
                'storage'     => 'link_storage',
                'title_field' => 'link_view'
            ],
            'from'   => [
                'Lead'    => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title',
                        'email'
                    ]
                ],
                'Patient' => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title',
                        'email'
                    ]
                ],
                'Account' => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title',
                        'email'
                    ]
                ]
            ],
            'models' => [
                'Lead',
                'Patient',
                'Account'
            ],
        ]
    ];

    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $models = $subject->getEventObject();
        if (!is_array( $models )) {
            $models = [ $models ];
        }
        foreach ($models as $model) {
            $configs = [ ];
            array_walk( $this->$linkSettings, function ( &$config, $key ) use ( &$configs, $model ) {
                if (in_array( $model->getModelName(), $config[ 'models' ] )) {
                    $configs[ ] = $config;
                }
            } );
            //update as link model
            switch ($this->getRootConfig()[ 'action' ]) {
                case 'update':
                    foreach ($configs as $config) {
                        $this->updateLinkAction( $model, $config );
                    }
                    break;
                case 'delete':
                    foreach ($configs as $config) {
                        $this->deleteLinkAction( $model, $config );
                    }
                    break;
            }
            //update as collector model
            switch ($this->getRootConfig()[ 'action' ]) {
                case 'update':
                    foreach ($configs as $config) {
                        $this->updateCollectorAction( $model, $config );
                    }
                    break;
                case 'delete':
                    foreach ($configs as $config) {
                        $this->deleteCollectorAction( $model, $config );
                    }
                    break;
            }
            exit;
        }
    }

    public function updateLinkAction( $model, $config )
    {

        prn( $this->linkSettings );
        exit;
    }

    public function deleteLinkAction( $model, $config )
    {
        prn( $this->linkSettings );
        exit;
    }

    public function updateCollectorAction( $model, $config )
    {

        prn( $this->linkSettings );
        exit;
    }

    public function deleteCollectorAction( $model, $config )
    {
        prn( $this->linkSettings );
        exit;
    }

}
