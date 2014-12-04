<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

use ModelFramework\QueryService\Query;

abstract class AbstractObserver
    implements \SplObserver
{

    use ConfigAwareTrait, SubjectAwareTrait;

    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $models = $subject->getEventObject();
        if ( !( is_array( $models ) || $models instanceof ResultSetInterface ) )
        {
            $models = [ $models ];
        }

        $aModels = [];
        foreach ( $models as $_k => $model )
        {
            if ( $model instanceof AclDataModel )
            {
                $dataModel = $model->getDataModel();
            }
            else
            {
                $dataModel = $model;
            }

            foreach ( $this->getConfig() as $key => $value )
            {
                if ( is_numeric($key ) )
                {
                    $key = $value;
                    $value = '';
                }
                if ( !isset( $dataModel->$key ) )
                {
                    throw new \Exception( 'Field ' . $key . ' does not exist in model ' . $dataModel->getModelName() );
                }

                $this->process( $dataModel, $key, $value);

            }

            $aModels[ ] = $model->getArrayCopy();
        }

        if ( $models instanceof ResultSetInterface )
        {
            $models->initialize( $aModels );
        }
    }


    /**
     * @param $model
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    abstract public function process( $model, $key, $value );

}