<?php
/**
 * Trait FormConfigParserServiceAwareTrait
 * @package ModelFramework\ModelConfigsService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormConfigParserService;

trait FormConfigParserServiceAwareTrait
{
    /**
     * @var FormConfigParserServiceInterface
     */
    private $_formConfigParserService = null;

    /**
     * @param FormConfigParserServiceInterface $formConfigParserService
     *
     * @return $this
     */
    public function setFormConfigParserService(FormConfigParserServiceInterface $formConfigParserService)
    {
        $this->_formConfigParserService = $formConfigParserService;
    }

    /**
     * @return FormConfigParserServiceInterface
     */
    public function getFormConfigParserService()
    {
        return $this->_formConfigParserService;
    }

    /**
     * @return FormConfigParserServiceInterface
     * @throws \Exception
     */
    public function getFormConfigParserServiceVerify()
    {
        $formConfigParserService = $this->getFormConfigParserService();
        if ($formConfigParserService == null ||
             !$formConfigParserService instanceof FormConfigParserServiceInterface
        ) {
            throw new \Exception('FormConfigParserService does not set in the FormConfigParserServiceAware instance of '.
                                  get_class($this));
        }

        return $formConfigParserService;
    }
}
