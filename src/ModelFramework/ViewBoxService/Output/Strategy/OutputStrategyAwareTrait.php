<?php
namespace ModelFramework\ViewBoxService\Output\Strategy;

/**
 * Class OutputStrategyAwareTrait
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
trait OutputStrategyAwareTrait
{
    /**
     * Set Strategy
     * @param OutputStrategyInterface $strategy
     * @return $this
     */
    protected function setStrategy(OutputStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * Get Strategy
     * @return OutputStrategyInterface
     */
    protected function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * choose Strategy
     * @param string $type
     * @return $this
     */
    public function chooseStrategy($type)
    {
        switch ($type) {
            case 'pdf':
                $this->setStrategy(new PDFOutStrategy());
                break;
            case 'html':
                $this->setStrategy(new HtmlOutStrategy());
                break;
            default:
                $this->setStrategy(new HtmlOutStrategy());
        }
        return $this;
    }
}