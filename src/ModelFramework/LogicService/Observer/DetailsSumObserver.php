<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class DetailsSumObserver extends AbstractObserver
{

    use SubjectAwareTrait;

    public function process( $model, $key, $value )
    {
//        prn('heare',$model);
        $sum = 0;
        $details = $this->getSubject()->getGatewayService()->get( $value[ 'details' ] )
                        ->find( [ $value[ 'extLinkF' ] => $model->$value[ 'curLinkF' ] ] );
        foreach ( $details as $detail )
        {
            $sum += $detail->$value['sum_field'];
        }
        $model->$key = $sum;
//        prn($sum);
//        exit;
    }
}