<?php
namespace ModelFramework\ViewBoxService\Output\Strategy;

/**
 * Strategy for output PDF file
 * Interface OutputStrategyInterface
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
interface OutputStrategyInterface {
    /**
     * Set $ViewBox in property
     * @param array $data
     * @return $this
     */
    public function setViewBox($ViewBox);

    /**
     * Generate output data
     * @return mixed
     */
    public function output();



}