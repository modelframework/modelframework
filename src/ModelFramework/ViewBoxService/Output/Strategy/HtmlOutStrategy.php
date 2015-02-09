<?php
namespace ModelFramework\ViewBoxService\Output\Strategy;

use Zend\View\Model\ViewModel as ZendViewModel;

/**
 * Strategy for output PDF file
 * Class PDFOutStrategy
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
class HtmlOutStrategy
    implements OutputStrategyInterface
{
    /**
     * @var $ViewBox
     */
    protected $ViewBox;

    /**
     * Set $ViewBox in property
     * @param $ViewBox
     * @return $this
     */
    public function setViewBox($ViewBox){
        $this->ViewBox=$ViewBox;
    }

    /**
     * Generate output data
     * @return mixed
     */
    public function output()
    {
        $data=$this->ViewBox->getData();
        $viewModel = new ZendViewModel( $data );

        return $viewModel->setTemplate( $data[ 'template' ] );
    }

}
