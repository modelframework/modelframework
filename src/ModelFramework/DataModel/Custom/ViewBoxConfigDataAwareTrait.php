<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:42 PM
 */

namespace ModelFramework\DataModel\Custom;


trait ViewBoxConfigDataAwareTrait {

    /**
     * @var ViewBoxConfigData
     */
    private $_viewBoxConfig = null;

    /**
     * @param ViewBoxConfigData $viewBoxConfig
     *
     * @return $this
     */
    public function setViewBoxConfigData( ViewBoxConfigData $viewBoxConfig )
    {
        $this->_viewBoxConfig = $viewBoxConfig;
    }

    /**
     * @return ViewBoxConfigData
     *
     */
    public function getViewBoxConfigData( )
    {
        return $this->_viewBoxConfig;
    }

    /**
     * @return ViewBoxConfigData
     * @throws \Exception
     */
    public function getViewBoxConfigDataVerify( )
    {
        $viewBoxConfig = $this->getViewBoxConfigData();
        if ( $viewBoxConfig==null || ! $viewBoxConfig instanceof ViewBoxConfigData )
        {
            throw new \Exception( 'View Config Data does not set set in ModelView' );
        }
        return $viewBoxConfig;
    }
} 