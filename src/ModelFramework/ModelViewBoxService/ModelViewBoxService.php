<?php

/**
 * Class ModelViewBoxService
 * @package ModelFramework\ModelViewBoxService
 */

namespace ModelFramework\ModelViewBoxService;

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
use ModelFramework\ModelViewService\ModelViewServiceAwareInterface;
use ModelFramework\ModelViewService\ModelViewServiceAwareTrait;
use ModelFramework\ViewBoxConfigsService\ViewBoxConfigsServiceAwareInterface;
use ModelFramework\ViewBoxConfigsService\ViewBoxConfigsServiceAwareTrait;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareInterface;
use ModelFramework\ViewConfigsService\ViewConfigsServiceAwareTrait;

class ModelViewBoxService
    implements ModelViewBoxServiceInterface, ViewBoxConfigsServiceAwareInterface, ModelViewServiceAwareInterface
//    , ModelViewServiceAwareInterface
//    , GatewayServiceAwareInterface
//    , ViewConfigsServiceAwareInterface, ModelConfigParserServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface
//    , FormServiceAwareInterface, DataMappingServiceAwareInterface, AuthServiceAwareInterface
{

    use ModelViewServiceAwareTrait, ViewBoxConfigsServiceAwareTrait;

//        , GatewayServiceAwareTrait
//        , ViewConfigsServiceAwareTrait, ModelConfigParserServiceAwareTrait
//        , AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait
//        , DataMappingServiceAwareTrait, AuthServiceAwareTrait

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function getModelViewBox( $modelName, $viewName )
    {
        return $this->createViewBox( $modelName, $viewName );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    public function get( $modelName, $viewName )
    {
        return $this->getModelViewBox( $modelName, $viewName );
    }

    /**
     * @param string $modelName
     * @param string $viewName
     *
     * @return ModelViewBox|ModelViewBoxInterface
     * @throws \Exception
     */
    protected function createViewBox( $modelName, $viewName )
    {
        // this object will deal with all view of model stuff
        $modelViewBox = new ModelViewBox();

//        $modelView->setAuthService( $this->getAuthServiceVerify() );
//
//        // we want modelView get to know what to show and how
        $viewBoxConfigData = $this->getViewBoxConfigsServiceVerify()->get( $modelName, $viewName );
        $modelViewBox->setViewBoxConfigData( $viewBoxConfigData );

        $modelViewBox -> setModelViewService( $this->getModelViewServiceVerify() );

//        // config parser service
//        $modelView->setModelConfigParserService( $this->getModelConfigParserServiceVerify() );
//
//        // info about model - how it is organized. it will be useful
//        $modelConfigArray = $this->getModelConfigParserServiceVerify()->getModelConfig( $viewConfigData->model );
//        $modelView->setModelConfig( $modelConfigArray );
//
//        // model view should deal with acl enabled model
//        $aclModel = $this->getAclServiceVerify()->getAclModel( $viewConfigData->model );
//        // primary gateway for data ops
//        $gateway = $this->getGatewayServiceVerify()->get( $modelName, $aclModel );
//        $modelView->setGateway( $gateway );
//
//        // gateway service for queries
//        $modelView->setGatewayService( $this->getGatewayServiceVerify() );
//
//        // form service for form creation
//        $modelView->setFormService( $this->getFormServiceVerify() );
//
//        $modelView->setDataMappingService( $this->getDataMappingServiceVerify() );
//
//        // initialize stuff. observers as primary
//        $modelView->init();

        return $modelViewBox;
    }

} 