<?php

namespace ModelFramework\LogicService;

use ModelFramework\BaseService\AbstractService;
use ModelFramework\DataModel\Custom\ConfigData;
use ModelFramework\DataModel\Custom\ModelConfigAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\GatewayService\MongoGateway;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareInterface;
use ModelFramework\ModelConfigsService\ModelConfigsServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use Wepo\Model\Email;
use Wepo\Model\Status;
use Wepo\Model\Table;

class DataLogic extends AbstractService
{
    use ModelServiceAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait;

    static public $adapter = 'wepo_company';
    private $_event = null;
    protected $_rules = null;
    protected $_modelName = null;

    public function __construct( $modelName )
    {
        $this->_modelName = $modelName;
    }

    protected function setEvent( $event )
    {
        $this->_event = $event;

        return $this;
    }

    protected function getEvent()
    {
        return $this->_event;
    }

    /**
     * @param string $gatewayName
     *
     * @return MongoGateway
     */
    public function getGateway( $gatewayName )
    {
        return $this -> getGatewayService() -> get( $gatewayName );
    }

    /**
     * @param string $modelName
     * @return Array
     */
    public function getModelConfig( $modelName )
    {
        return $this -> getModelConfigParserService() -> getModelConfig( $modelName );
    }

//    /**
//     * @param string $modelName
//     *
//     * @return ModelService
//     */
//    public function getModel( $modelName )
//    {
//        return $this->getServiceLocator()->get( 'ModelFramework\ModelService' )->get( $modelName );
//    }

//    protected function getModel()
//    {
//        return $this->_event->getParams();
//    }

    protected function getEventObjects()
    {
        return $this->_event->getParams();
    }

    protected function getController()
    {
        return $this->_event->getTarget();
    }

    protected function getAction()
    {
        return $this->getController()->params( 'action' );
    }

    protected function getRules( $ruletype )
    {
        $_rules = array();
        if ( isset( $this->_rules[ $ruletype ] ) )
        {
            $_rules = $this->_rules[ $ruletype ];
        }

        return $_rules;
    }

    public function fillJoins()
    {
        $models = $this->getEventObjects();
        $modelConfig = $this->getModelConfig($this->_modelName);

        if ( !is_array( $models ) )
        {
            $models = [ $models ];
        }
        foreach ( $models as $mymodel )
        {
            foreach ( $modelConfig['joins'] as $_k => $join )
            {
                $othergw = $this->getGateway( $join[ 'model' ] );
                foreach ( $join[ 'on' ] as $myfield => $otherfield )
                {
                    $othermodel = $othergw->find( [ $otherfield => $mymodel->$myfield ] )->current();
                    if ( $othermodel !== null )
                    {
                        foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                        {
                            $mymodel->$myfield = $othermodel->$otherfield;
                        }
                    }
                    else
                    {
                        foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                        {
                            unset( $mymodel->$myfield );
                        }
                    }
                }
            }
        }
    }

    public function fillJoinsConvert( $model )
    {
        ////////////////////////////////////////////////////
        $modelConfig = $this->getModelConfig($model->_model);

        $mymodel = $model;
        foreach ( $modelConfig['joins'] as $_k => $join )
        {
            $othergw = $this->getGateway( $join[ 'model' ] );
            foreach ( $join[ 'on' ] as $myfield => $otherfield )
            {
                $othermodel = $othergw->find( [ $otherfield => $mymodel->$myfield ] )->current();
                if ( $othermodel !== null )
                {
                    foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                    {
                        $mymodel->$myfield = $othermodel->$otherfield;
                    }
                }
                else
                {
                    foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                    {
                        unset( $mymodel->$myfield );
                    }
                }
            }
        }
        ////////////////////////////////////////////////////
    }

    public function formatFields()
    {
        $models = $this->getEventObjects();
        if ( !is_array( $models ) )
        {
            $models = [ $models ];
        }
        foreach ( $models as $mymodel )
        {
            //TODO Add field type functionality
            foreach ( $mymodel->toArray() as $_name => $_value )
            {
                if ( $_name == 'mobile' || $_name == 'phone' )
                {
                    $_value = preg_replace( '/[^0-9]/', '', $_value );
                    $phone  = '';
                    $length = strlen( $_value );
                    if ( $length >= 7 )
                    {
                        for ( $i = strlen( $_value ) - 1; $i >= 0; $i-- )
                        {
                            switch ( $length - $i )
                            {
                                case 0:
                                case 1:
                                case 2:
                                case 3:
                                    $phone = $_value[ $i ] . $phone;
                                    break;
                                case 4:
                                    $phone = '-' . $_value[ $i ] . $phone;
                                    break;
                                case 5:
                                case 6:
                                    $phone = $_value[ $i ] . $phone;
                                    break;
                                case 7:
                                    $phone = $_value[ $i ] . $phone;
                                    break;
                                case 8:
                                    $phone = $_value[ $i ] . ') ' . $phone;
                                    break;
                                case 9:
                                    $phone = $_value[ $i ] . $phone;
                                    break;
                                case 10:
                                    $phone = ' (' . $_value[ $i ] . $phone;
                                    break;
                            }
                            if ( $length - $i > 10 && $length - $i < 14 )
                            {
                                $phone = $_value[ $i ] . $phone;
                            }
                        }
                        if ( $length > 10 )
                        {
                            $phone = '+' . $phone;
                        }
                    }
                    $mymodel->$_name = $phone;
                }
            }
        }
    }

    public function presave( $event )
    {
        $this->setEvent( $event )->forge();
        $this->formatFields();
        $this->fillJoins();
    }

    public function update( $event )
    {
        $this->setEvent( $event );
        $model = $this->getEventObjects();
        $this->fillJoins();
    }

    public function postsave( $event )
    {
        $this->setEvent( $event );
        $this->saveLog();
    }

    public function prerecycle( $event )
    {
        $this->setEvent( $event );
    }

    public function recycle( $event )
    {
        $this->setEvent( $event );
        $ids = [ ];
        if ( !is_array( $this->getEventObjects() ) )
        {
            $modelname = $this->getEventObjects()->getModelName();
            $ids[ ]    = $this->getEventObjects()->id();
        }
        else
        {
            $models = $this->getEventObjects();
            foreach ( $models as $model )
            {
                $ids[ ] = $model->id();
            }
            $modelname = reset( $models )->getModelName();
        }
        switch ( $this->getAction() )
        {
            case 'restore':
                $this->getGateway( $modelname )->update( [
                                                                         'status'    => Status::getLabel( Status::NORMAL ),
                                                                         'status_id' => Status::NORMAL
                                                                     ], [ '_id' => $ids ] );
                break;
            case 'delete':
                $this->getGateway( $modelname )->update( [
                                                                         'status'    => Status::getLabel( Status::DELETED ),
                                                                         'status_id' => Status::DELETED
                                                                     ], [ '_id' => $ids ] );
                break;
            case 'clean':
                $this->getGateway( $modelname )->delete( [ '_id' => $ids ] );
                break;
        }
    }

    public function postrecycle( $event )
    {
        $this->setEvent( $event );
        $this->saveLog();
    }

    public function convert( $event )
    {
        $this->setEvent( $event );
    }

    public function payment( $event )
    {
        $this->setEvent( $event );
    }

    protected function forge( )
    {
        $model = $this->getEventObjects();
        foreach ( $this->getRules( $this->getAction() ) as $_key => $_rules )
        {
            if ( !isset( $model->$_key ) )
            {
                continue;
//                throw new \Exception( 'wrong field name ' . "'$_key'" );
            }
            if ( $_rules[ 'type' ] == 'function' && method_exists( $this, $_rules[ 'value' ] ) )
            {
                $this->{$_rules[ 'value' ]}( $model, $_key, isset( $_rules[ 'params' ] ) ? $_rules[ 'params' ] : null );
            }
            elseif ( $_rules[ 'type' ] == 'const' )
            {
                $model->$_key = $_rules[ 'value' ];
            }
        }

        return $this->getEventObjects();
    }

    protected function setDate( $model, $key, $params = null )
    {
        if ( empty( $model->$key ) )
        {
            if ( $params != null )
            {
                $model->$key = date( $params );
            }
            else
            {
                $model->$key = date( 'Y-m-d H:i:s' );
            }
        }

        return $model->$key;
    }

    protected function setOwner( $model, $key )
    {
        if ( empty( $model->$key ) )
        {
            $model->$key = $this->getController()->User()->id();
        }

        return $model->$key;
    }

    protected function updateEmails( $model, $key )
    {
        $nameRules = \Wepo\Model\Email::getLinkRules();
        $modelName = $model->getModelName();

        $tr         = $this->getController()->table( 'Email' );
        $foundEmail = $tr->findOne( [ 'email' => $model->$nameRules[ $modelName ][ 'email' ] ] );

        $newName = '';
        foreach ( $nameRules[ $modelName ][ 'name' ] as $value )
        {
            $newName = $newName . $model->$value . ' ';
        }
        $newName = trim( $newName );

        if ( isset( $foundEmail ) )
        {

            $oldNamePriority = $nameRules[ $foundEmail->user_name_source ][ 'priority' ];
            $newNamePriority = $nameRules[ $modelName ][ 'priority' ];

            if ( $newNamePriority > $oldNamePriority )
            {
                $foundEmail->user_name        = $newName;
                $foundEmail->user_name_source = $modelName;
            }
        }
        else
        {
            $foundEmail = new \Wepo\Model\Email( [
                                                     'email'            => $model->email,
                                                     'user_name'        => $newName,
                                                     'user_name_source' => $modelName,
                                                 ] );
        }
        $tr->save( $foundEmail );

        return $model->$key;
    }


    protected function setEmailId( $model, $key )
    {
        $nameRules = \Wepo\Model\Email::getLinkRules();
        $modelName = $model->getModelName();

        $tr         = $this->getController()->table( 'Email' );
        $foundEmail = $tr->findOne( [ 'email' => $model->$nameRules[ $modelName ][ 'email' ] ] );

        $model->$key = $foundEmail->id();

        return $model->$key;
    }

    protected function setUser( $model, $key )
    {
        return $model->$key = $this->getController()->User()->id();
    }

    protected function setClientIp( $model, $key )
    {
        return $model->$key = $this->getController()->getClientIp();
    }

    protected function saveLog()
    {
        //temporary commit for test other functionality
        //code below doesn't work, method getEventObjects have no sense, check it

//        $models = $this->getEventObjects();
//        if ( !is_array( $models ) )
//        {
//            $models = [ $models ];
//        }
//        foreach ( $models as $model )
//        {
//            if ( $model->id() == 0 )
//            {
//                $arr = $model->toArray();
//                foreach ( $arr as $_key => $_value )
//                {
//                    if ( $_value == null || is_array( $_value ) )
//                    {
//                        unset( $arr[ $_key ] );
//                    }
//                }
//                $model = $this->getGateway( $model->getModelName() )->find( $arr )->current();
//            }
//            if ( $model != null )
//            {
//                $eventlog = $this->getEventObjects( 'EventLog' );
//                $this->setDate( $eventlog, 'event_dtm' );
//                $this->setUser( $eventlog, 'executor_id' );
//                $eventlog->table_id  = Table:: getTableId( $model->getModelName() ); //. strtoupper( $model -> getModelName() ) );
//                $eventlog->target_id = $model->id();
//                $eventlog->event_id  = constant( '\Wepo\Model\EventType::' . strtoupper( $this->getAction() ) );
//                $this->fillJoinsConvert( $eventlog );
//                $this->getGateway( 'EventLog' )->save( $eventlog );
//            }
//        }
    }

    public function looked( $model, $key )
    {
        if ( $model->$key == Status::NEW_ )
        {
            $modelname   = $model->getModelName();
            $model->$key = Status::NORMAL;
            $id          = $model->owner_id;
            $user        = $this->getGateway( 'User' )->find( array( '_id' => $id ) )->current();
            $newItems    = $user->newitems;
            if ( (int) $newItems[ $modelname ] > 0 )
            {
                $newItems[ $modelname ] = (int) $newItems[ $modelname ] - 1;
            }
            $this->getGateway( 'User' )->update( [ 'newitems' => $newItems ], [ '_id' => $id ] );
        }

        return $model->$key;
    }

    public function newItem( $model, $key )
    {
        $modelname   = $model->getModelName();
        $model->$key = Status::NEW_;
        $id          = $model->owner_id;
        $user        = $this->getGateway( 'User' )->find( array( '_id' => $id ) )->current();
        $newItems    = $user->newitems;

        $newItems[ $modelname ] = (int) $newItems[ $modelname ] + 1;
        $this->getGateway( 'User' )->update( [ 'newitems' => $newItems ], [ '_id' => $id ] );

        return $model->$key;
    }

    public function setAvatar()
    {
        $user     = $this->getEventObjects();
        $request  = $this->getController()->getRequest();
        $filename = false;
        if ( $request->isPost() )
        {
            $files    = $request->getFiles();
            $fileinfo = $files->get( 'fields' )[ 'avatar-file' ];
            $fs       = $this->getController()->getServiceLocator()->get( '\Wepo\Lib\FileService' );
            $filename = $fs->saveFile( $fileinfo[ 'name' ], $fileinfo[ 'tmp_name' ], true, $user->id() );
        }
        if ( $filename )
        {
            $user->avatar = basename( $filename );
        }
    }

}
