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

class CreateEmailObserver
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
//        return;
        //todo check what is going when collector model updating or linking model.
        $this->setSubject( $subject );
        $models  = $subject->getEventObject();
        $linkGW  =
            $subject->getGatewayServiceVerify()->get( 'EmailToMail' );
        $emailGW = $subject->getGatewayServiceVerify()->get( 'Email' );
//        $mailDetailGW      = $subject->getGatewayServiceVerify()->get( 'MailDetail' );
        $action       = $this->getRootConfig()[ 'action' ];
        $search_field = $this->getRootConfig()[ 'search_field' ];
        if (!is_array( $models )) {
            $models = [ $models ];
        }

        $mailDetailsToUpdate = [ ];

        foreach ($models as $model) {
            if (!empty( $model->$search_field )) {
                //prn( $model );
                switch ($action) {
                    case 'update':
                        $email = $emailGW->find( [
                            'data'     => $model->getModelName(),
                            'model_id' => (string) $model->_id
                        ] )->current();

                        if (!isset( $email )) {
                            $email           = $subject->getModelServiceVerify()
                                                       ->get( 'Email' );
                            $email->data     = $model->getModelName();
                            $email->model_id = (string) $model->_id;
                        }
                        $email->_acl  = $model->_acl;
                        $email->email = $model->$search_field;
                        $email->title = $model->title;
//                        prn($email);
//                        exit;
                        $emailGW->save( $email );

                        $email->_id                     =
                            isset( $email->_id ) ?
                                $email->_id :
                                $emailGW->getLastInsertId();
                        $model->{$search_field . '_id'} = $email->_id;

                        $unlinkedLinks = $linkGW->find( [
                            'mail_email' => $model->$search_field
                        ] );

                        foreach ($unlinkedLinks as $link) {
                            if (!in_array( (string) $link->mail_id,
                                $mailDetailsToUpdate )
                            ) {
                                $mailDetailsToUpdate[ ] =
                                    (string) $link->mail_id;
                                if ($link->model_id) {
                                    $newLink =
                                        $subject->getModelServiceVerify()
                                                ->get( 'EmailToMail' );
                                    $newLink->exchangeArray( $link->toArray() );
                                    $newLink->email_id = $email->_id;
                                    $link              = $newLink;

                                } else {
                                    $link->email_id = $email->_id;
                                }
                            }
                            $subject->getLogicService()
                                    ->get( 'update', 'EmailToMail' )
                                    ->trigger( $link );
                            $linkGW->save( $link );
                        }
                        break;
                    case 'delete':
                        $linkedLinks = $linkGW->find( [
                            'email_model_id' => (string) $model->_id,
                            'email_data'     => $model->getModelName()
                        ] );
                        $emailGW->delete( [
                            'model_id' => (string) $model->_id,
                            'data'     => $model->getModelName()
                        ] );
                        foreach ($linkedLinks as $link) {
                            $mailDetailsToUpdate[ ] = $link->mail_id;
                        }
                        $linkGW->delete( [
                            'email_data'     => $model->getMOdelName(),
                            'email_model_id' => (string) $model->_id
                        ] );
                        break;
                }
            }
        }
        $mailDetailsToUpdate = array_unique( $mailDetailsToUpdate );
        $mailDetailGW        =
            $subject->getGatewayServiceVerify()->get( 'MailDetail' );
        $res                 =
            $mailDetailGW->find( [ '_id' => $mailDetailsToUpdate ] );
        $mailDetails         = [ ];
        foreach ($res as $mailDetail) {
            $mailDetails[ ] = $mailDetail;
        }

        if(count($mailDetails)) {
            $subject->getLogicService()->get( 'updateTitle', 'MailDetail' )
                    ->trigger( $mailDetails );
        }
//        exit;
    }
}