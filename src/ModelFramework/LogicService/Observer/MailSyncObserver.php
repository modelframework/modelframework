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

        $mailGW  = $this->getSubject()->getGatewayService()->get( 'MailDetail' );
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
//            prn($newMail->getModelName());
            $this->getSubject()->getLogicService()->get('sync', $newMail->getModelName() )->trigger( $newMail );
//            prn($this->getSubject()->getLogicService()->get('sync', $newMail->getModelName ));
//            prn($newMail);
            $mailGW->save( $newMail );
            $count++;
        }

        return $count;
    }


    //todo rewrite update method to work in
    public function updateMailChains( $user )
    {
        $mailGW  = $this->getSubject()->getGatewayService()->get( 'MailDetail' );
        $chainGW = $this->getSubject()->getGatewayService()->get( 'Mail' );
        $modelService = $this->getSubject()->getModelServiceVerify();

        $noChainMails = $mailGW->find( [ 'chain_id' => '', 'owner_id' => $user->id() ] );
//        prn('all mails',$noChainMails->toArray());
//        exit;

        foreach ( $noChainMails as $mail )
        {
//            prn('//////////////////////////////////////////////////////////////////////////////////////////////');
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
            $chainWhere = array_unique( $chainWhere );
            $chains     = $chainGW->find( [ 'reference' => $chainWhere ] );
//            prn('chain search array', $chainWhere);
//            prn('chain search result', $chains->toArray());

            $chain            = $modelService->get( 'Mail' );
            $chain->reference = $chainWhere;
            $chain->title     = $mail->title;
            $chain->date      = $mail->date;
            $chain->count     = 1;
            $chain->status_id = $mail->status_id;
            $oldChainIds      = [ ];

            if ( count( $chains ) )
            {
                foreach ( $chains as $oldChain )
                {
                    $oldChainIds[ ]   = $oldChain->id();
                    $chain->reference = array_unique( array_merge( $chain->reference, $oldChain->reference ) );

                    $chainDate    = strtotime( $chain->date );
                    $oldChainDate = strtotime( $oldChain->date );
                    if ( $chainDate < $oldChainDate )
                    {
                        $chain->title = $oldChain->title;
                    }
                    else
                    {
                        $date        = new \DateTime( '@' . $oldChainDate );
                        $chain->date = $date->format( 'Y-m-d H:i:s' );//$oldChainDate;
                    }
                }
                $chain->_id     = array_pop( $oldChainIds );
                $mail->chain_id = $chain->_id;
            }
            $chain->count     = count( $chain->reference );
            $chain->reference = array_values( $chain->reference );

            try
            {
                $chain->owner_id = $user->id();

                $this->getSubject()->getLogicService()->get('sync', $chain->getModelName() )->trigger( $chain );
                $chainGW->save( $chain );
//                $mail->chain_id = $chainGW->getLastInsertId()?:$mail->chain_id;
//                $this->table('Mail')->save($mail);
                $chainGW->delete( [ '_id' => $oldChainIds ] );
//                $this->trigger('postsave',$chain);
            }
            catch ( \Exception $ex )
            {
                throw $ex;
                continue;
            }
        }

        foreach ( $noChainMails as $mail )
        {
            $message_id     = 'message-id';
            $chain          =
                $chainGW->findOne( [ 'reference' => [ $mail->header[ $message_id ] ] ] );
            $mail->chain_id = $chain->_id;
            $mailGW->save( $mail );
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