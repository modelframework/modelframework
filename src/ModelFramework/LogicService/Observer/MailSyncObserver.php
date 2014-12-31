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
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

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

        }
    }

    public function checkMailSettingExist( $actionType, $user = null )
    {
        if ( !$user )
        {
            $user = $this->user();
        }
        switch ( $actionType )
        {
            case 'sync':
                $settings = $this->table( 'MailSetting' )->find( [
                                                                     'user_id'          => $user->id(),
                                                                     'setting_protocol' => \Wepo\Model\MailSetting::receiveProtocols(),
                                                                     'status_id'        => [
                                                                         Status::NORMAL,
                                                                         Status::NEW_
                                                                     ]
                                                                 ] );
                break;
            case 'send': //change when mail setting become apart for send and sync
                $settings = $this->table( 'MailSetting' )->find( [
                                                                     'user_id'          => $user->id(),
                                                                     'setting_protocol' => \Wepo\Model\MailSetting::sendProtocols(),
                                                                     'status_id'        => [
                                                                         Status::NORMAL,
                                                                         Status::NEW_
                                                                     ]
                                                                 ] );
                break;
            default:
                throw new \Exception( 'actionType is wrong' );
                break;
        }

        return $settings;
    }

    public function syncAction()
    {
//        prn('done');
//        exit;
//        $settings = $this->checkMailSettingExist( 'sync', $this->user() );
        $count = 0;
//        /*
        try
        {
            $count = $this -> syncMails();
        }
        catch(\Exception $ex)
        {
//            throw $ex;
            $count = 0;
        }
        exit;
//        /*/
        $this -> updateMailChains();
//        exit;
        /**/
//        return $this -> refresh( $count . ' mails was successfully add', $this -> url() -> fromRoute( 'mail', ['action' => 'list' ] ) );
    }

    public function syncMails( $user = null )
    {
        $user = !is_null($user) ? $user : $this->user();
        ini_set( 'max_execution_time', 300 );
        $settings = $this->checkMailSettingExist( 'sync', $user );
        $count    = 0;
        $mails    = $this->table( 'Mail' )->find( [ 'owner_id' => $this->user()->id() ] );
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
//            prn($exceptUids);
//            exit();
            $syncService = $this->mail($setting);
            $fetchedMails = $syncService -> fetchAll( $exceptUids );
            if($syncService->lastSyncIsSuccessful())
            {
                $this->table('Mail')->delete(['header.message-id'=>'send']);
            }
//            prn( $fetchedMails );
            foreach ( $fetchedMails as $key => $mail )
            {
                if ( isset( $newMails[ $key ] ) )
                {
                    $newMails[ $key ]->protocol_ids =
                        array_merge( $mail->protocol_ids, $newMails[ $key ]->protocol_ids );
                }
                else
                {
                    $newMails[ $key ] = $this->model( 'Mail' )->exchangeArray( $mail );
                }
            }
        }

//        prn($newMails);
        $oldMails = count($newMails)? $this->table('Mail')->find([ 'header.message-id' => array_keys($newMails), 'owner_id' => $user->id()]):[];

        foreach($oldMails as $oldMail)
        {
            $newMail = $newMails[$oldMail->header['message-id']];
            unset($newMails[$oldMail->header['message-id']]);
            $oldMail->protocol_ids = array_merge( $newMail->protocol_ids, $oldMail->protocol_ids );
            $this->table('Mail')->save($oldMail);
        }

        foreach($newMails as $newMail)
        {
            $this->trigger('presave',$newMail);
            $this->table('Mail')->save($newMail);
            $count++;
        }

        return $count;
    }

    public function updateMailChains($user = null)
    {
        $user = is_null($user)? $this->user() : $user;
        $noChainMails = $this->table( 'Mail' )->find( [ 'chain_id' => '', 'owner_id' => $user->id() ] );
//        prn('all mails',$noChainMails->toArray());
//        exit;

        foreach ( $noChainMails as $mail )
        {
//            prn('//////////////////////////////////////////////////////////////////////////////////////////////');
            $MInReplyTo = isset($mail->header['in-reply-to'])?$mail->header['in-reply-to']:null;
            $MMessageId = $mail->header['message-id'];
            $MReferences = isset($mail->header['references'])?$mail->header['references']:[];
            $chainWhere = $MReferences;
            if(isset($MInReplyTo))
            {
                array_push($chainWhere, $MMessageId, $MInReplyTo);
            }
            else
            {
                array_push($chainWhere, $MMessageId);
            }
            $chainWhere = array_unique($chainWhere);
            $chains = $this->table('MailChain')->find(['reference'=>$chainWhere]);
//            prn('chain search array', $chainWhere);
//            prn('chain search result', $chains->toArray());


            $chain = $this->model('MailChain');
            $chain->reference = $chainWhere;
            $chain->title = $mail->title;
            $chain->date = $mail->date;
            $chain->count = 1;
            $chain->status_id = $mail->status_id;
            $oldChainIds = [];

            if(count($chains))
            {
                foreach($chains as $oldChain)
                {
                    $oldChainIds[] = $oldChain->id();
                    $chain->reference = array_unique(array_merge($chain->reference,$oldChain->reference ));

                    $chainDate = strtotime($chain->date);
                    $oldChainDate = strtotime($oldChain->date);
                    if($chainDate < $oldChainDate)
                    {
                        $chain->title = $oldChain->title;
                    }
                    else
                    {
                        $date = new \DateTime('@'.$oldChainDate);
                        $chain->date = $date->format('Y-m-d H:i:s');//$oldChainDate;
                    }
                }
                $chain->_id = array_pop($oldChainIds);
                $mail->chain_id = $chain->_id;
            }
            $chain->count = count($chain->reference);
            $chain->reference = array_values($chain->reference);


            try
            {
                $this->trigger('presave', $chain);
                $tr = $this->table('MailChain');
                $tr->save($chain);
//                $mail->chain_id = $tr->getLastInsertId()?:$mail->chain_id;
//                $this->table('Mail')->save($mail);
                $tr->delete( ['_id' => $oldChainIds] );
//                $this->trigger('postsave',$chain);
            }
            catch(\Exception $ex)
            {
                throw $ex;
                continue;
            }
        }

        foreach($noChainMails as $mail)
        {
            $message_id = 'message-id';
            $chain = $this->table('MailChain')->findOne(['reference'=>[$mail->header[$message_id]]]);
            $mail->chain_id = $chain->_id;
            $this->table('Mail')->save($mail);
        }
    }


}