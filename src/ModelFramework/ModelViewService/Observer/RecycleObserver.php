<?php
/**
 * Class RecycleObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService\Observer;

class RecycleObserver implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        prn( 'RecycleObserver', $subject );
        $viewConfig        = $subject->getViewConfigDataVerify();
        $modelName         = $viewConfig->model;
        $modelRoute        = strtolower( $viewConfig->model );
        $request           = $subject->getParams()->getController()->getRequest();
        $results           = [ ];
        $results[ 'view' ] = $subject->getParam( 'view' );
        $ids               = $request->getPost( 'checkedid', null );
        if ( !is_array( $ids ) )
        {
            $id = $subject->getParams()->fromRoute( 'id', 0 );
            if ( $id )
            {
                $ids = array( $id );
            }
            else
            {
                $subject->setRedirect( $subject->getParams()->getController()->redirect()->toRoute( 'common',
                                                                                                    [
                                                                                                        'data' => $modelRoute,
                                                                                                        'view' =>
                                                                                                            $results[ 'view' ] ==
                                                                                                            'delete' ?
                                                                                                                'list' :
                                                                                                                'recyclelist'
                                                                                                    ] ) );

                return;
            }
        }
        $results[ 'ids' ] = $ids;
        foreach ( $ids as $id )
        {
            try
            {
                $results[ 'items' ][ $id ] = $subject->getGateway()->findOne( [ '_id' => $id ] );
            }
            catch ( \Exception $ex )
            {
                $subject->setRedirect( $subject->refresh( 'Data is invalid ' .
                                                          $ex->getMessage(), $this->url()
                                                                                  ->fromRoute( 'common',
                                                                                               array(
                                                                                                   'data' => $modelRoute,
                                                                                                   'view' => 'list'
                                                                                               ) ) ) );

                return;
            }
        }
        if ( $request->isPost() )
        {
            $delyes = $request->getPost( 'delyes', null );
            $delno  = $request->getPost( 'delno', null );
            if ( $delyes !== null )
            {
                $subject->getParams()->getController()->trigger( 'prerecycle', $results[ 'items' ] );
                $subject->getParams()->getController()->trigger( 'recycle', $results[ 'items' ] );
                $subject->getParams()->getController()->trigger( 'postrecycle', $results[ 'items' ] );
                $url = $subject->getParams()->fromPost( 'saurl' )[ 'back' ];
                if ( !isset( $url ) )
                {
                    $url = $subject->getParams()->getController()->url()->fromRoute( 'common', [
                        'data' => $modelRoute, 'view' => $results[ 'view' ] == 'delete' ? 'list' : 'recyclelist'
                    ] );
                }
                $subject->setRedirect( $subject->refresh( ucfirst( $results[ 'view' ] ) . ' was successfull ',
                                                          $url ) );

                return;

            }
            if ( $delno !== null )
            {
                $subject->setRedirect( $subject->getParams()->getController()->redirect()->toRoute( 'common', [
                    'data' => $modelRoute, 'view' => $results[ 'view' ] == 'delete' ? 'list' : 'recyclelist'
                ] ) );

                return;
            }
        }
        $subject->setData( $results );
    }

}