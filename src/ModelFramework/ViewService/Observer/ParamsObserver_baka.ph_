<?php
/**
 * Class ParamsObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\DataModel\AclDataModel;

class ParamsObserver implements \SplObserver, ConfigAwareInterface
{

    use ConfigAwareTrait;

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigVerify();
        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $config = $this->getRootConfig();
        $data   = $subject->getData();
        if ( isset( $data[ 'model' ] ) )
        {
            $model = $data[ 'model' ];
        }
        else
        {
            $id = (string) $subject->getParam( 'id', '0' );
            if ( $id == '0' )
            {
                $model = $subject->getGateway()->model();
            }
            else
            {
                $model = $subject->getGateway()->findOne( $query->getWhere() );
                if ( $model == null )
                {
                    throw new \Exception( 'Can\'t find data for edit' );
                }
            }
        }

        $aclModel = null;
        if ( $model instanceof AclDataModel )
        {
            $aclModel = $model;
            $model    = $aclModel->getDataModel();
        }
        /***
         *
         */
        foreach ( $config as $key => $param )
        {
            $value = $subject->getParam( $param, null );
            if ( $value != null )
            {
                $model->$key = $value;
            }
        }
        /***
         *
         */
        if ( $aclModel !== null && $aclModel instanceof AclDataModel )
        {
            $aclModel->setDataModel( $model );
            $model    = $aclModel;
            $aclModel = null;
        }

        $data[ 'model' ] = $model;
        $subject->setData( $data );
    }
}