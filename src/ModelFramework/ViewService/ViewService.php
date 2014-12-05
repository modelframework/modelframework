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
use ModelFramework\FormService\FormServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\LogicService\LogicServiceAwareInterface;
use ModelFramework\LogicService\LogicServiceAwareTrait;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelService\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\FormService\FormServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\ViewService\ViewConfig\ViewConfig;

class ViewService
    implements ViewServiceInterface, ConfigServiceAwareInterface, ModelConfigParserServiceAwareInterface,
               GatewayServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface,
               FormServiceAwareInterface, AuthServiceAwareInterface, LogicServiceAwareInterface,
               QueryServiceAwareInterface
{

    use ConfigServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait, AuthServiceAwareTrait, LogicServiceAwareTrait, QueryServiceAwareTrait;

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
        $viewConfig = $this->getConfigServiceVerify()->getByObject( $viewName, new ViewConfig() );
//      $viewConfig = $this->getViewConfigsServiceVerify()->get( $modelName, $viewName );

        if ( $viewConfig == null )
        {
            throw new \Exception( 'Please fill ViewConfig for the ' . $viewName . '. I can\'t work on' );
        }
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

        $view->setConfigService( $this->getConfigServiceVerify() );
        $view->setQueryService( $this->getQueryServiceVerify() );
//        $view->setDataMappingService( $this->getDataMappingServiceVerify() );

        // initialize stuff. observers as primary
        $view->init();

        return $view;
    }

} 