<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */


namespace ModelFramework\ViewService\Observer;

/**
 * Build variable array to insert pattern in to template
 * Class ConstructPatternMenuObserver
 * @package ModelFramework\ViewService\Observer
 */
class ConstructPatternMenuObserver implements \SplObserver
{
    public function update(\SplSubject $subject)
    {

        $Recipient_id = $subject->getParam('recipient',0 );
        if (!$Recipient_id){
            return;
        }
        $dataModel = ucfirst($subject
            ->getGatewayServiceVerify()
            ->get('Email')
            ->findOne(['model_id'=>$Recipient_id])->data);
        $visibleFields['pattern_items'][$dataModel] = $subject
             ->getAclServiceVerify()
             ->getVisibleFields($dataModel);

        $subject->setData($visibleFields);
    }
}
