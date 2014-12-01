<?php
/**
 * Class View
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewBoxService;

use ModelFramework\DataModel\Custom\ViewBoxConfigAwareInterface;
use ModelFramework\DataModel\Custom\ViewBoxConfigAwareTrait;
use ModelFramework\ViewService\ViewServiceAwareInterface;
use ModelFramework\ViewService\ViewServiceAwareTrait;
use ModelFramework\ViewService\ParamsAwareInterface;
use ModelFramework\ViewService\ParamsAwareTrait;
use Zend\View\Model\ViewModel as ZendViewModel;

class ViewBox implements ViewBoxConfigAwareInterface, ParamsAwareInterface, ViewServiceAwareInterface
{

    use ViewBoxConfigAwareTrait, ParamsAwareTrait, ViewServiceAwareTrait;

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
        $viewBoxConfig = $this->getViewBoxConfigVerify();

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

        foreach ( $this->getViewBoxConfigVerify()->blocks as $blockName => $viewNames )
        {
            foreach ( $viewNames as $viewName )
            {
                $modelView = $this->getViewServiceVerify()->get( $viewName );

                $modelView->setParams( $this->getParamsVerify() );
                $modelView->process();
//
//                $result = $modelView->getData();

                $viewResults = [ 'data' => [ $blockName => [ $viewName => $modelView->getData() ] ] ];
                $this->setData( $viewResults );
            }

        }

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
        $viewModel = new ZendViewModel( $data );

        return $viewModel->setTemplate( $data[ 'template' ] );
    }

}