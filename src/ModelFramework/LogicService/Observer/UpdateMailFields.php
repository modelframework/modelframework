<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class UpdateMailFields
    implements \SplObserver, SubjectAwareInterface, ConfigAwareInterface
{

    use SubjectAwareTrait;
    use ConfigAwareTrait;


    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        $destinationField = $this->getRootConfig()[ 'destination' ];
        $models           = $subject->getEventObject();
        if (!is_array( $models )) {
            $models = [ $models ];
        }

        $linkGW = $subject->getGatewayServiceVerify()->get( 'EmailToMail' );
//        $modelGW = $subject->getGatewayServiceVerify()->get($models[0]->getModelName());
        $destinationValue = [];
        foreach ($models as $model) {
            $links = $linkGW->find( [ 'mail_id' => (string) $model->_id ] );
            foreach ($links as $link) {
                if(!empty($link->model_id)) {
                    $destinationValue[ $link->model_data ][ $link->model_id ] =
                        $link->model_title . ' <' . $link->mail_email . '>';
                }else
                {
                    $destinationValue['Other'][] = '<' . $link->mail_email . '>';
                }
            }
            $model->$destinationField = $destinationValue;
        }

    }
}