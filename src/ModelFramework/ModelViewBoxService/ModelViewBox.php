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
use ModelFramework\ModelViewService\ModelView;
use ModelFramework\ModelViewService\ModelViewServiceAwareInterface;
use ModelFramework\ModelViewService\ModelViewServiceAwareTrait;
use ModelFramework\ModelViewService\ParamsAwareInterface;
use ModelFramework\ModelViewService\ParamsAwareTrait;

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

    public function process()
    {
        // !!!! FIXME !!!!


        // should i use init ?

        foreach ( $this -> getViewBoxConfigDataVerify()->blocks as $blockName => $viewNames )
        {
            foreach ( $viewNames as $viewName )
            {
                $vDoc = explode('.', $viewName );
                $modelView =  $this->getModelViewServiceVerify()->get( $vDoc[0], $vDoc[1] );
                prn( $modelView );

                $modelView->setParams( $this->getParamsVerify() );
                $modelView->process();
//                if ( $modelView->hasRedirect() )
//                {
//                    return $modelView->getRedirect();
//                }
//                $result = $modelView->getData();

                $this->setData( [ $blockName => $modelView->getData() ] );
            }

        }

        prn( $this->getData() );
        return $this;
    }

}