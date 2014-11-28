<?php

/**
 * Class ModelViewService
 * @package ModelFramework\ModelViewService
 */

namespace ModelFramework\ModelViewService;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigsService\ConfigsServiceAwareInterface;
use ModelFramework\ConfigsService\ConfigsServiceAwareTrait;
use ModelFramework\DataMappingService\DataMappingServiceAwareInterface;
use ModelFramework\DataMappingService\DataMappingServiceAwareTrait;
use ModelFramework\DataModel\Custom\ViewBoxConfigDataAwareInterface;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\ViewBoxConfigsService\ViewBoxConfigsServiceAwareTrait;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareInterface;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareTrait;

class ModelViewService
    implements ModelViewServiceInterface, ViewConfigsServiceAwareInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface,
               FormServiceAwareInterface, DataMappingServiceAwareInterface, AuthServiceAwareInterface, LogicServiceAwareInterface
{

    use ViewConfigsServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, DataMappingServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait;

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function getModelView( $modelName, $viewName )
    {
        return $this->createView( $modelName, $viewName );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName )
    {
        return $this->getModelView( $modelName, $viewName );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelView|ModelViewInterface
     * @throws \Exception
     */
    protected function createView( $modelName, $viewName )
    {
        // this object will deal with all view of model stuff
        $modelView = new ModelView();

        $modelView->setAuthService( $this->getAuthServiceVerify() );
        $modelView->setLogicService( $this->getLogicServiceVerify() );

        // we want modelView get to know what to show and how
//        $viewConfigData = $this->getConfigsServiceVerify()->get( 'ViewConfig', $modelName, new ViewConfig() );
        $viewConfigData = $this->getViewConfigsServiceVerify()->get( $modelName, $viewName );
        $modelView->setViewConfigData( $viewConfigData );

        // config parser service
        $modelView->setModelConfigParserService( $this->getModelConfigParserServiceVerify() );

        // info about model - how it is organized. it will be useful
        $modelConfigArray = $this->getModelConfigParserServiceVerify()->getModelConfig( $viewConfigData->model );
        $modelView->setModelConfig( $modelConfigArray );

        // model view should deal with acl enabled model
        $aclModel = $this->getAclServiceVerify()->getAclModel( $viewConfigData->model );
        // primary gateway for data ops
        $gateway = $this->getGatewayServiceVerify()->get( $modelName, $aclModel );
        $modelView->setGateway( $gateway );

        // gateway service for queries
        $modelView->setGatewayService( $this->getGatewayServiceVerify() );

        // form service for form creation
        $modelView->setFormService( $this->getFormServiceVerify() );

        $modelView->setDataMappingService( $this->getDataMappingServiceVerify() );

        // initialize stuff. observers as primary
        $modelView->init();

        return $modelView;
    }

} 