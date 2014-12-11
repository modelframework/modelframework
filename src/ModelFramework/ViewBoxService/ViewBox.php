<?php
/**
 * Class ViewBox
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewBoxService;

use ModelFramework\AuthService\AuthServiceAwareInterface;
use ModelFramework\AuthService\AuthServiceAwareTrait;
use ModelFramework\Utility\Arr;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;
use ModelFramework\ViewBoxService\ViewBoxConfig\ViewBoxConfigAwareInterface;
use ModelFramework\ViewBoxService\ViewBoxConfig\ViewBoxConfigAwareTrait;
use ModelFramework\ViewService\ViewServiceAwareInterface;
use ModelFramework\ViewService\ViewServiceAwareTrait;
use Zend\View\Model\ViewModel as ZendViewModel;

class ViewBox implements ViewBoxConfigAwareInterface, ParamsAwareInterface, ViewServiceAwareInterface, AuthServiceAwareInterface
{

    use ViewBoxConfigAwareTrait, ParamsAwareTrait, ViewServiceAwareTrait, AuthServiceAwareTrait;

    private $_data = [ ];
    private $_redirect = null;

    public function setRedirect( $redirect )
    {
        $this->_redirect = $redirect;
    }

    public function getRedirect()
    {
        return $this->_redirect;
    }

    public function hasRedirect()
    {
        if ( !empty( $this->_redirect ) )
        {
            return true;
        }

        return false;
    }


    public function getData()
    {
        return $this->_data;
    }

    public function setData( array $data )
    {
        $this->_data = Arr::merge( $this->_data, $data );
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function setDataFields()
    {
        $viewBoxConfig        = $this->getViewBoxConfigVerify();
        $result               = [ ];
        $result[ 'data' ]     = [ ];
        $result[ 'document' ] = $viewBoxConfig->document;
        $result[ 'blocks' ]   = $viewBoxConfig->blocks;
        $result[ 'template' ] = $viewBoxConfig->template;
        $result[ 'title' ]    = $viewBoxConfig->title;
        $result[ 'mode' ]     = $viewBoxConfig->mode;
        $result[ 'user' ]     = $this->getAuthServiceVerify()->getUser();
        $this->setData( $result );
    }

    public function process()
    {
        $this->setDataFields();

        $params = [];

        foreach ( $this->getViewBoxConfigVerify()->blocks as $blockName => $viewNames )
        {
            foreach ( $viewNames as $viewName )
            {
                $modelView = $this->getViewServiceVerify()->get( $viewName );
                $modelView->setParams( $this->getParamsVerify() );
                $modelView->process();
                if ( $modelView->hasRedirect() )
                {
                    $this->setRedirect( $modelView->getRedirect() );

                    return;
                }

                $data = $modelView->getData();

                $vParams = Arr::getDoubtField( $data, 'params', [ ] );
                if ( count( $vParams ) )
                {
                    $params = Arr::merge( $params, $vParams );
                }

                $viewResults = [ 'data' => [ $blockName => [ $viewName => $modelView->getData() ] ] ];
                $this->setData( $viewResults );
            }

        }

        $params['data'] = strtolower( $this -> getViewBoxConfigVerify()->document );
        $params['view'] = strtolower( $this -> getViewBoxConfigVerify()->mode );

        $this->setData( [ 'viewboxparams' => $params ] );

        return $this;
    }

    public function output()
    {
        if ( $this->hasRedirect() )
        {
            return $this->getRedirect();
        }
        $data = $this->getData();

        $viewModel = new ZendViewModel( $data );

        return $viewModel->setTemplate( $data[ 'template' ] );
    }

}