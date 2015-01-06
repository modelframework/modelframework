<?php
/**
 * Class FormatObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class FormatObserver extends AbstractObserver
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
            'format' => ''
        ];

        $config = $this->getRootConfig();

        prn($config);

        $subject->setData( $data );

    }

}
