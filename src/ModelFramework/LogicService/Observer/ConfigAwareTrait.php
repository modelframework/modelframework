<?php
/**
 * Created by PhpStorm.
 * User: PROG-3
 * Date: 02.12.2014
 * Time: 17:05
 */

namespace ModelFramework\LogicService\Observer;

trait ConfigAwareTrait
{

    private $_obConfig = null;

    public function setConfig( $config )
    {
        $this->_obConfig = $config;
    }

    public function getConfig( )
    {
        return $this->_obConfig;
    }

} 