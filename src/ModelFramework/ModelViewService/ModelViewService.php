<?php

/**
 * Class ModelViewService
 * @package ModelFramework\ModelViewService
 */

namespace ModelFramework\ModelViewService;

use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
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
               GatewayServiceAwareInterface, AclServiceAwareInterface, ModelServiceAwareInterface, FormServiceAwareInterface
{

    use ViewConfigsServiceAwareTrait, ModelConfigParserServiceAwareTrait, GatewayServiceAwareTrait, AclServiceAwareTrait, ModelServiceAwareTrait, FormServiceAwareTrait;

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
        $modelView = new ModelView();

        $viewConfigData   = $this->getViewConfigsServiceVerify()->get( $modelName, $viewName );
        $modelConfigArray = $this->getModelConfigParserService()->getViewConfig( $modelName );

        $modelView->setViewConfigData( $viewConfigData );
        $modelView->setModelConfig( $modelConfigArray );

        $aclModel = $this->getAclServiceVerify()->getAclModel( $modelName );
        $gateway  = $this->getGatewayServiceVerify()->get( $modelName, $aclModel );

        $modelView->setGatewayService( $this->getGatewayServiceVerify() );
        $modelView->setFormService( $this->getFormServiceVerify() );

        $modelView->setGateway( $gateway );
        $modelView->init();

        return $modelView;
    }

} 