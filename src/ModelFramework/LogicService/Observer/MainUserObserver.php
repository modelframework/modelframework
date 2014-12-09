<?php
/**
 * Class AgeObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

class MainUserObserver
    implements \SplObserver
{
    /**
     * @param \SplSubject|Logic $subject
     */
    public function update( \SplSubject $subject )
    {
        $this->setMainUser( $subject );
//        This is a place for debugging results of Observers
//        prn($subject->getEventObject());
//        exit;
    }

    //creates new MainUser if it does not exist or update old one if MainUser already exist
    public function setMainUser( $subject )
    {
//        prn('_____________in observer____________________');
//        prn($subject);

        $mainUserGW = $subject->getGatewayService()->get('MainUser');
        $userGW = $subject->getGatewayService()->get('User');
        $user = $subject->getEventObject();

        $oldMainUser = $mainUserGW->find(['$or'=>[['_id'=>$user->main_id],['login'=>$user->login]]])->current();
//        prn($oldMainUser);
        if(isset($oldMainUser))
        {
            $mainUser = clone $oldMainUser;
            $mainUserId = (string) $mainUser->_id;
        }
        else{
            $mainUser = $subject->getModelService()->get('MainUser');
            $mainUserId = null;
        }
//        prn('old main user',$oldMainUser);

        $mainUser->exchangeArray($user->toArray());
        $mainUser->_id = $mainUserId;
        $mainUser->company_id = $subject->getAuthService()->getMainUser()->company_id;
//        prn($mainUser);
//        exit;
//        prn($mainUser,$oldMainUser);
        $mainUserGW->save($mainUser);
        $mainUserId = empty($mainUserId)? $mainUserGW->getLastInsertId() : $mainUserId;
        $user->main_id = $mainUserId;
        $userGW->save($user);

//        prn($mainUser);
//        prn($user);
//        prn('hello world');
//        prn('_____________end observer___________________');
//        exit;
    }
}