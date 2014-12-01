<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:29 PM
 */

namespace ModelFramework\DataModel\Custom;


interface ViewConfigAwareInterface {

    /**
     * @param ViewConfig $viewConfig
     *
     * @return $this
     */
    public function setViewConfig( ViewConfig $viewConfig );

    /**
     * @return ViewConfig
     */
    public function getViewConfig( );

    /**
     * @return ViewConfig
     * @throws \Exception
     */
    public function getViewConfigVerify( );
}