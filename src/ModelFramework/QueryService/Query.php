<?php
/**
 * Class Query
 * @package ModelFramework\QueryService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService;

use ModelFramework\QueryService\QueryConfig\QueryConfigAwareInterface;
use ModelFramework\QueryService\QueryConfig\QueryConfigAwareTrait;

class Query
    implements QueryInterface, QueryConfigAwareInterface, \SplSubject
{

    use QueryConfigAwareTrait;

    protected $allowed_observers = [
    ];

    protected $observers = [ ];

    public function attach( \SplObserver $observer )
    {
        $this->observers[ ] = $observer;
    }

    public function detach( \SplObserver $observer )
    {
        $key = array_search( $observer, $this->observers );
        if ( $key )
        {
            unset( $this->observers[ $key ] );
        }
    }

    public function notify()
    {
        foreach ( $this->observers as $observer )
        {
            $observer->update( $this );
        }
    }


    public function  init()
    {
        foreach ( $this->getQueryConfigVerify()->query as $observer => $obConfig )
        {
            if ( is_numeric( $observer ) )
            {
                $observer = $obConfig;
                $obConfig = null;
            }
            if ( !in_array( $observer, $this->allowed_observers ) )
            {
                throw new \Exception( $observer . ' is not allowed in ' . get_class( $this ) );
            }
            $observerClassName = 'ModelFramework\QueryService\Observer\\' . $observer;
            $_obs              = new $observerClassName();
            if ( !empty( $obConfig ) )
            {
                $_obs->setConfig( $obConfig );
            }
            $this->attach( $_obs );
        }

    }


    public function process()
    {
        $this->notify();

        return $this;
    }

}