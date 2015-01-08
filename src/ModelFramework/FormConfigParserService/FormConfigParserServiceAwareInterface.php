<?php
/**
 * Class FormConfigParserServiceAwareInterface
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

interface FormConfigParserServiceAwareInterface
{
    /**
     * @param FormConfigParserServiceInterface $formConfigParserService
     *
     * @return $this
     */
    public function setFormConfigParserService(FormConfigParserServiceInterface $formConfigParserService);

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
