<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use Zend\Db\ResultSet\ResultSetInterface;

abstract class LinkObserver
    implements \SplObserver, SubjectAwareInterface
{

    use SubjectAwareTrait;
    use ConfigAwareTrait;

    protected $linkSettings = [
        [
            'to'   => [
                'model'       => 'Mail',
                'search'      => [
                    'to',
                    'from'
                ],
                'storage'     => 'link_storage',
                'title_field' => 'link_view'
            ],
            'from' => [
                'models'  => 'Lead',
                'search'  => 'email',
                'storage' => 'email_id',
                'title'   => [
                    'title',
                    'email'
                ]
            ]
        ]
    ];

    /**
     * @param \SplSubject|Logic $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
        prn( $this->linkSettings );
        exit;
    }
}
