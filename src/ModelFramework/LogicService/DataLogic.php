<?php
/**
 * Class DataLogic
 * @package ModelFramework\LogicService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService;

use ModelFramework\BaseService\AbstractService;
use ModelFramework\DataModel\Custom\LogicConfigDataAwareInterface;
use ModelFramework\DataModel\Custom\LogicConfigDataAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareInterface;
use ModelFramework\ModelConfigParserService\ModelConfigParserServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareTrait;

class DataLogic extends AbstractService
    implements GatewayServiceAwareInterface, ModelConfigParserServiceAwareInterface, LogicConfigDataAwareInterface
{

    use ModelServiceAwareTrait, GatewayServiceAwareTrait, ModelConfigParserServiceAwareTrait, LogicConfigDataAwareTrait;

    private $_event = null;

    protected function getRules()
    {
        $this->getLogicConfigDataVerify()->rules;
    }

    protected function getModelName()
    {
        $this->getLogicConfigDataVerify()->getModelName();
    }

    protected function setEvent( $event )
    {
        $this->_event = $event;

        return $this;
    }

    protected function getEvent()
    {
        return $this->_event;
    }

}