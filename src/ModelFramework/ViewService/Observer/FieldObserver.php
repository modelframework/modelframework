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

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        prn( 'ViewService FieldObserver' );
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
        prn( $viewConfig );
        prn( $modelConfig = $subject->getModelConfigParserService()->getModelConfig( $data ) );
        prn( $aclData = $subject->getAclServiceVerify()->getAclData( $data ) );
        $fieldConfigs =
            [
                'fields' => [ ],
                'labels' => [ ]
            ];
        foreach ( array_keys( $aclData->fields ) as $field )
        {
            $fieldConfigs[ 'fields' ][ $field ] = in_array( $field, $viewConfig->fields ) ? true : false;
            $fieldConfigs[ 'labels' ][ $field ] = $modelConfig[ 'labels' ][ $field ];
        }
        prn( $fieldConfigs );
        $result                   = [ ];
        $result[ 'fieldconfigs' ] = $fieldConfigs;
        $subject->setData( $result );
    }

}