<?php

/**
 * Class ViewService
 * @package ModelFramework\ViewService
 */

namespace ModelFramework\ViewService;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\Custom\ViewConfig;
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

class ViewService
    implements ViewServiceInterface, ConfigServiceAwareInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface,
               FormServiceAwareInterface, AuthServiceAwareInterface, LogicServiceAwareInterface
{

    use ConfigServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait;

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function getView( $viewName )
    {
        return $this->createView( $viewName );
    }

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    public function get( $viewName )
    {
        return $this->getView( $viewName );
    }

    /**
     * @param string $viewName
     *
     * @return View|ViewInterface
     * @throws \Exception
     */
    protected function createView( $viewName )
    {
        // this object will deal with all view of model stuff
        $view = new View();

        $view->setAuthService( $this->getAuthServiceVerify() );
        $view->setLogicService( $this->getLogicServiceVerify() );

        // we want modelView get to know what to show and how
        prn( $viewName );
        $viewConfig = $this->getConfigServiceVerify()->get( 'ViewConfig', $viewName, new ViewConfig() );
//      $viewConfig = $this->getViewConfigsServiceVerify()->get( $modelName, $viewName );
        $view->setViewConfig( $viewConfig );

        // config parser service
        $view->setModelConfigParserService( $this->getModelConfigParserServiceVerify() );

        // info about model - how it is organized. it will be useful
        $modelConfigArray = $this->getModelConfigParserServiceVerify()->getModelConfig( $viewConfig->model );
        $view->setModelConfig( $modelConfigArray );

        // model view should deal with acl enabled model
        $aclModel = $this->getAclServiceVerify()->getAclModel( $viewConfig->model );
        // primary gateway for data ops
        $gateway = $this->getGatewayServiceVerify()->get( $viewConfig->model, $aclModel );
        $view->setGateway( $gateway );

        // gateway service for queries
        $view->setGatewayService( $this->getGatewayServiceVerify() );

        // form service for form creation
        $view->setFormService( $this->getFormServiceVerify() );

        $view -> setConfigService( $this->getConfigServiceVerify() );
//        $view->setDataMappingService( $this->getDataMappingServiceVerify() );

        // initialize stuff. observers as primary
        $view->init();

        return $view;
    }

} 