<?php

namespace ModelFramework\ModelViewService;


use Zend\Mvc\Controller\Plugin\Params;

interface ParamsAwareInterface {

    /**
     * @param Params $params
     *
     * @return $this
     */
    public function setParams( Params $params );

    /**
     * @return Params
     */
    public function getParams(  );

    /**
     * @return Params
     * @throws \Exception
     */
    public function getParamsVerify(  );

} 