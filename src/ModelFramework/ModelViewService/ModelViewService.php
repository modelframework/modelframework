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
use ModelFramework\DataMappingService\DataMappingServiceAwareInterface;
use ModelFramework\DataMappingService\DataMappingServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareInterface;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareTrait;

class ModelViewService
    implements ModelViewServiceInterface, ViewConfigsServiceAwareInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface,
               FormServiceAwareInterface, DataMappingServiceAwareInterface, AuthServiceAwareInterface
{

    use ViewConfigsServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, DataMappingServiceAwareTrait, AuthServiceAwareTrait;

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

        $modelView->setAuthService( $this -> getAuthServiceVerify() );

        // we want modelView get to know what to show and how
        $viewConfigData = $this->getViewConfigsServiceVerify()->get( $modelName, $viewName );
        $modelView->setViewConfigData( $viewConfigData );

        // info about model - how it is organized. it will be useful
        $modelConfigArray = $this->getModelConfigParserService()->getModelConfig( $modelName );
        $modelView->setModelConfig( $modelConfigArray );

        // model view should deal with acl enabled model
        $aclModel = $this->getAclServiceVerify()->getAclModel( $modelName );
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