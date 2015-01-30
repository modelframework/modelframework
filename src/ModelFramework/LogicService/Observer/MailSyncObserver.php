<?php
/**
 * Class MailSyncObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\FormService\StaticDataConfig\StaticDataConfig;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use Wepo\Model\Status;

class MailSyncObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );

        $users = $subject->getEventObject();
        $action = $this->getRootConfig()['action'];
        if (!( is_array( $users ) || $users instanceof ResultSetInterface )) {
            $users = [ $users ];
        }
        switch($action)
        {
            case 'fetch':
                exit;
                foreach ($users as $_k => $user) {
                    try {
                        $count = $this->syncMails( $user );
                    } catch ( \Exception $ex ) {
                        throw $ex;
                        $count = 0;
                    }

                    $this->updateMailChains( $user );
                }
                break;
            case 'send':
                exit;
                //todo move heare mail send observer logic
                break;
        }
    }

    public function getSyncSetting( $user )
    {
        //        prn( $user->id() );
        $protocols = array_keys( $this->getSubject()->getConfigServiceVerify()
                                      ->get( 'StaticDataSource',
                                          'ReceiveMailProtocol',
                                          new StaticDataConfig() )->options );
//        prn( $protocols );
        $gw = $this->getSubject()->getGatewayService()
                   ->get( 'MailReceiveSetting' );
//        exit;
        $settings = $gw->find( [
            'user_id'             => $user->id(),
            'setting_protocol_id' => $protocols,
            'status_id'           => [
                Status::NORMAL,
                Status::NEW_,
            ],
        ] );

        return $settings;
    }

    public function syncMails( $user )
    {
        ini_set( 'max_execution_time', 300 );
        $settings = $this->getSyncSetting( $user );
        $count    = 0;

//        prn( $this->getSubject()->getModelServiceVerify()->get( 'MailDetail' ) );
//        exit;
        $modelService = $this->getSubject()->getModelServiceVerify();

        $mailGW = $this->getSubject()->getGatewayService()->get( 'MailDetail' );
//        $chainGW = $this->getSubject()->getGatewayService()->get( 'Mail' );

        $mails    = $mailGW->find( [ 'owner_id' => $user->id() ] );
        $newMails = [ ];

        foreach ($settings as $setting) {
            $exceptUids = [ ];
            foreach ($mails as $mail) {
                if (isset( $mail->protocol_ids[ $setting->id() ] )) {
                    $exceptUids[ ] = $mail->protocol_ids[ $setting->id() ];
                }
            }
//            prn($exceptUids, $setting);
//            exit();
            $syncService  = $this->getFetchTransport( $setting );
            $fetchedMails = $syncService->fetchAll( $exceptUids );
//            prn($fetchedMails);
//            exit;
            if ($syncService->lastSyncIsSuccessful()) {
                $mailGW->delete( [ 'status_id' => Status::SENDING ] );
            }
//            prn( $fetchedMails );
//            exit;
            foreach ($fetchedMails as $key => $mail) {
                if (isset( $newMails[ $key ] )) {
                    $newMails[ $key ]->protocol_ids =
                        array_merge( $mail->protocol_ids,
                            $newMails[ $key ]->protocol_ids );
                } else {
                    //                    $newMails[ $key ] = $this->model( 'Mail' )->exchangeArray( $mail );
                    $newMails[ $key ] = $modelService->get( 'MailDetail' )
                                                     ->exchangeArray( $mail );
                    $this->configureFetchedMail( $user, $setting, $newMails[ $key ] );
                }
            }
        }

//        prn($newMails);
        $oldMails = count( $newMails ) ? $mailGW->find( [
            'header.message-id' => array_keys( $newMails ),
            'owner_id'          => $user->id(),
        ] ) : [ ];

        foreach ($oldMails as $oldMail) {
            $newMail = $newMails[ $oldMail->header[ 'message-id' ] ];
            unset( $newMails[ $oldMail->header[ 'message-id' ] ] );
            $oldMail->protocol_ids =
                array_merge( $newMail->protocol_ids, $oldMail->protocol_ids );
            $mailGW->save( $oldMail );
        }

        foreach ($newMails as $newMail) {
            //            $newMail->owner_id = $user->id();
//            $newMail->title = $newMail->header[ 'subject' ];
//            $newMail->date  = ( new \DateTime( $newMail->header[ 'date' ] ) )->format( 'Y-m-d H:i:s' );
            $this->getSubject()->getLogicService()
                 ->get( 'presave', $newMail->getModelName() )->trigger( $newMail );
            $mailGW->save( $newMail );
            $newMail->_id = $mailGW->getLastInsertId();
            $this->getSubject()->getLogicService()
                 ->get( 'postsync', $newMail->getModelName() )->trigger( $newMail );
            $this->createEmailToMail( $newMail );
            $count++;
        }

        return $count;
    }

    //todo rewrite update method to work in
//    public function updateMailChains( $user )
//    {
//        $mailGW       =
//            $this->getSubject()->getGatewayService()->get( 'MailDetail' );
//        $chainGW      = $this->getSubject()->getGatewayService()->get( 'Mail' );
//        $modelService = $this->getSubject()->getModelServiceVerify();
//
//        $mails        =
//            $mailGW->find( [ 'chain_id' => '', 'owner_id' => $user->id() ] );
//        $noChainMails = [ ];
//        foreach ($mails as $mail) {
//            $noChainMails[ ] = $mail;
//        }
//
//        while (count( $noChainMails )) {
//            $mail        = array_pop( $noChainMails );
//            $chainMails  = [ ];
//            $MInReplyTo  = isset( $mail->header[ 'in-reply-to' ] ) ?
//                $mail->header[ 'in-reply-to' ] : null;
//            $MMessageId  = $mail->header[ 'message-id' ];
//            $MReferences = isset( $mail->header[ 'references' ] ) ?
//                $mail->header[ 'references' ] : [ ];
//            $chainWhere  = $MReferences;
//            if (isset( $MInReplyTo )) {
//                array_push( $chainWhere, $MMessageId, $MInReplyTo );
//            } else {
//                array_push( $chainWhere, $MMessageId );
//            }
//
//            foreach ($noChainMails as $key => $mailToCheck) {
//                $MInReplyTo     =
//                    isset( $mailToCheck->header[ 'in-reply-to' ] ) ?
//                        $mailToCheck->header[ 'in-reply-to' ] : null;
//                $MMessageId     = $mailToCheck->header[ 'message-id' ];
//                $MReferences    =
//                    isset( $mailToCheck->header[ 'references' ] ) ?
//                        $mailToCheck->header[ 'references' ] : [ ];
//                $testChainWhere = $MReferences;
//                if (isset( $MInReplyTo )) {
//                    array_push( $testChainWhere, $MMessageId, $MInReplyTo );
//                } else {
//                    array_push( $testChainWhere, $MMessageId );
//                }
////                prn($testChainWhere, $chainWhere);
//                if (count( array_intersect( $testChainWhere, $chainWhere ) )) {
//                    $chainWhere = array_unique( array_merge( $testChainWhere,
//                        $chainWhere ) );
//                    reset( $noChainMails );
//                    unset( $noChainMails[ $key ] );
//                    $chainMails[ ] = $mailToCheck;
//                }
//            }
//            $chainMails[ ] = $mail;
//
////            $chain = $modelService->get( 'Mail' );
//
//            $chain =
//                $chainGW->find( [ 'reference' => array_values( $chainWhere ) ] )
//                        ->current();
//            $chain = isset( $chain ) ? $chain : $modelService->get( 'Mail' );
//
//            $firstMailDate = strtotime( $chain->date );
//            $lastMailDate  = strtotime( $chain->date );
//            $title         = $chain->title;
//            $date          = $chain->date;
//            $last_mail     = $chain->last_mail;
//            $status        = Status::NEW_;
//            foreach ($chainMails as $mail) {
//                $mailDate = strtotime( $mail->date );
////                prn( $mail->date, $mailDate, $firstMailDate, $lastMailDate );
//                if (( $mailDate < $firstMailDate ) || !$firstMailDate) {
//                    $title         = $mail->title;
//                    $firstMailDate = $mailDate;
//                }
//                if (( $mailDate > $lastMailDate ) || !$lastMailDate) {
//                    $date         = $mail->date;//$oldChainDate;
//                    $lastMailDate = $mailDate;
//                    $last_mail    = $mail->_id;
//                    $status       = $mail->status_id;
//                }
//            }
//
//            $chain->reference =
//                array_unique( array_merge( $chainWhere, $chain->reference ) );
//            $chain->title     = $title;
//            $chain->date      = $date;
//            $chain->last_mail = $last_mail;
//            $chain->count     = $chain->count + count( $chainMails );
//            $chain->status_id = $status;
////            prn( 'result', $chain );
//
//            try {
//                $chain->owner_id = $user->id();
//
//                $this->getSubject()->getLogicService()
//                     ->get( 'sync', $chain->getModelName() )->trigger( $chain );
////                prn($chain);
////                prn($chainMails);
//                $chainGW->save( $chain );
//                foreach ($chainMails as $mail) {
//                    $mail->chain_id =
//                        $chainGW->getLastInsertId() ?: $chain->_id;
////                    prn($mail);
//                    $mailGW->save( $mail );
//                    $mail->_id = $mailGW->getLastInsertId() ?: $mail->_id;
//                }
//            } catch ( \Exception $ex ) {
//                throw $ex;
//                continue;
//            }
//        }
//    }

    /**
     * @param Object $setting
     *
     * @return \Mail\Receive\BaseTransport|\Mail\Send\BaseTransport
     */
    public function getFetchTransport( $setting )
    {
        $tm = $this->getSubject()->getMailService();

        $purpose      = 'Receive';
        $protocolName = $setting->setting_protocol_id;
        $settingId    = (string) $setting->_id;

        $setting = array(
            'host'     => $setting->setting_host,
            'user'     => $setting->setting_user,
            'password' => $setting->pass,
            'ssl'      => $setting->setting_security_id,
            'port'     => $setting->setting_port,
        );

        return $tm->getGateway( $purpose, $protocolName, $setting, $settingId );
    }

    public function configureFetchedMail( $user, $setting, $mail )
    {
        $mail->from_id   = $setting->user_id;
        $mail->status_id = Status::NORMAL;
        foreach ($mail->header[ 'to' ] as $email) {
            $email        = strtolower( trim( $email ) );
            $settingEmail = strtolower( trim( $setting->email ) );
            if ($email == $settingEmail) {
                $mail->type      = 'inbox';
                $mail->to_id     = $setting->user_id;
                $mail->from_id   = '';
                $mail->status_id = Status::NEW_;

                break;
            }
        }
        $mail->owner_id = $user->id();
        $mail->title    = $mail->header[ 'subject' ];
        $mail->date     =
            ( new \DateTime( $mail->header[ 'date' ] ) )->format( 'Y-m-d H:i:s' );
    }

    //todo move to linkObserver
//    public function createEmailToMail( $mail )
//    {
//        $searchValues = $mail->type == 'inbox' ? $mail->header[ 'from' ] :
//            $mail->header[ 'to' ];
//        $emailGW      =
//            $this->getSubject()->getGatewayServiceVerify()->get( 'Email' );
//        $linkGW       =
//            $this->getSubject()->getGatewayServiceVerify()
//                 ->get( 'EmailToMail' );
//
//        $nonLinkedEmails = $emailGW->find( [ 'email' => $searchValues ] );
//        foreach ($nonLinkedEmails as $email) {
//            $link             = $this->getSubject()->getModelServiceVerify()
//                                     ->get( 'EmailToMail' );
//            $link->email_id   = $email->_id;
//            $link->mail_id    = (string) $mail->_id;
//            $link->mail_field = 'from_to_title';
//            $link->mail_title = $mail->title;
//            $link->mail_email = $email->email;
//            $link->_acl       = $mail->_acl;
//            $link->owner_id   = $mail->owner_id;
//            $this->getSubject()->getLogicService()
//                 ->get( 'update', 'EmailToMail' )->trigger( $link );
//            unset( $searchValues[ array_search( $link->mail_email,
//                    $searchValues ) ] );
//            $linkGW->save( $link );
//        }
//        if (count( $searchValues )) {
//            foreach ($searchValues as $email) {
//                $newLink             =
//                    $this->getSubject()->getModelServiceVerify()
//                         ->get( 'EmailToMail' );
//                $newLink->mail_email = $email;
//                $newLink->mail_id    = (string) $mail->_id;
//                $newLink->mail_field = 'from_to_title';
//                $newLink->mail_title = $mail->title;
//                $newLink->_acl       = $mail->_acl;
//                $newLink->owner_id   = $mail->owner_id;
//                $this->getSubject()->getLogicService()
//                     ->get( 'update', 'EmailToMail' )->trigger( $newLink );
//                $linkGW->save( $newLink );
//            }
//        }
//
//        $this->getSubject()->getLogicService()
//             ->get( 'updateTitle', 'MailDetail' )->trigger( $mail );
//    }
}
