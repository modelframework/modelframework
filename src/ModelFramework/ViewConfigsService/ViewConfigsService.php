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

        'Lead.list'           => [
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
        'Lead.recyclelist'    => [
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
        'Lead.view'           => [
            'observers' => [ 'ViewObserver' ],
            'name'      => 'Lead.view',
            'title'     => 'View Lead',
            'custom'    => 0,
            'model'     => 'Lead',
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
            'actions'   => [ 'convert' => 'Convert' ]
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
        'Contact.list'        => [
            'observers' => [ 'ListObserver' ],
            'name'      => 'Contact.list',
            'title'     => 'Contacts',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.recyclelist' => [
            'observers' => [ 'ListObserver' ],
            'name'      => 'Contact.recyclelist',
            'title'     => 'Recycle: Contacts',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.view'        => [
            'observers' => [ 'ViewObserver' ],
            'name'      => 'Contact.view',
            'title'     => 'View Contact',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.add'         => [
            'observers' => [ 'FormObserver' ],
            'name'      => 'Contact.add',
            'title'     => 'Add Contact',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.edit'        => [
            'observers' => [ 'FormObserver' ],
            'name'      => 'Contact.edit',
            'title'     => 'Edit Contact',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.delete'      => [
            'observers' => [ 'RecycleObserver' ],
            'name'      => 'Contact.delete',
            'title'     => 'Delete Contact(s)',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.restore'     => [
            'observers' => [ 'RecycleObserver' ],
            'name'      => 'Contact.restore',
            'title'     => 'Restore Contact(s)',
            'custom'    => 0,
            'model'     => 'Contact',
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
        'Contact.clean'       => [
            'observers' => [ 'RecycleObserver' ],
            'name'      => 'Contact.clean',
            'title'     => 'Clean Contact(s)',
            'custom'    => 0,
            'model'     => 'Contact',
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
        ]
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
                throw new \Exception( ' unknown config for model ' . $modelName . ' view ' . $viewName );
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