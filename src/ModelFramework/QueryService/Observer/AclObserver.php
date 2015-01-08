<?php
/**
 * Class AclObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class AclObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);
//        prn( $subject->getData());
        $config = $this->getRootConfig();
        $user            = $subject->getAuthServiceVerify()->getUser();
        $where  = [ ];
        $match = [
//            'type'=> [ 'owner', 'shared', 'hieararhy' ],
//            'role_id'=> [ $user->id(), $user->role_id ],
            'permissions' => $this->getConfigPart('permissions'),
        ];

//        prn($this->getConfigPart( 'type' ));

        if (!count($this->getConfigPart('type'))) {
            $match[ 'type' ]  = [ 'owner', 'shared', 'hierarchy' ];
            $match[ 'role_id' ] = [ $user->id(), $user->role_id ];
//            'type'=> [ 'owner', 'shared', 'hieararhy' ],
//            'role_id'
        }

        if (in_array('owner', $this->getConfigPart('type'))) {
            $match[ 'type' ][] = 'owner';
            $match[ 'role_id' ][] = $user->id();
        }

        if (in_array('hierarchy', $this->getConfigPart('type'))) {
            $match[ 'type' ][] = 'hierarchy';
            $match[ 'role_id' ][] = $user->role_id();
        }

        if (in_array('shared', $this->getConfigPart('type'))) {
            $match[ 'type' ][] = 'shared';
            $match[ 'role_id' ] = [ $user->id(), $user->role_id ];
        }

        $user            = $subject->getAuthServiceVerify()->getUser();
//            $where [ 'acl.role_id' ] = [$user->id(), $user->role_id];
//            prn($user);
//            $where [ 'acl.permissions' ] = $config[ 'permissions' ];

            $where [ '_acl' ] = [ '$elemMatch' => $match ];
//            $where [ 'acl.role_id' ] = [$user->id()
//                '$elemMatch' => [ 'role_id' => $user->id() ], 'permissions' => [ '$in' => [ $config[ 'permissions' ] ] ]
//                    'permissions' => [ '$in' => $config[ 'permissions' ] ]
//            ];
//        }
        $subject->setWhere($where);
//        prn($where);

//        prn($subject);
//        prn( $subject->getData() );
//        exit;

//        $user = $subject->getAuthServiceVerify()->getUser();
//
//        foreach ( $this->getRootConfig() as $field => $value )
//        {
//            if ( $user->role_id == $value )
//            {
//                $where[ $field ] = $user->id();
//            }
//        }
//
//        # I need to know permissions !
//        $subject->setData( [ 'where' => $where ] );
    }

    /*
     *
     * db.Lead.find( { "acl":{ $elemMatch:  { "name" : { $in: ["Users", "Admins", "User_1253216378126371"] }, "permissions": { $in: ['c', 'r'] } } } } )
     *
     */
}
