<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class SearchObserver extends AbstractObserver
{

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $data = [
            'where' => [],
//            'order' => [],
//            'result' => [],
            'params' => []
        ];

        $config = $this->getRootConfig();

        $searchQuery = $subject->getParam( $config['param'], '' );

        if ( !strlen($searchQuery) )
            return;

        $data['search_query'] = $searchQuery;
        $data['params'][$config['param']] = $searchQuery;


//        $data['where'] = [
//            '$and' => [ $subject->swipeDataKey('where'), [ '$text' => [ '$search' => $searchQuery ] ] ]
//        ];

        $data['where'] = [
            '$and' => [ [ '$text' => [ '$search' => $searchQuery ] ] ]
        ];


        $subject->setData( $data );

    }

}