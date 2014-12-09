<?php
/**
 * Class AclObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\Utility\SplSubject\SubjectAwareTrait;

class AclObserver extends AbstractObserver
{

    use SubjectAwareTrait;

    public function process( $model, $key, $value )
    {
        $user = $this->getSubject()->getAuthService()->getUser();
        $acl  = [ ];
        if ( isset( $value[ 'owner' ] ) )
        {
            $acl[ ] = [
                'type'        => 'owner',
                'role'        => 'owner',
                'role_id'     => $user->id(),
                'permissions' => $value[ 'owner' ]
            ];
        }
        if ( isset( $value[ 'hierarchy' ] ) )
        {
            foreach ( $value[ 'hierarchy' ] as $_key => $_value )
            {
                $roleClass = 'Wepo\Model\Role';
                $ucRole    = strtoupper( $_key );
                $acl[ ]    = [
                    'type'        => 'hierarchy',
                    'role'        => $_key,
                    'role_id'     => constant( $roleClass . '::' . $ucRole ),
                    'permissions' => $_value
                ];
            }
        }
        $model->$key = $acl;
    }

}