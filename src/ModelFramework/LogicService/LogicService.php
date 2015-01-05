<?php
/**
 * Class LogicService
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use Mail\MailServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicConfig\LogicConfig;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

class LogicService
    implements LogicServiceInterface, ConfigServiceAwareInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface, ParamsAwareInterface,
               AuthServiceAwareInterface, ModelServiceAwareInterface
{

    use ConfigServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait,
        AuthServiceAwareTrait, ModelServiceAwareTrait, ParamsAwareTrait, MailServiceAwareTrait;

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

//        $dataLogic = $this->get( $modelName, $modelName ) -> trigger( $model );
        return $this->get( $event->getName(), $modelName )->trigger( $event );

//        return call_user_func( [ $dataLogic, $event->getName() ], $event );
    }

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function get( $eventName, $modelName )
    {
        return $this->createLogic( $eventName, $modelName );
    }

    /**
     * @param string $eventName
     * @param string $modelName
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function createLogic( $eventName, $modelName )
    {
        $logicConfig = $this->getConfigServiceVerify()->getByObject( $modelName . '.' . $eventName, new LogicConfig() );


        $logic = new Logic();
        if ( $logicConfig == null )
        {
            return $logic;
        }


        $logic->setLogicConfig( $logicConfig );

        $logic->setConfigService( $this->getConfigServiceVerify() );
        $logic->setModelConfigParserService( $this->getModelConfigParserServiceVerify() );
        $logic->setGatewayService( $this->getGatewayServiceVerify() );
        $logic->setAuthService( $this->getAuthServiceVerify() );
        $logic->setModelService( $this->getModelService() );
        $logic->setLogicService( $this );
        $logic->setMailService( $this->getMailService() );
        if ( $this->getParams() != null )
        {
            $logic->setParams( $this->getParams() );
        }
        $logic->init();

        return $logic;

    }

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $eventObject
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function trigger__b00bs( $eventName, $eventObject )
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
        $logic->setAuthService( $this->getAuthServiceVerify() );
        $logic->setModelService( $this->getModelService() );
        $logic->setLogicService( $this );
        if ( $this->getParams() != null )
        {
            $logic->setParams( $this->getParams() );
        }
        $logic->init();
        $logic->process();
//        $dataLogic->setServiceLocator( $this->getServiceLocator() );
//        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );

//
//        return $dataLogic;

    }

}
