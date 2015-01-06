<?php
/**
 * Class DateObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class DetailsSumObserver extends AbstractConfigObserver
{

    use SubjectAwareTrait;

    public function process( $model, $key, $value )
    {
        $subject = $this->getSubject();

        $subject->setParamSource( $model );

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $value[ 'query' ] )
                    ->setParamSource( $model )
                    ->process();

        $model->$key = 0;
        $details     = $this->getSubject()->getGatewayService()->get( $query->getModelName() )
                            ->find( $query->getWhere(), $query->getOrder() );
        foreach ( $details as $detail )
        {
            $model->$key += $detail->$value[ 'field' ];
        }

    }
}
