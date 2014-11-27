<?php
/**
 * Class ModelView
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewBoxService;

use ModelFramework\DataModel\Custom\ViewBoxConfigDataAwareInterface;
use ModelFramework\DataModel\Custom\ViewBoxConfigDataAwareTrait;
use ModelFramework\ModelViewService\ModelViewServiceAwareInterface;
use ModelFramework\ModelViewService\ModelViewServiceAwareTrait;
use ModelFramework\ModelViewService\ParamsAwareInterface;
use ModelFramework\ModelViewService\ParamsAwareTrait;
use Zend\View\Model\ViewModel;

class ModelViewBox implements ViewBoxConfigDataAwareInterface, ParamsAwareInterface, ModelViewServiceAwareInterface
{

    use ViewBoxConfigDataAwareTrait, ParamsAwareTrait, ModelViewServiceAwareTrait;

    private $_data = [ ];

    public function getData()
    {
        return $this->_data;
    }

    public function setData( array $data )
    {
        $this->_data = \Zend\Stdlib\ArrayUtils::merge( $this->_data, $data );
//        $this->_data += $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function setDataFields()
    {
        $viewBoxConfig = $this->getViewBoxConfigDataVerify();

        $result           = [ ];
        $result[ 'data' ] = [ ];

        $result[ 'document' ] = $viewBoxConfig->document;
        $result[ 'blocks' ]   = $viewBoxConfig->blocks;
        $result[ 'template' ] = $viewBoxConfig->template;
        $result[ 'title' ]    = $viewBoxConfig->title;
        $result[ 'mode' ]     = $viewBoxConfig->mode;

//        $result[ 'fields' ]    = $this->fields();
//        $result[ 'labels' ]    = $this->labels();
//        $result[ 'modelname' ] = strtolower( $viewConfig->model );
//        $result[ 'table' ]     = [ 'id' => Table::getTableId( $viewConfig->model ) ];
//        $result[ 'user' ]      = $this->getUser();
//        $result[ 'saurlhash' ] = $this->generateLabel();
//        $result[ 'saurl' ]     = '?back=' . $result[ 'saurlhash' ];
//        $result[ 'saurlback' ] = $this->getSaUrlBack( $this->getParams()->fromQuery( 'back', 'home' ) );
//        $result[ 'user' ]      = $this->getUser();
//        $result[ 'actions' ]   = $this->getViewConfigDataVerify()->actions;

        $this->setData( $result );
    }

    public function init()
    {
        $this->setDataFields();
    }

    public function process()
    {
        $this->init();

        // !!!! FIXME !!!!

        // should i use init ?

        foreach ( $this->getViewBoxConfigDataVerify()->blocks as $blockName => $viewNames )
        {
            prn( $blockName, $viewNames );
            foreach ( $viewNames as $viewName )
            {
                $vDoc      = explode( '.', $viewName );
                $modelView = $this->getModelViewServiceVerify()->get( $vDoc[ 0 ], $vDoc[ 1 ] );

                prn( $modelView, $modelView->getViewConfigData() );
                $modelView->setParams( $this->getParamsVerify() );
                $modelView->process();
//
//                $result = $modelView->getData();

                $viewResults = [ 'data' => [ $blockName => [ $viewName => $modelView->getData() ] ] ];
                $this->setData( $viewResults );
            }

        }

        prn( $this->getData() );

        return $this;
//        return $this;
    }

    public function output()
    {
//        if ( $modelView->hasRedirect() )
//        {
//            return $modelView->getRedirect();
//        }

        $data      = $this->getData();
        $viewModel = new ViewModel( $data );

        return $viewModel->setTemplate( $data[ 'template' ] );
    }

}