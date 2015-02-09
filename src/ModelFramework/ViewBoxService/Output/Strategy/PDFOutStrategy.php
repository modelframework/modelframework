<?php
namespace ModelFramework\ViewBoxService\Output\Strategy;

/**
 * Strategy for output PDF file
 * Class PDFOutStrategy
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
class PDFOutStrategy
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
        $pdf= $this->ViewBox->getPDFServiceVerify();
        return $pdf->getPDFtoSave($data[ 'template' ],$data);
    }
}
