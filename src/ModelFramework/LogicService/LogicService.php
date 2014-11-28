<?php

namespace ModelFramework\LogicService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use ModelFramework\DataModel\DataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\LogicConfigsService\LogicConfigsServiceAwareInterface;
use ModelFramework\LogicConfigsService\LogicConfigsServiceAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class LogicService
    implements LogicServiceInterface, ServiceLocatorAwareInterface, LogicConfigsServiceAwareInterface
{

    use ServiceLocatorAwareTrait, LogicConfigsServiceAwareTrait;

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
        if ( $model instanceof DataModel )
        {
            $modelName = $model->getModelName();
        }
        else
        {
            throw new \Exception( 'Event Params must be instance of DataModel' );
        }
        $dataLogic = $this->get( $modelName );

        call_user_func( [ $dataLogic, $event->getName() ], $event );
        exit;
//        return call_user_func( [ $dataLogic, $event->getName() ], $event );
    }

    /**
     * @param array|\ModelFramework\DataModel\DataModelInterface $eventName
     * @param                                                    $model
     *
     * @return DataLogic|void
     */
    public function get( $eventName, $model )
    {
        return $this->trigger( $eventName, $model );
    }

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

        $logicConfig = $this->getLogicConfigsServiceVerify()->get( $oModel->getModelName() . '.' . $eventName );

        if ( $logicConfig == null )
        {
            return null;
        }


        prn('$logicConfig', $logicConfig );
//        $dataLogic   = new DataLogic();
//        $dataLogic->setLogicConfigData( $logicConfig );
//        $dataLogic->setServiceLocator( $this->getServiceLocator() );
//        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );
//        $dataLogic->setGatewayService( $this->getServiceLocator()->get( 'ModelFramework\GatewayService' ) );
//        $dataLogic->setModelConfigParserService( $this->getServiceLocator()
//                                                      ->get( 'ModelFramework\ModelConfigParserService' ) );
//
//        return $dataLogic;

        $model->title = 'booboo';
    }

}
