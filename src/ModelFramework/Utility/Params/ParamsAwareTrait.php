<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 8/1/14
 * Time: 4:15 PM
 */

namespace ModelFramework\Utility\Params;


use Zend\Mvc\Controller\Plugin\Params;

trait ParamsAwareTrait {

    private $_params = null;

    /**
     * @param Params $params
     *
     * @return $this
     */
    public function setParams( Params $params )
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * @return Params
     */
    public function getParams(  )
    {
        return $this->_params;
    }

    /**
     * @return Params
     * @throws \Exception
     */
    public function getParamsVerify(  )
    {
        $params = $this->getParams();
        if ( $params === null || !$params instanceof Params )
        {
            throw new \Exception('Params does not set in the ParamsAware instance of '. get_class( $this ) );
        }

        return $params;
    }


    public function getParam( $name, $default = '' )
    {
        $param = $this->getParamsVerify()->fromQuery( $name, $default );
        if ( $param === $default )
        {
            $param = $this->getParamsVerify()->fromRoute( $name, $default );
        }

        return $param;
    }
}