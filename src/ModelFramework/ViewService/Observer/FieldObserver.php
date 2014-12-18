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
        $fieldConfigs         = [ 'fields' => [ ], 'labels' => [ ] ];
        foreach ( $viewConfig->fields as $field )
        {
            if ( in_array( $field, array_keys( $aclData->fields ) ) )
            {
                $fieldConfigs[ 'fields' ][ $field ] = false;
            }
        }
        foreach ( array_keys( $aclData->fields ) as $field )
        {
            $fieldConfigs[ 'fields' ][ $field ] = in_array( $field, $viewConfig->fields ) ? true : false;
            $fieldConfigs[ 'labels' ][ $field ] = $modelConfig[ 'labels' ][ $field ];
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
                                   'action' => 'index', 'data' => $this->viewViewConfig->document,
                                   'mode'   => $this->viewViewConfig->mode
                               ] );
            }

            $subject->setRedirect( $subject->refresh( 'FieldConfig was successfully saved', $url ) );

            return;
        }

    }

}