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

class MailSendObserver
    implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{

    use ConfigAwareTrait, SubjectAwareTrait;

    public function update( \SplSubject $subject )
    {
//        prn( 'in mail send logic observer' );
        $this->setSubject( $subject );

        $mails = $subject->getEventObject();
        if (!( is_array( $mails ) || $mails instanceof ResultSetInterface )) {
            $mails = [ $mails ];
        }
//        prn($mails);

        foreach ($mails as $mail) {
            $setting = $subject->getGatewayService()->get( 'MailSendSetting' )
                               ->find( [ '_id' => $mail->protocol_ids[ 0 ] ] )
                               ->current();
            $gw      = $this->getTransport( $setting );

            $res = $gw->sendMail( [
                'text'   => $mail->text,
                'header' => $mail->header,
                'link'   => [ ]
            ] );
//            prn($res);
        }
    }

    /**
     * @param Object $setting
     *
     * @return \Mail\Receive\BaseTransport|\Mail\Send\BaseTransport
     */
    public function getTransport( $setting )
    {
        $tm = $this->getSubject()->getMailService();

        $purpose      = 'Send';
        $protocolName = $setting->setting_protocol_id;
        $settingId    = (string) $setting->_id;

//        prn($setting);
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
//        prn($setting);

        return $tm->getGateway( $purpose, $protocolName, $setting, $settingId );
    }
}
