<?php

/**
 * Class ViewBoxService
 * @package ModelFramework\ViewBoxService
 */

namespace ModelFramework\ViewBoxService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\Custom\ViewBoxConfig;
use ModelFramework\ViewService\ViewServiceAwareInterface;
use ModelFramework\ViewService\ViewServiceAwareTrait;

class ViewBoxService
    implements ViewBoxServiceInterface, ConfigServiceAwareInterface, ViewServiceAwareInterface, AuthServiceAwareInterface
{

    use ViewServiceAwareTrait, ConfigServiceAwareTrait, AuthServiceAwareTrait;

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function getViewBox( $viewBoxName )
    {
        return $this->createViewBox( $viewBoxName );
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function get( $viewBoxName )
    {
        return $this->getViewBox( $viewBoxName );
    }

    /**
     * @param string $viewBoxName
     *
     * @return ViewBox|ViewBoxInterface
     * @throws \Exception
     */
    public function createViewBox( $viewBoxName )
    {
        // this object will deal with all view of model stuff
        $viewBox = new ViewBox();

        $viewBoxConfig = $this->getConfigServiceVerify()->getByObject( $viewBoxName, new ViewBoxConfig() );

        $viewBox->setViewBoxConfig( $viewBoxConfig );

        $viewBox->setViewService( $this->getViewServiceVerify() );
        $viewBox->setAuthService( $this->getAuthServiceVerify() );

        return $viewBox;
    }

} 