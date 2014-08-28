<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/30/14
 * Time: 8:54 PM
 */

namespace ModelFramework\ModelConfigsService;


interface ModelConfigsServiceAwareInterface {

    /**
     * @param ModelConfigsServiceInterface $modelConfigsService
     *
     * @return $this
     */
    public function setModelConfigsService(ModelConfigsServiceInterface $modelConfigsService);

    /**
     * @return ModelConfigsServiceInterface
     */
    public function getModelConfigsService();

    /**
     * @return ModelConfigsServiceInterface
     * @throws \Exception
     */
    public function getModelConfigsServiceVerify();

} 