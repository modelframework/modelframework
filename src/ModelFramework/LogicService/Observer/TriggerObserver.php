<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class TriggerObserver extends AbstractObserver
{

    use SubjectAwareTrait;

    public function process( $model, $key, $value )
    {
        $action    = $value;
        $srcConfig = $this->getSubject()->getModelConfigParserService()->getModelConfig( 'QuoteDetail' )[ 'joins' ];
        foreach ( $srcConfig as $join )
        {
            if ( isset( $join[ 'on' ][ $key ] ) )
            {
                $trgModelName   = $join[ 'model' ];
                $trgSearchField = $join[ 'on' ][ $key ];
                $trgModelGW     = $this->getSubject()->getGatewayService()->get( $trgModelName );
                $trgModel       = $trgModelGW->find( [ $trgSearchField => $model->$key ] )->current();
                $this->getSubject()->getLogicService()->trigger( $trgModelName . '.' . $action, $trgModel );
            }
        }
        exit;
    }

}