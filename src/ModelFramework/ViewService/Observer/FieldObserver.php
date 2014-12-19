<?php
/**
 * Class FieldObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;
use ModelFramework\ViewService\ViewConfig\ViewConfig;

class FieldObserver
    implements \SplObserver
{

    private $viewViewConfig = null;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $data = $subject->getParam( 'data', null );
        $view = $subject->getParam( 'view', null );
        if ( $data == null || $view == null )
        {
            throw new \Exception( 'Please specify data param' );
        }
        $data       = ucfirst( $data );
        $viewConfig = $subject->getConfigServiceVerify()->getByObject( $data . '.' . $view, new ViewConfig() );
        if ( $viewConfig == null )
        {
            throw new \Exception( 'Please fill ViewConfig for the ' . $data . '.' . $view );
        }
        $this->viewViewConfig = $viewConfig;
        $modelConfig          = $subject->getModelConfigParserService()->getModelConfig( $data );
        $aclData              = $subject->getAclServiceVerify()->getAclData( $data );
        $fieldConfigs = [ 'fields' => [ ], 'labels' => [ ] ];
        foreach ( $viewConfig->fields as $field )
        {
            if ( array_key_exists( $field, $modelConfig[ 'fields' ] )
//                && array_key_exists( $field, $aclData->fields )
//                && $aclData->fields[ $field ] !== 'x'
            )
            {
                $fieldConfigs[ 'fields' ][ $field ] = false;
            }
        }

        foreach ( $modelConfig[ 'fields' ] as $field => $fConfig )
        {
            if ( $fConfig[ 'type' ] == 'field' )
            {
                //check $field in acl
                if ( !array_key_exists( $field, $aclData->fields )
                     || !in_array( $aclData->fields[ $field ], [ 'r', 'e' ] )
                )
                {
                    unset($fieldConfigs[ 'fields' ][ $field ]);
                    continue;
                }
            }
            if ( $fConfig[ 'type' ] == 'alias' )
            {
                //check $fConfig['source'] in acl
                if ( !array_key_exists( $fConfig[ 'source' ], $aclData->fields )
                     || !in_array( $aclData->fields[ $fConfig[ 'source' ] ], [ 'r', 'e' ] )
                )
                {
                    unset($fieldConfigs[ 'fields' ][ $field ]);
                    continue;
                }
            }
            if ( $fConfig[ 'type' ]=='source' )
            {
                if ( $fConfig['source']!==$field
                     || !array_key_exists( $fConfig[ 'source' ], $aclData->fields )
                     || !in_array( $aclData->fields[ $fConfig[ 'source' ] ], [ 'r', 'e' ] ))
                {
                    unset($fieldConfigs[ 'fields' ][ $field ]);
                    continue;
                }
            }
            if ( $fConfig[ 'type' ] == 'pk' )
            {
                continue;
            }
            $fieldConfigs[ 'fields' ][ $field ] = in_array( $field, $viewConfig->fields ) ? true : false;
            $fieldConfigs[ 'labels' ][ $field ] = $fConfig[ 'label' ];
        }

        $result                   = [ ];
        $result[ 'fieldconfigs' ] = $fieldConfigs;
        $result[ 'params' ]       = [ 'data' => $data, 'view' => $view ];
        $subject->setData( $result );
        $this->postVerify( $subject );
    }

    public function postVerify( View $subject )
    {

        $fields  = [ ];
        $request = $subject->getParams()->getController()->getRequest();
        if ( $request->isPost() )
        {
            $_rows = $subject->getParams()->fromPost( 'row' );

            if ( is_array( $_rows ) )
            {
                foreach ( $_rows as $_k => $_row )
                {
                    if ( !empty( $_row[ 'visible' ] ) && $_row[ 'visible' ] == 1 )
                    {
                        $fields[ ] = $_k;
                    }
                }

                if ( count( $fields ) )
                {
                    $this->viewViewConfig->fields = $fields;
                    $subject->getConfigServiceVerify()->saveByObject( $this->viewViewConfig );
                }
            }

            $url = $subject->getBackUrl();
            if ( $url == null || $url == '/' )
            {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute( 'common', [
                                   'action' => 'index', 'data' => strtolower( $this->viewViewConfig->document ),
                                   'mode'   => $this->viewViewConfig->mode
                               ] );
            }

            $subject->setRedirect( $subject->refresh( 'FieldConfig was successfully saved', $url ) );

            return;
        }

    }

}