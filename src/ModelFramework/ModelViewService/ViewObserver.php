<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelViewService;

use ModelFramework\DataModel\AclDataModel;
use ModelFramework\DataModel\Custom\ModelConfigAwareInterface;
use ModelFramework\DataModel\Custom\ModelConfigAwareTrait;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareInterface;
use ModelFramework\DataModel\Custom\ViewConfigDataAwareTrait;
use ModelFramework\DataModel\DataModel;
use ModelFramework\GatewayService\GatewayAwareInterface;
use ModelFramework\GatewayService\GatewayAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Model\Table;

class ViewObserver
    implements \SplObserver
{

    public function update(\SplSubject $subject) {
        echo "I was updated by " . get_class($subject);
    }

}