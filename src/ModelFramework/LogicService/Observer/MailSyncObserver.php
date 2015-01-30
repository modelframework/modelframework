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

        $users  = $subject->getEventObject();
        $action = $this->getRootConfig()[ 'action' ];
        $mails  = [ ];
        if (!( is_array( $users ) || $users instanceof ResultSetInterface )) {
            $users = [ $users ];
        }
        switch ($action) {
            case 'fetch':
                foreach ($users as $_k => $user) {
                    try {
                        list( $count, $resMails ) = $this->syncMails( $user );
                        $mails = array_merge( $mails, $resMails );
                    } catch ( \Exception $ex ) {
                        throw $ex;
                        $count = 0;
                    }
                }
                break;
            case 'send':
                foreach ($users as $user) {
                    $resMails = $this->sendMails( $user );
                    $mails    = array_merge( $mails, $resMails );
                }
                //todo move heare mail send observer logic
                break;
        }
        $this->getSubject()->getLogicService()
             ->get( 'postsync', 'MailDetail' )->trigger( $mails );
        exit;

    }

    public function getFetchSetting( $user )
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
            'user_id'   => $user->id(),
            //            'setting_protocol_id' => $protocols,
            'status_id' => [
                Status::NORMAL,
                Status::NEW_,
            ],
        ] );

        return $settings;
    }

    public function getSendSetting( $user )
    {
        //        prn( $user->id() );
        $protocols = array_keys( $this->getSubject()->getConfigServiceVerify()
                                      ->get( 'StaticDataSource',
                                          'SendMailProtocol',
                                          new StaticDataConfig() )->options );
//        prn( $protocols );
        $gw = $this->getSubject()->getGatewayService()
                   ->get( 'MailSendSetting' );
//        exit;
        $settings = $gw->find( [
            'user_id'   => $user->id(),
            //            'setting_protocol_id' => $protocols,
            'status_id' => [
                Status::NORMAL,
                Status::NEW_,
            ],
        ] );

        return $settings;
    }

    public function sendMails( $user )
    {
        $mails    = [ ];
        $settings = $this->getSendSetting( $user );
        foreach ($settings as $setting) {

            $mailsGW =
                $this->getSubject()->getGatewayService()->get( 'MailDetail' );
            $mails   =
                $mailsGW->find( [
                    'protocol_ids' => [ $setting->_id ],
                    'status_id'    => Status::SENDING
                ] );
            $gw      = $this->getSendTransport( $setting );
            foreach ($mails as $mail) {
                try {
                    $res = $gw->sendMail( [
                        'text'   => $mail->text,
                        'header' => $mail->header,
                        'link'   => [ ]
                    ] );
                } catch ( \Exception $ex ) {
                    $mail->errors =
                        array_merge( $ex->getMessage(), $mail->errors );
                }
                $mails[ ] = $mail;
            }
        }
        return $mails;

    }


    public function syncMails( $user )
    {
        ini_set( 'max_execution_time', 300 );
        $settings = $this->getFetchSetting( $user );
        $count    = 0;

        //prn( $this->getSubject()->getModelServiceVerify()->get( 'MailDetail' ) );
        //exit;
        $modelService = $this->getSubject()->getModelServiceVerify();

        $mailGW = $this->getSubject()->getGatewayService()->get( 'MailDetail' );
        //$chainGW = $this->getSubject()->getGatewayService()->get( 'Mail' );

        $mails    = $mailGW->find( [ 'owner_id' => $user->id() ] );
        $newMails = [ ];

        foreach ($settings as $setting) {
            $exceptUids = [ ];
            foreach ($mails as $mail) {
                if (isset( $mail->protocol_ids[ $setting->id() ] )) {
                    $exceptUids[ ] = $mail->protocol_ids[ $setting->id() ];
                }
            }
            //prn($exceptUids, $setting);
            //exit();
            $syncService  = $this->getFetchTransport( $setting );
            $fetchedMails = $syncService->fetchAll( $exceptUids );
            //prn($fetchedMails);
            //exit;
            if ($syncService->lastSyncIsSuccessful()) {
                $email        = $setting->email;
                $mailSendGW   = $this->getSubject()->getGatewayService()
                                     ->get( 'MailSendSetting' );
                $sendSettings = $mailSendGW->find( [
                    'email'   => $email,
                    'user_id' => $user->_id
                ] );
                $ssIds        = [ ];

                foreach ($sendSettings as $sendSetting) {
                    $ssIds[ ] = $sendSetting->_id;
                }
                $resMails = $mailGW->find( [
                    'status_id'    => Status::SENDING,
                    'protocol_ids' => $ssIds
                ] );
                $mailsToUnchain = [];
                foreach ($resMails as $mail) {
                    $mailsToUnchain[ ] = $mail;
                }
                $this->getSubject()->getLogicService()
                     ->get( 'delete', 'MailDetail' )
                     ->trigger( $mailsToUnchain );

                $mailGW->delete( [
                    'status_id'    => Status::SENDING,
                    'protocol_ids' => $ssIds
                ] );
            }
            //prn( $fetchedMails );
            //exit;
            foreach ($fetchedMails as $key => $mail) {
                if (isset( $newMails[ $key ] )) {
                    $newMails[ $key ]->protocol_ids =
                        array_merge( $mail->protocol_ids,
                            $newMails[ $key ]->protocol_ids );
                } else {
                    //$newMails[ $key ] = $this->model( 'Mail' )->exchangeArray( $mail );
                    $newMails[ $key ] = $modelService->get( 'MailDetail' )
                                                     ->exchangeArray( $mail );
                    $this->configureFetchedMail( $user, $setting,
                        $newMails[ $key ] );
                }
            }
        }

        //prn($newMails);
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
        $returnMails = [];

        foreach ($newMails as $newMail) {
            //$newMail->owner_id = $user->id();
            //$newMail->title = $newMail->header[ 'subject' ];
            //$newMail->date  = ( new \DateTime( $newMail->header[ 'date' ] ) )->format( 'Y-m-d H:i:s' );
            $this->getSubject()->getLogicService()
                 ->get( 'presave', 'MailDetail' )
                 ->trigger( $newMail );
            $mailGW->save( $newMail );
            $newMail->_id = $mailGW->getLastInsertId();
            //$this->createEmailToMail( $newMail );
            $returnMails[] = $newMail;
            $count++;
        }

        return [ $count, $returnMails ];
    }


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


    /**
     * @param Object $setting
     *
     * @return \Mail\Receive\BaseTransport|\Mail\Send\BaseTransport
     */
    public function getSendTransport( $setting )
    {
        $tm = $this->getSubject()->getMailService();

        $purpose      = 'Send';
        $protocolName = $setting->setting_protocol_id;
        $settingId    = (string) $setting->_id;

        $setting = [
            'name'              => 'Wepo',
            'host'              => $setting->setting_host,
            'port'              => $setting->setting_port,
            'connection_class'  => 'login',
            'connection_config' => [
                'ssl'      => $setting->setting_security_id,
                'username' => $setting->setting_user,
                'password' => $setting->pass,
            ],
        ];

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
}
