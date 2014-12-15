<?php
/**
 * Class OrderObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

use ModelFramework\Utility\Arr;

class OrderObserver extends AbstractObserver
{

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $data  = [
            'params' => [ ],
            'column' => [ ]
        ];
        $order = [ ];

//        $queryConfig = $subject->getQueryConfig();
//        $defaults    = $queryConfig->order;
        $defaults    =  $this->getRootConfig();

        $sort = $subject->getParam( 'sort', null );
        if ( $sort == null || !in_array( $sort, $queryConfig->fields ) )
        {
            $sort = Arr::getDoubtField( $defaults, 'sort', null );
            if ( $sort == null )
            {
                return '';
            }
        }
        else
        {
            $data[ 'params' ][ 'sort' ] = $sort;
        }

        $s = (int) $subject->getParam( 'desc', null );
        if ( $s == null )
        {
            $s = Arr::getDoubtField( $defaults, 'desc', 0 );
        }
        else
        {
            $data[ 'params' ][ 'desc' ] = $s;
        }

        $order[ $sort ] = ( $s == 1 ) ? 'desc' : 'asc';
        $subject->setOrder( $order );
        $data[ 'column' ][ 'sort' ] = $sort;
        $data[ 'column' ][ 'desc' ] = $s;

        $subject->setData( $data );

    }

}