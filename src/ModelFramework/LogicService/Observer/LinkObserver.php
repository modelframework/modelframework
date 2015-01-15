<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class LinkObserver
    implements \SplObserver, SubjectAwareInterface, ConfigAwareInterface
{

    use SubjectAwareTrait;
    use ConfigAwareTrait;

    protected $linkSettings = [
        [
            '_id'    => 'unique_id1',
            'to'     => [
                'model'  => 'Mail',
                'update' => 'linkupdate',
                'search' => [
                    'to'             => [
                        'view'    => 'to_view',
                        'storage' => 'to_storage'
                    ],
                    'from'           => [
                        'view'    => 'from_view',
                        'storage' => 'from_link'
                    ],
                    'common_storage' => '_links'
                ],
            ],
            'from'   => [
                'Lead'    => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title' => [
                            'pre'  => '',
                            'post' => ' '
                        ],
                        'email' => [
                            'pre'  => '<',
                            'post' => '>'
                        ],
                    ]
                ],
                'Patient' => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title' => [
                            'pre'  => '',
                            'post' => ' '
                        ],
                        'email' => [
                            'pre'  => '<',
                            'post' => '>'
                        ],
                    ]
                ],
                'Account' => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title' => [
                            'pre'  => '',
                            'post' => ' '
                        ],
                        'email' => [
                            'pre'  => '<',
                            'post' => '>'
                        ],
                    ]
                ],
                'Doctor'  => [
                    'search'  => 'email',
                    'storage' => 'email_id',
                    'title'   => [
                        'title' => [
                            'pre'  => '',
                            'post' => ' '
                        ],
                        'email' => [
                            'pre'  => '<',
                            'post' => '>'
                        ],
                    ]
                ],
                'User'    => [
                    'search'  => 'login',
                    'storage' => 'email_id',
                    'title'   => [
                        'title' => [
                            'pre'  => '',
                            'post' => ' '
                        ],
                        'email' => [
                            'pre'  => '<',
                            'post' => '>'
                        ],
                    ]
                ],
            ],
            'models' => [
                'Lead',
                'Patient',
                'Account',
                'User',
                'Doctor'
            ],
        ],
    ];

    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        //todo check what is going when collector model updating or linking model.
        $this->setSubject( $subject );
        $models = $subject->getEventObject();
        if (!is_array( $models )) {
            $models = [ $models ];
        }
        $collectorConfigs = [ ];
        $linkConfigs      = [ ];
        try {
            $modelName = $models[ 0 ]->getModelName();
        } catch ( \Exception $ex ) {
            throw $ex;
        }
        array_walk( $this->linkSettings,
            function ( $config, $key ) use ( &$collectorConfigs, &$linkConfigs, $modelName ) {
                if (in_array( $modelName, $config[ 'models' ] )) {
                    $linkConfigs[ ] = $config;
                } elseif ($config[ 'to' ][ 'model' ] == $modelName) {
                    $collectorConfigs[ ] = $config;
                }
            } );



        foreach ($linkConfigs as $config) {
            //update as link model
            $searchValues = [ ];
            $linksId      = [ ];
            foreach ($models as $model) {
                switch ($this->getRootConfig()[ 'action' ]) {
                    case 'update':
                        list( $searchValues[ ], $linksId[ ] ) = $this->updateLink( $model, $config );
                        break;
                    case 'delete':
                        list( $searchValues[ ], $linksId[ ] ) = $this->deleteLink( $model, $config );
                        break;
                }
            }
            //todo trigger collector models by $linkId
            $this->updateCollectorModels( $config, $searchValues, $linksId );
        }


        foreach ($collectorConfigs as $config) {
            //update as collector model
            foreach ($models as $model) {
                switch ($this->getRootConfig()['action'])
                {
                    case 'update' :
//                        $link
                        break;
                    case 'sync':
                        break;
                }
                $searchFields = $model->$config[ 'to' ][ 'search' ];
                $searchValues = [ ];
                foreach ($searchFields as $field => $params) {
                    $searchValues = $model->$field;
                    if (!is_array( $model->$field )) {
                        array_push( $searchValues, $model->$field );
                    } else {
                        array_merge( $searchValues, $model->$field );
                    }
                }
                $searchValues = array_unique( $searchValues );
                $this->updateCollectorModels( $searchValues, $config );
            }
        }
        exit;
    }

    public function updateLink( $model, $config )
    {
        $modelName = $model->getModelName();
        $linkGw    = $this->getSubject()->getGatewayServiceVerify()->get( 'Link' );
        $link      = $linkGw->find( [
            'setting_id'    => $config[ '_id' ],
            'link_model'    => $modelName,
            'link_model_id' => $model->_id
        ] )->current();
        $link      = isset( $link ) ? $link : $this->getSubject()->getModelServiceVerify()->get( 'Link' );
        foreach ($config[ 'from' ][ $modelName ][ 'title' ] as $titleField => $param) {
            $value       = trim( $model->$titleField );
            $pre         = $param[ 'pre' ];
            $post        = $param[ 'post' ];
            $link->title = $link->title . $pre . $value . $post;
        }

        $link->setting_id      = $config[ '_id' ];
        $link->link_model      = $modelName;
        $link->link_model_id   = $model->_id;
        $link->_acl            = $model->_acl;
        $link->collector_model = $config[ 'to' ][ 'model' ];
        $link->search_value    = $model->$config[ 'from' ][ $modelName ][ 'search' ];

        $linkGw->save( $link );

        $id = !empty( $link->_id ) ? $link->_id : $linkGw->getLastInsertId();
        if (empty( $id )) {
            throw new \Exception( 'Sorry, saving link exception happened' );
        }
//        $model->$config[ 'from' ][ $modelName ][ 'storage' ] = $id;
        return [ $link->search_value, $id ];
//        return $id;
    }

    public function deleteLink( $model, $config )
    {
        $modelName = $model->getModelName();
        $linkGw    = $this->getSubject()->getGatewayServiceVerify()->get( 'Link' );
        $link      = $linkGw->find( [
            'setting_id'    => $config[ '_id' ],
            'link_model'    => $modelName,
            'link_model_id' => $model->_id
        ] )->current();

        $linkGw->delete( [ '_id' => $link->_id ] );

        return [ $link->search_value, $link->_id ];
//        return $link->_id;
    }

    public function updateCollectorModels( $config, $searchValues=null, $updatedLinks = null )
    {
//        if()
        //todo add implementation that works fine when linking model add or update and collector model sync or update
        //todo if updatedLinks == null then find links in db, else update collector models by updatedLinks
    }
}
