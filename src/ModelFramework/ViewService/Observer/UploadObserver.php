<?php
/**
 * Class FormObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use Wepo\Lib\Acl;

class UploadObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $files = $subject->getParams()->fromFiles();
        if ( count( $files ) )
        {
            $fileService = $subject->getFileServiceVerify();
            $data        = $subject->getData();
            if ( isset( $data[ 'model' ] ) )
            {
                $model = $data[ 'model' ];
            }
            else
            {
                $id = (string) $subject->getParam( 'id', '0' );
                if ( $id == '0' )
                {
                    // :FIXME: check create permission
                    $model = $subject->getGateway()->model();
//                    $mode  = Acl::MODE_CREATE;
                }
                else
                {
                    // :FIXME: add security filter
                    $model = $subject->getGateway()->get( $id );
//                    $mode  = Acl::MODE_EDIT;
                }
            }

            $aclModel = null;
            if ( $model instanceof AclDataModel )
            {
                $aclModel = $model;
                $model    = $aclModel->getDataModel();
            }
            foreach ( $files as $_group => $_filefields )
            {
                foreach ( $_filefields as $_fieldname => $_file )
                {
                    if ( isset( $model->$_fieldname ) )
                    {
                        $realname  = $_fieldname . '_real_name';
                        $size      = $_fieldname . '_size';
                        $extension = $_fieldname . '_extension';
                        if ( isset( $model->$size ) )
                        {
                            $model->$size = (string) ( round( (float) $_file[ 'size' ] / 1048576, 2 ) ) . ' MB';
                        }
                        if ( isset( $model->$realname ) )
                        {
                            $model->$realname = basename( $_file[ 'name' ] );
                        }
                        if ( isset( $model->$extension ) )
                        {
                            $model->$extension = $fileService->getFileExtension( $_file[ 'name' ] );
                        }
                        if ( $_fieldname != 'avatar' )
                        {

                            $model->$_fieldname = $fileService->saveFile( $_file[ 'name' ], $_file[ 'tmp_name' ] );
                        }
                        else
                        {
                            $model->$_fieldname = basename($fileService->saveFile( $_file[ 'name' ], $_file[ 'tmp_name' ], true, lcfirst($model->getModelName() )));
                        }
                    }
                }
            }

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
}