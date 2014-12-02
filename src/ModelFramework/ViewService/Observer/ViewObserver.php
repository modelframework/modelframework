<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class ViewObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $id                  = (string) $subject->getParams()->fromRoute( 'id', 0 );
        $result              = [ ];
        $result[ 'widgets' ] = [ ];
        $model               = $subject->getGatewayVerify()->findOne( [ '_id' => $id ] );
        if ( !$model )
        {
            throw new \Exception( 'Data not found' );
        }

        $subject->getLogicServiceVerify()->trigger( 'preview', $model );

        $result[ 'model' ]          = $model;
        $result[ 'params' ][ 'id' ] = $id;
        $result[ 'title' ]          = $subject->getViewConfigVerify()->title . ' ' . $model->title;
//        $this->widgets( $subject, $model );
        $subject->setData( $result );

        $subject->getLogicServiceVerify()->trigger( 'postview', $model );
    }

}