<?php

namespace ModelFramework\LogicService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\Custom\LogicConfig;
use ModelFramework\DataModel\Custom\LogicConfigData;
use ModelFramework\DataModel\DataModelInterface;

class LogicService
    implements LogicServiceInterface, ConfigServiceAwareInterface
{

    use ConfigServiceAwareTrait;

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
     * @param array|DataModelInterface $model
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function get( $eventName, $model )
    {
        return $this->trigger( $eventName, $model );
    }

    /**
     * @param string                   $eventName
     * @param array|DataModelInterface $model
     *
     * @return DataLogic|void
     * @throws \Exception
     */
    public function trigger( $eventName, $model )
    {
        $oModel = $model;
        if ( is_array( $model ) )
        {
            $oModel = reset( $model );
        }

        if ( !$oModel instanceof DataModelInterface )
        {
            throw new \Exception( 'Event Param must implement DataModelInterface ' );
        }

        $logicConfig = $this->getConfigServiceVerify()->getByObject( $oModel->getModelName() . '.' . $eventName,
                                                                     new LogicConfig() );

        if ( $logicConfig == null )
        {
            return null;
        }

        $dataLogic = new DataLogic();
        $dataLogic->setLogicConfig( $logicConfig );
        $dataLogic->process();
//        $dataLogic->setServiceLocator( $this->getServiceLocator() );
//        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );
//        $dataLogic->setGatewayService( $this->getServiceLocator()->get( 'ModelFramework\GatewayService' ) );
//        $dataLogic->setModelConfigParserService( $this->getServiceLocator()
//                                                      ->get( 'ModelFramework\ModelConfigParserService' ) );
//
//        return $dataLogic;

    }

}
