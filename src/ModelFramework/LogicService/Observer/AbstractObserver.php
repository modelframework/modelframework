<?php
/**
 * Class ConcatenationObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\LogicService\Logic;

abstract class AbstractObserver
    implements \SplObserver
{

    use ConfigAwareTrait;

    private $_subject = null;

    public function setSubject( \SplSubject $subject )
    {
        $this->_subject = $subject;
    }

    public function getSubject( )
    {
        return $this->_subject;
    }

    /**
     * @param \SplSubject|Logic $subject
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


    abstract public function process( $model, $key, $value );

}