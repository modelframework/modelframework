<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 15.07.14
 * Time: 19:50
 */

namespace ModelFramework\LogicService;

trait LogicServiceAwareTrait
{

    /**
     * @var LogicServiceInterface
     */
    private $_logicService = null;

    /**
     * @param LogicServiceInterface $logicService
     *
     * @return mixed
     */
    public function setLogicService( LogicServiceInterface $logicService )
    {
        $this->_logicService = $logicService;

        return $this;
    }

    /**
     * @return $this
     */
    public function getLogicService()
    {
        return $this->_logicService;
    }

    /**
     * @return LogicServiceInterface
     * @throws \Exception
     */
    public function getLogicServiceVerify()
    {
        $_logicService = $this->getLogicService();
        if ( $_logicService == null || !$_logicService instanceof LogicServiceInterface )
        {
            throw new \Exception( 'LogicService does not set in the LogicServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $_logicService;

    }
} 