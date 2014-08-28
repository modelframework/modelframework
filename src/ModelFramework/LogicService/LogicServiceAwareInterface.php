<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 15.07.14
 * Time: 19:48
 */

namespace ModelFramework\LogicService;


interface LogicServiceAwareInterface {

    /**
     * @param LogicServiceInterface $logicService
     *
     * @return mixed
     */
    public function setLogicService( LogicServiceInterface $logicService );

    /**
     * @return LogicServiceInterface
     */
    public function getLogicService(  );

    /**
     * @return LogicServiceInterface
     * @throws \Exception
     */
    public function getLogicServiceVerify(  );


} 