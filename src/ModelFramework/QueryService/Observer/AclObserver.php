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
    public function update( \SplSubject $subject )
    {
        $this->setSubject( $subject );
//        prn( $subject->getData());
        $config = $this->getRootConfig();
        $where  = [ ];

        if ( $config[ 'owner' ] )
        {
            $user            = $subject->getAuthServiceVerify()->getUser();
//            $where [ 'acl.role_id' ] = [$user->id(), $user->role_id];
//            prn($user);
//            $where [ 'acl.permissions' ] = $config[ 'permissions' ];
            $where [ 'acl' ] = [ '$elemMatch'=>
                                     [
                                         'role_id'=> [ $user->id(), $user->role_id ],
                                         'permissions'=> $config[ 'permissions' ],
                                     ]
            ];
//            $where [ 'acl.role_id' ] = [$user->id()
//                '$elemMatch' => [ 'role_id' => $user->id() ], 'permissions' => [ '$in' => [ $config[ 'permissions' ] ] ]
//                    'permissions' => [ '$in' => $config[ 'permissions' ] ]
//            ];
        }
        $subject->setData( [ 'where' => $where ] );

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