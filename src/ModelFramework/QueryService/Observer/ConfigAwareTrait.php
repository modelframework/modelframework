<?php
/**
 * Class ConfigAwareTrait
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

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