<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:42 PM
 */

namespace ModelFramework\DataModel\Custom;


trait ViewConfigAwareTrait {

    /**
     * @var ViewConfig
     */
    private $_viewConfig = null;

    /**
     * @param ViewConfig $viewConfig
     *
     * @return $this
     */
    public function setViewConfig( ViewConfig $viewConfig )
    {
        $this->_viewConfig = $viewConfig;
    }

    /**
     * @return ViewConfig
     *
     */
    public function getViewConfig( )
    {
        return $this->_viewConfig;
    }

    /**
     * @return ViewConfig
     * @throws \Exception
     */
    public function getViewConfigVerify( )
    {
        $viewConfig = $this->getViewConfig();
        if ( $viewConfig==null || ! $viewConfig instanceof ViewConfig )
        {
            throw new \Exception( 'View Config Data does not set set in ModelView' );
        }
        return $viewConfig;
    }
} 