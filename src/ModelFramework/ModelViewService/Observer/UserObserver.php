<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

use ModelFramework\ModelViewService\ModelView;

class UserObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        /**
         * @var ModelView $subject
         */
        $result              = [ ];
        $model               = $subject -> getAuthServiceVerify() -> getUser();
        if ( !$model )
        {
            throw new \Exception( 'User not found' );
        }
        $result[ 'model' ]          = $model;
        $result[ 'title' ]          = $subject->getViewConfigDataVerify()->title . ' ' . $model->title;
//        $this->widgets( $subject, $model );
        $subject->setData( $result );
    }
}