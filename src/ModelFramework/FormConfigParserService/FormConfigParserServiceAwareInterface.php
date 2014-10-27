<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 11:18 AM
 */

namespace ModelFramework\FormConfigParserService;


interface FormConfigParserServiceAwareInterface {

    /**
     * @param FormConfigParserServiceInterface $formConfigParserService
     *
     * @return $this
     */
    public function setFormConfigParserService( FormConfigParserServiceInterface $formConfigParserService );

    /**
     * @return FormConfigParserServiceInterface
     */
    public function getFormConfigParserService();

    /**
     * @return FormConfigParserServiceInterface
     * @throws \Exception
     */
    public function getFormConfigParserServiceVerify();

} 