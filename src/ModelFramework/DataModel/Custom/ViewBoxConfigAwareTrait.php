<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:42 PM
 */

namespace ModelFramework\DataModel\Custom;


trait ViewBoxConfigAwareTrait {

    /**
     * @var ViewBoxConfig
     */
    private $_viewBoxConfig = null;

    /**
     * @param ViewBoxConfig $viewBoxConfig
     *
     * @return $this
     */
    public function setViewBoxConfig( ViewBoxConfig $viewBoxConfig )
    {
        $this->_viewBoxConfig = $viewBoxConfig;
    }

    /**
     * @return ViewBoxConfig
     *
     */
    public function getViewBoxConfig( )
    {
        return $this->_viewBoxConfig;
    }

    /**
     * @return ViewBoxConfig
     * @throws \Exception
     */
    public function getViewBoxConfigVerify( )
    {
        $viewBoxConfig = $this->getViewBoxConfig();
        if ( $viewBoxConfig==null || ! $viewBoxConfig instanceof ViewBoxConfig )
        {
            throw new \Exception( 'View Config Data does not set set in ModelView' );
        }
        return $viewBoxConfig;
    }
} 