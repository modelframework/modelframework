<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:29 PM
 */

namespace ModelFramework\DataModel\Custom;


interface ViewConfigDataAwareInterface {

    /**
     * @param ViewConfigData $viewConfig
     *
     * @return $this
     */
    public function setViewConfigData( ViewConfigData $viewConfig );

    /**
     * @return ViewConfigData
     */
    public function getViewConfigData( );

    /**
     * @return ViewConfigData
     * @throws \Exception
     */
    public function getViewConfigDataVerify( );
}