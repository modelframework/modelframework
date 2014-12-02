<?php
/**
 * Class LogicService
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\Custom\LogicConfig;
use ModelFramework\DataModel\Custom\LogicConfigData;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

class LogicService
    implements LogicServiceInterface, ConfigServiceAwareInterface, ModelConfigParserServiceAwareInterface, GatewayServiceAwareInterface
{

    use ConfigServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait;

//    public function get( $modelName )
//    {
//        $logicConfig = $this->getLogicConfigsServiceVerify()->get( $modelName );
//        $dataLogic   = new DataLogic();
//        $dataLogic->setLogicConfigData( $logicConfig );
//        $dataLogic->setServiceLocator( $this->getServiceLocator() );
//        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );
//        $dataLogic->setGatewayService( $this->getServiceLocator()->get( 'ModelFramework\GatewayService' ) );
//        $dataLogic->setModelConfigParserService( $this->getServiceLocator()
//                                                      ->get( 'ModelFramework\ModelConfigParserService' ) );
//
//        return $dataLogic;
//    }

    public function dispatch( $event )
    {
        $model = $event->getParams();
        if ( is_array( $model ) )
        {
            $model = array_shift( $model );
        }
        if ( $model instanceof DataModelInterface )
        {
            $modelName = $model->getModelName();
        }
        else
        {
            throw new \Exception( 'Event Params must be instance of DataModel' );
        }
        $dataLogic = $this->get( $modelName, $model );

        return call_user_func( [ $dataLogic, $event->getName() ], $event );
//        return call_user_func( [ $dataLogic, $event->getName() ], $event );
    }

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $eventObject
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function get( $eventName, $eventObject )
    {
        return $this->trigger( $eventName, $eventObject );
    }

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $eventObject
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function trigger( $eventName, $eventObject )
    {
        $model = $eventObject;
        if ( is_array( $eventObject ) )
        {
            $model = reset( $eventObject );
        }
        if ( $eventObject instanceof ResultSetInterface )
        {
            $model = $eventObject->getArrayObjectPrototype();
        }
        if ( !$model instanceof DataModelInterface )
        {
            throw new \Exception( 'Event Param must implement DataModelInterface ' );
        }
        $logicConfig = $this->getConfigServiceVerify()->getByObject( $model->getModelName() . '.' . $eventName,
                                                                     new LogicConfig() );
        if ( $logicConfig == null )
        {
            return null;
        }
        $logic = new Logic();
        $logic->setLogicConfig( $logicConfig );
        $logic->setEventObject( $eventObject );
        $logic->setModelConfigParserService( $this->getModelConfigParserServiceVerify() );
        $logic->setGatewayService( $this->getGatewayServiceVerify() );
        $logic->init();
        $logic->process();
//        $dataLogic->setServiceLocator( $this->getServiceLocator() );
//        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );

//
//        return $dataLogic;

    }

}
