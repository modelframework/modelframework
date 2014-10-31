<?php

namespace ModelFramework\LogicService;

use ModelFramework\BaseService\ServiceLocatorAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class LogicService
    implements LogicServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $_logics = ['default' => '\ModelFramework\LogicService\DataLogic'];

    public function get( $model )
    {
        if ( is_array( $model ) )
        {
            $model = reset( $model );
        }

        $logicName = ucfirst($model->_model);
        $logic     = '\Wepo\Model\Logic\\' . $logicName;

        if ( !class_exists( $logic ) )
        {
            $this->_logics[ $logicName ] = $this->_logics[ 'default' ];
        }
        else
        {
            $this->_logics[ $logicName ] = $logic;
        }

        $dataLogic = new $this->_logics[$logicName]($logicName);

        $dataLogic->setServiceLocator( $this->getServiceLocator() );
        $dataLogic->setModelService( $this->getServiceLocator()->get( 'ModelFramework\ModelService' ) );
        $dataLogic->setGatewayService( $this->getServiceLocator()->get( 'ModelFramework\GatewayService' ) );
        $dataLogic->setModelConfigParserService($this->getServiceLocator()->get('ModelFramework\ModelConfigParserService'));

        return $dataLogic;
    }

    public function dispatch( $event )
    {
       return call_user_func( [ $this->get( $event->getParams() ), $event->getName() ], $event );
    }

}
