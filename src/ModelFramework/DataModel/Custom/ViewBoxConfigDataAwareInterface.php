<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/29/14
 * Time: 6:29 PM
 */

namespace ModelFramework\DataModel\Custom;


interface ViewBoxConfigDataAwareInterface {

    /**
     * @param ViewBoxConfigData $viewBoxConfigData
     *
     * @return $this
     */
    public function setViewBoxConfigData( ViewBoxConfigData $viewBoxConfigData );

    /**
     * @return ViewBoxConfigData
     */
    public function getViewBoxConfigData( );

    /**
     * @return ViewBoxConfigData
     * @throws \Exception
     */
    public function getViewBoxConfigDataVerify( );
}