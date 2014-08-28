<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:42 PM
 */

namespace ModelFramework\DataModel\Custom;


trait ViewConfigDataAwareTrait {

    /**
     * @var ViewConfigData
     */
    private $_viewConfig = null;

    /**
     * @param ViewConfigData $viewConfig
     *
     * @return $this
     */
    public function setViewConfigData( ViewConfigData $viewConfig )
    {
        $this->_viewConfig = $viewConfig;
    }

    /**
     * @return ViewConfigData
     *
     */
    public function getViewConfigData( )
    {
        return $this->_viewConfig;
    }

    /**
     * @return ViewConfigData
     * @throws \Exception
     */
    public function getViewConfigDataVerify( )
    {
        $viewConfig = $this->getViewConfigData();
        if ( $viewConfig==null || ! $viewConfig instanceof ViewConfigData )
        {
            throw new \Exception( 'View Config Data does not set set in ModelView' );
        }
        return $viewConfig;
    }
} 