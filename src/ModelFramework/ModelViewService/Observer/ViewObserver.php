<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

class ViewObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
//        $viewConfig          = $subject->getViewConfigDataVerify();
        $id                  = (string) $subject->getParams()->fromRoute( 'id', 0 );
        $result              = [ ];
        $result[ 'widgets' ] = [ ];
        $model               = $subject->getGatewayVerify()->findOne( [ '_id' => $id ] );
        prn ($subject->getGatewayVerify()->model());
        if ( !$model )
        {
            throw new \Exception( 'Data not found' );
        }
        $result[ 'model' ]          = $model;
        $result[ 'params' ][ 'id' ] = $id;
        $subject->setData( $result );
    }

}