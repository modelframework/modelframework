<?php
/**
 * Class ViewConfigsService
 * @package ModelFramework\ViewConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewConfigsService;

use ModelFramework\DataModel\Custom\ViewConfigData;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Model\Status;

class ViewConfigsService implements ViewConfigsServiceInterface, GatewayServiceAwareInterface
{

    use GatewayServiceAwareTrait;

    /**
     * @var array
     */
    protected $_viewConfig = [

    ];

    /**
     * @var array
     */
    protected $_systemConfig = [

        'list' => [
            'rows' => [ 5, 10, 25, 50, 100 ]
        ],

    ];

    /**
     * @var array
     */
    protected $_dbConfig = [

        'Lead.list'        => [
            'observers' => [ 'ListObserver' ],
            'name'      => 'Lead.list',
            'title'     => 'Leads',
            'custom'    => 0,
            'model'     => 'Lead',
            'mode'      => 'list',
            'query'     => [
                'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
            ],
            'fields'    => [
                'owner_login',
                'owner_lname',
                'title',
                'fname',
                'lname',
                'phone',
                'mobile',
                'email',
                'birth_date',
                'changer_login',
                'changed_dtm',
                'created_dtm',
                'status_status'
            ],
            'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
            'groups'    => [ ],
            'rows'      => 10,
            'actions'   => [ 'convert' => 'Convert' ]
        ],
        'Lead.recyclelist' => [
            'observers' => [ 'ListObserver' ],
            'name'      => 'Lead.recyclelist',
            'title'     => 'Recycle: Leads',
            'custom'    => 0,
            'model'     => 'Lead',
            'mode'      => 'recyclelist',
            'query'     => [
                'status_id' => [ Status::DELETED ]
            ],
            'fields'    => [
                'owner_login',
                'fname',
                'lname',
                'phone',
                'mobile',
                'email',
                'birth_date',
                'changer_login',
                'changed_dtm',
                'created_dtm',
                'status_status'
            ],
            'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
            'groups'    => [ ],
            'rows'      => 10,
        ],
        'Lead.view'        => [
            'observers'           => [ 'ViewObserver' ],
            'name'                => 'Lead.view',
            'title'               => 'View Lead',
            'custom'              => 0,
            'model'               => 'Lead',
            'mode'                => 'view',
            'query'               => [
                'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
            ],
            'fields'              => [
                'owner_login',
                'fname',
                'lname',
                'phone',
                'mobile',
                'email',
                'birth_date',
                'changer_login',
                'changed_dtm',
                'created_dtm',
                'status_status'
            ],
            'params'              => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
            'groups'              => [ ],
            'rows'                => 10,
            'actions'             => [
                'convert'  => 'Convert',
//                'call'     => [
//                    'url'     => 'activity', 'action' => 'add', 'activity' => 'call', 'id' => '~model.id()',
//                    'tableid' => 'table_id', 'class' => 'call btn-icon', 'label' => 'Call'
//                ],
//                'task'     => [
//                    'url'     => 'activity', 'action' => 'add', 'activity' => 'task', 'id' => '~model.id()',
//                    'tableid' => 'table_id', 'class' => 'task btn-icon', 'label' => 'Task'
//                ],
//                'event'    => [
//                    'url'     => 'activity', 'action' => 'add', 'activity' => 'event', 'id' => '~model.id()',
//                    'tableid' => 'table_id', 'class' => 'event btn-icon', 'label' => 'Event'
//                ],
//                'document' => [
//                    'url'   => 'document', 'action' => 'add', 'id' => '~model.id()', 'tableid' => 'table_id',
//                    'class' => 'attach btn-icon', 'label' => 'Attach'
//                ],
//                'delete'   => [
//                    'url'   => 'lead', 'action' => 'delete', 'id' => '~model.id()', 'class' => 'delete btn-icon',
//                    'label' => 'Delete'
//                ],
//                'convert2' => [
//                    'url'   => 'lead', 'action' => 'convert', 'id' => '~model.id()', 'class' => 'convert btn-icon',
//                    'label' => 'Convert'
//                ]
                ]
            ],
            'Lead.add'            => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Lead.add',
                'title'     => 'Add Lead',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'add',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Lead.edit'           => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Lead.edit',
                'title'     => 'Edit Lead',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'edit',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Lead.convert'        => [
                'observers' => [ 'ConvertObserver' ],
                'name'      => 'Lead.convert',
                'title'     => 'Convert Lead',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'convert',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Lead.delete'         => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Lead.delete',
                'title'     => 'Delete Lead(s)',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'delete',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Lead.restore'        => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Lead.restore',
                'title'     => 'Restore Lead(s)',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'restore',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Lead.clean'          => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Lead.clean',
                'title'     => 'Clean Lead(s)',
                'custom'    => 0,
                'model'     => 'Lead',
                'mode'      => 'clean',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.list'        => [
                'observers' => [ 'ListObserver' ],
                'name'      => 'Patient.list',
                'title'     => 'Patients',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'list',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.recyclelist' => [
                'observers' => [ 'ListObserver' ],
                'name'      => 'Patient.recyclelist',
                'title'     => 'Recycle: Patients',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'recyclelist',
                'query'     => [
                    'status_id' => [ Status::DELETED ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.view'        => [
                'observers' => [ 'ViewObserver' ],
                'name'      => 'Patient.view',
                'title'     => 'View Patient',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'view',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.add'         => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Patient.add',
                'title'     => 'Add Patient',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'add',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.edit'        => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Patient.edit',
                'title'     => 'Edit Patient',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'edit',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.delete'      => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Patient.delete',
                'title'     => 'Delete Patient(s)',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'delete',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.restore'     => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Patient.restore',
                'title'     => 'Restore Patient(s)',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'restore',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Patient.clean'       => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Patient.clean',
                'title'     => 'Clean Patient(s)',
                'custom'    => 0,
                'model'     => 'Patient',
                'mode'      => 'clean',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'fname',
                    'lname',
                    'phone',
                    'mobile',
                    'email',
                    'birth_date',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10,
            ],
            'Account.list'        => [
                'observers' => [ 'ListObserver' ],
                'name'      => 'Account.list',
                'title'     => 'Accounts',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'list',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.add'        => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Account.add',
                'title'     => 'Add Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'add',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.view'        => [
                'observers' => [ 'ViewObserver' ],
                'name'      => 'Account.view',
                'title'     => 'View Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'view',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.edit'        => [
                'observers' => [ 'FormObserver' ],
                'name'      => 'Account.edit',
                'title'     => 'Edit Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'edit',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.recyclelist'        => [
                'observers' => [ 'ListObserver' ],
                'name'      => 'Account.recyclelist',
                'title'     => 'Recucle: Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'recyclelist',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.restore'        => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Account.restore',
                'title'     => 'Restore Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'restore',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
            'Account.clean'        => [
                'observers' => [ 'RecycleObserver' ],
                'name'      => 'Account.clean',
                'title'     => 'Clean Account',
                'custom'    => 0,
                'model'     => 'Account',
                'mode'      => 'clean',
                'query'     => [
                    'status_id' => [ Status::NEW_, Status::NORMAL, Status::CONVERTED, Status::DEAD ]
                ],
                'fields'    => [
                    'owner_login',
                    'title',
                    'phone',
                    'email',
                    'changer_login',
                    'changed_dtm',
                    'created_dtm',
                    'status_status'
                ],
                'params'    => [ 'rows' => 10, 'sort' => 'created_dtm', 'desc' => 1, 'q' => '' ],
                'groups'    => [ ],
                'rows'      => 10
            ],
        ];

    protected function getKeyName( $modelName, $viewName )
    {
        return $modelName . '.' . $viewName;
    }

    protected function getConfigFromDb( $modelName, $viewName )
    {

        $viewConfigData = $this->getGatewayServiceVerify()->getGateway( 'ModelView', new ViewConfigData() )->findOne(
            [ 'model' => $modelName, 'mode' => $viewName ]
        );
        if ( $viewConfigData == null )
        {
            $configArray = Arr::getDoubtField( $this->_dbConfig, $this->getKeyName( $modelName, $viewName ), null );
            if ( $configArray == null )
            {
                throw new \Exception( ' unknown view config for model ' . $modelName . ' in the view ' . $viewName );
            }
            $viewConfigData = new ViewConfigData( $configArray );
//            $configData->exchangeArray( $configArray );
//            $this->getGatewayServiceVerify()->get( 'ConfigData', $viewConfigData )->save( $viewConfigData );
        }

        return $viewConfigData;
    }

    /**
     * @param $modelName
     * @param $viewName
     *
     * @return ViewConfigData
     * @throws \Exception
     */
    public function getViewConfigData( $modelName, $viewName )
    {

        $viewConfigData = $this->getConfigFromDb( $modelName, $viewName );
        if ( $viewConfigData == null )
        {
            $viewConfigArray =
                Arr::getDoubtField( $this->_viewConfig, $this->getKeyName( $modelName, $viewName ), null );
            if ( $viewConfigArray !== null )
            {
                $viewConfigData = new ViewConfigData( $viewConfigArray );
            }
            else
            {
                throw new \Exception( 'Unknown view config for ' . $modelName . '.' . $viewName );
            }
        }

        return $viewConfigData;
    }

    public function get( $modelName, $viewName )
    {
        return $this->getViewConfigData( $modelName, $viewName );
    }
}