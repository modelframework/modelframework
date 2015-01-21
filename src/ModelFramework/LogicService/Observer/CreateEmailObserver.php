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
        //todo check what is going when collector model updating or linking model.
        $this->setSubject( $subject );
        $models       = $subject->getEventObject();
        $linkGW       = $subject->getGatewayServiceVerify()->get( 'EmailToMail' );
        $action       = $this->getRootConfig()[ 'action' ];
        $search_field = $this->getRootConfig()[ 'search_field' ];
        if (!is_array( $models )) {
            $models = [ $models ];
        }

        $mailDetailsToUpdate = [ ];

        foreach ($models as $model) {
            if (!empty( $model->$search_field )) {
                //prn( $model );
                $linkedLinks =
                    $linkGW->find( [ 'model_id' => (string) $model->_id, 'model_data' => $model->getModelName() ] );
                switch ($action) {
                    case 'update':
                        $unlinkedLinks = $linkGW->find( [
                            '-model_id'  => (string) $model->_id,
                            'mail_email' => $model->$search_field
                        ] );
                        //prn( 'linked links' );
                        foreach ($linkedLinks as $link) {
                            //prn( $link );
                            $link->model_email = $model->$search_field;
                            $link->model_title = $model->title;
                            $link->_acl        = $model->_acl;
                            //prn( $link );
                            $linkGW->save( $link );

                            $mailDetailsToUpdate[ ] = (string) $link->mail_id;
                        }
                        //prn( 'unlinked links' );
                        //prn( !count( $unlinkedLinks ) && !count( $linkedLinks ), count( $unlinkedLinks ),
                        //count( $linkedLinks ) );
                        if (!count( $unlinkedLinks ) && !count( $linkedLinks )) {
                            //prn( 'no links for model found' );
                            //prn( !count( $unlinkedLinks ) && !count( $linkedLinks ), count( $unlinkedLinks ),
                            //count( $linkedLinks ) );
                            $unlinkedLinks = [ $subject->getModelServiceVerify()->get( 'EmailToMail' ) ];
                        }
                        foreach ($unlinkedLinks as $link) {
                            if ($link->model_id) {
                                $newLink = $subject->getModelServiceVerify()->get( 'EmailToMail' );
                                $newLink->exchangeArray( $link->toArray() );
                                $newLink->model_id    = (string) $model->_id;
                                $newLink->model_title = $model->title;
                                $newLink->model_email = $model->$search_field;
                                $newLink->_acl        = $model->_acl;
                                $linkGW->save( $newLink );
                                //prn( 'new link', $newLink );

                                $mailDetailsToUpdate[ ] = (string) $link->mail_id;
                            } elseif (!in_array( (string) $link->mail_id, $mailDetailsToUpdate )) {
                                $link->model_id    = (string) $model->_id;
                                $link->model_data  = $model->getModelName();
                                $link->model_title = $model->title;
                                $link->model_email = $model->$search_field;
                                $link->_acl        = $model->_acl;
                                $linkGW->save( $link );
                                //prn( 'found link', $link );

                                $mailDetailsToUpdate[ ] = (string) $link->mail_id;
                            }
                        }
                        exit;

                        break;
                    case 'delete':
                        foreach ($linkedLinks as $link) {
                            $link->model_id   = 0;
                            $link->model_data = 'Other';
                            $linkGW->save( $link );

                            $mailDetailsToUpdate[ ] = $link->mail_id;
                        }
                        break;
                }
            }
        }
        exit;
        $mailDetailsToUpdate = array_unique( $mailDetailsToUpdate );
        $mailDetailGW        = $subject->getGatewayServiceVerify()->get( 'MailDetail' );
        $res                 = $mailDetailGW->find( [ '_id' => $mailDetailsToUpdate ] );
        $mailDetails         = [ ];
        foreach ($res as $mailDetail) {
            $mailDetails[ ] = $mailDetail;
        }

        $subject->getLogicService()->get( 'updateTitle', 'MailDetail' )->trigger( $mailDetails );
//        exit;
    }
}