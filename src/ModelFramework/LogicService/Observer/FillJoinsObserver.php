<?php
/**
 * Class FillJoinsObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\DataModel\AclDataModel;
use ModelFramework\LogicService\Logic;
use Zend\Db\ResultSet\ResultSetInterface;

class FillJoinsObserver
    implements \SplObserver
{

    /**
     * @param \SplSubject|Logic $subject
     */
    public function update( \SplSubject $subject )
    {
        $this->fillJoins( $subject );
//        FIXME This is a place for debugging results of Observers
//        prn($subject->getEventObject());
//        exit;
    }

    /**
     * @param \SplSubject|Logic $subject
     */
    protected function fillJoins( $subject )
    {
        $models      = $subject->getEventObject();
        $modelConfig = $subject->getModelConfigParserService()->getModelConfig( $subject->getModelName() );
        if ( !( is_array( $models ) || $models instanceof ResultSetInterface ) )
        {
            $models = [ $models ];
        }

        $aModels = [];
        foreach ( $models as $_k => $aclModel )
        {
            if ( $aclModel instanceof AclDataModel )
            {
                $mymodel = $aclModel->getDataModel();
            }
            else
            {
                $mymodel = $aclModel;
            }

            foreach ( $modelConfig[ 'joins' ] as $_k => $join )
            {
                $othergw = $subject->getGatewayServiceVerify()->get( $join[ 'model' ] );
                foreach ( $join[ 'on' ] as $myfield => $otherfield )
                {
                    $othermodel = $othergw->findOne( [ $otherfield => $mymodel->$myfield ] );
                    if ( $othermodel !== null )
                    {
                        foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                        {
                            $mymodel->$myfield = $othermodel->$otherfield;
                        }
                    }
                    else
                    {
                        foreach ( $join[ 'fields' ] as $myfield => $otherfield )
                        {
                            unset( $mymodel->$myfield );
                        }
                    }
                }
            }

            $aModels[ ] = $mymodel->getArrayCopy();

        }
        if ( $models instanceof ResultSetInterface )
        {
            $models->initialize( $aModels );
        }
//        else
//        {
//            $models = $aModels;
//        }

    }

}