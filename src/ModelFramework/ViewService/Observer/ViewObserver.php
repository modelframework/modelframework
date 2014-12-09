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
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $subject->setData( $query->getData() );


        $result              = [ ];
        $model               = $subject->getGatewayVerify()->findOne( $query -> getWhere() );
        if ( !$model )
        {
            throw new \Exception( 'Data not found' );
        }

        $subject->getLogicServiceVerify()->trigger( 'preview', $model );

        $result[ 'model' ]          = $model;
        $result[ 'title' ]          = $viewConfig->title . ' ' . $model->title;
        $subject->setData( $result );

        $subject->getLogicServiceVerify()->trigger( 'postview', $model );
    }

}