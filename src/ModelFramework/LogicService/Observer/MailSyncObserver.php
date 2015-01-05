<?php
/**
 * Class AclObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\FormConfigParserService\StaticDataConfig\StaticDataConfig;
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
        if ( !( is_array( $users ) || $users instanceof ResultSetInterface ) )
        {
            $users = [ $users ];
        }

        foreach ( $users as $_k => $user )
        {
            try
            {
                $count = $this->syncMails( $user );
            }
            catch ( \Exception $ex )
            {
//                prn($ex->getMessage());
//            throw $ex;
                $count = 0;
            }
//            prn($count);
//            exit;

            $this->updateMailChains( $user );
            exit;
        }
    }

    public function getSyncSetting( $user )
    {
//        prn( $user->id() );
        $protocols = array_keys( $this->getSubject()->getConfigServiceVerify()
                                      ->get( 'StaticDataSource', 'ReceiveMailProtocol',
                                             new StaticDataConfig() )->options );
//        prn( $protocols );
        $gw = $this->getSubject()->getGatewayService()->get( 'MailReceiveSetting' );
//        exit;
        $settings = $gw->find( [
                                   'user_id'             => $user->id(),
                                   'setting_protocol_id' => $protocols,
                                   'status_id'           => [
                                       Status::NORMAL,
                                       Status::NEW_
                                   ]
                               ] );
//        prn( $settings->toArray() );
//        exit;
//        switch ( $actionType )
//        {
//            case 'sync':
//                $settings = $this->table( 'MailSetting' )->find( [
//                                                                     'user_id'          => $user->id(),
//                                                                     'setting_protocol' => \Wepo\Model\MailSetting::receiveProtocols(),
//                                                                     'status_id'        => [
//                                                                         Status::NORMAL,
//                                                                         Status::NEW_
//                                                                     ]
//                                                                 ] );
//                break;
//            case 'send': //change when mail setting become apart for send and sync
//                $settings = $this->table( 'MailSetting' )->find( [
//                                                                     'user_id'          => $user->id(),
//                                                                     'setting_protocol' => \Wepo\Model\MailSetting::sendProtocols(),
//                                                                     'status_id'        => [
//                                                                         Status::NORMAL,
//                                                                         Status::NEW_
//                                                                     ]
//                                                                 ] );
//                break;
//            default:
//                throw new \Exception( 'actionType is wrong' );
//                break;
//        }

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

        foreach ( $settings as $setting )
        {
            $exceptUids = [ ];
            foreach ( $mails as $mail )
            {
                if ( isset( $mail->protocol_ids[ $setting->id() ] ) )
                {
                    $exceptUids[ ] = $mail->protocol_ids[ $setting->id() ];
                }
            }
//            prn($exceptUids, $setting);
//            exit();
            $syncService  = $this->mail( $setting );
            $fetchedMails = $syncService->fetchAll( $exceptUids );
//            prn($fetchedMails);
            if ( $syncService->lastSyncIsSuccessful() )
            {
                $mailGW->delete( [ 'header.message-id' => 'send' ] );
            }
//            prn( $fetchedMails );
//            exit;
            foreach ( $fetchedMails as $key => $mail )
            {
                if ( isset( $newMails[ $key ] ) )
                {
                    $newMails[ $key ]->protocol_ids =
                        array_merge( $mail->protocol_ids, $newMails[ $key ]->protocol_ids );
                }
                else
                {
//                    $newMails[ $key ] = $this->model( 'Mail' )->exchangeArray( $mail );
                    $newMails[ $key ] = $modelService->get( 'MailDetail' )->exchangeArray( $mail );
                }
            }
        }

//        prn($newMails);
        $oldMails = count( $newMails ) ? $mailGW->find( [
                                                            'header.message-id' => array_keys( $newMails ),
                                                            'owner_id'          => $user->id()
                                                        ] ) : [ ];

        foreach ( $oldMails as $oldMail )
        {
            $newMail = $newMails[ $oldMail->header[ 'message-id' ] ];
            unset( $newMails[ $oldMail->header[ 'message-id' ] ] );
            $oldMail->protocol_ids = array_merge( $newMail->protocol_ids, $oldMail->protocol_ids );
            $mailGW->save( $oldMail );
        }

        foreach ( $newMails as $newMail )
        {
            $newMail->owner_id = $user->id();
            $this->getSubject()->getLogicService()->get( 'sync', $newMail->getModelName() )->trigger( $newMail );
            $newMail->title = $newMail->header[ 'subject' ];
            $newMail->date  = ( new \DateTime( $newMail->header[ 'date' ] ) )->format( 'Y-m-d H:i:s' );
            $mailGW->save( $newMail );
            $count++;
        }

        return $count;
    }


    //todo rewrite update method to work in
    public function updateMailChains( $user )
    {
        $mailGW       = $this->getSubject()->getGatewayService()->get( 'MailDetail' );
        $chainGW      = $this->getSubject()->getGatewayService()->get( 'Mail' );
        $modelService = $this->getSubject()->getModelServiceVerify();

        $mails        = $mailGW->find( [ 'chain_id' => '', 'owner_id' => $user->id() ] );
        $noChainMails = [ ];
        foreach ( $mails as $mail )
        {
            $noChainMails[ ] = $mail;
        }

        while ( count( $noChainMails ) )
        {
            $mail        = array_pop( $noChainMails );
            $chainMails  = [ ];
            $MInReplyTo  = isset( $mail->header[ 'in-reply-to' ] ) ? $mail->header[ 'in-reply-to' ] : null;
            $MMessageId  = $mail->header[ 'message-id' ];
            $MReferences = isset( $mail->header[ 'references' ] ) ? $mail->header[ 'references' ] : [ ];
            $chainWhere  = $MReferences;
            if ( isset( $MInReplyTo ) )
            {
                array_push( $chainWhere, $MMessageId, $MInReplyTo );
            }
            else
            {
                array_push( $chainWhere, $MMessageId );
            }

            foreach ( $noChainMails as $key => $mailToCheck )
            {
                $MInReplyTo     =
                    isset( $mailToCheck->header[ 'in-reply-to' ] ) ? $mailToCheck->header[ 'in-reply-to' ] : null;
                $MMessageId     = $mailToCheck->header[ 'message-id' ];
                $MReferences    =
                    isset( $mailToCheck->header[ 'references' ] ) ? $mailToCheck->header[ 'references' ] : [ ];
                $testChainWhere = $MReferences;
                if ( isset( $MInReplyTo ) )
                {
                    array_push( $testChainWhere, $MMessageId, $MInReplyTo );
                }
                else
                {
                    array_push( $testChainWhere, $MMessageId );
                }
//                prn($testChainWhere, $chainWhere);
                if ( count( array_intersect( $testChainWhere, $chainWhere ) ) )
                {
                    $chainWhere = array_unique( array_merge( $testChainWhere, $chainWhere ) );
                    reset( $noChainMails );
                    unset( $noChainMails[ $key ] );
                    $chainMails[ ] = $mailToCheck;
                }
            }
            $chainMails[ ] = $mail;

//            $chain = $modelService->get( 'Mail' );

            //todo find old chain
            $chain = $chainGW->find( [ 'reference' => array_values( $chainWhere ) ] )->current();
            $chain = isset( $chain ) ? $chain : $modelService->get( 'Mail' );

            $firstMailDate = strtotime( $chain->date );
            $lastMailDate  = strtotime( $chain->date );
            $title         = $chain->title;
            $date          = $chain->date;
            foreach ( $chainMails as $mail )
            {
                $mailDate = strtotime( $mail->date );
//                prn( $mail->date, $mailDate, $firstMailDate, $lastMailDate );
                if ( ( $mailDate < $firstMailDate ) || !$firstMailDate )
                {
                    $title         = $mail->title;
                    $firstMailDate = $mailDate;
                }
                if ( ( $mailDate > $lastMailDate ) || !$lastMailDate )
                {
                    $date         = $mail->date;//$oldChainDate;
                    $lastMailDate = $mailDate;
                }
            }

            $chain->reference = array_unique( array_merge( $chainWhere, $chain->reference ) );
            $chain->title     = $title;
            $chain->date      = $date;
            $chain->count     = count( $chainMails );
            $chain->status_id = Status::NEW_;
//            prn( 'result', $chain );

            try
            {
                $chain->owner_id = $user->id();

                $this->getSubject()->getLogicService()->get( 'sync', $chain->getModelName() )->trigger( $chain );
                $chainGW->save( $chain );
                foreach ( $chainMails as $mail )
                {
                    $mail->chain_id = $chainGW->getLastInsertId() ?: $mail->chain_id;
                    $mailGW->save( $mail );
                }
            }
            catch ( \Exception $ex )
            {
                throw $ex;
                continue;
            }
        }
    }


    /**
     * @param Object $setting
     *
     * @return \Mail\Receive\BaseTransport|\Mail\Send\BaseTransport
     */
    public function mail( $setting )
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

}