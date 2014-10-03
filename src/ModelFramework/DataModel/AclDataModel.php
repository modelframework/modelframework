<?php

namespace ModelFramework\DataModel;

use ModelFramework\AclService\AclDataAwareInterface;
use ModelFramework\AclService\AclDataAwareTrait;

class AclDataModel implements DataModelInterface, DataModelAwareInterface, AclDataAwareInterface
{

    use DataModelAwareTrait, AclDataAwareTrait;


    public function getModelName()
    {
        return $this->getDataModelVerify()->getModelName();
    }

    public function exchangeArray( array $data )
    {
        return $this->getDataModelVerify()->exchangeArray( $data );
    }

    public function toArray()
    {
        return $this->getDataModelVerify()->toArray();
    }

    public function getArrayCopy()
    {
        return $this->getDataModelVerify()->getArrayCopy();
    }

    public function __set( $name, $value )
    {
        $_aclData = $this->getAclDataVerify();
        if ( !is_array( $_aclData->permissions ) || !in_array( 'e', $_aclData->permissions ) )
        {
            throw new \Exception( 'writing is not allowed' );
        }
        if ( empty( $_aclData->fields[ $name ] ) )
        {
            return null;
        }
        if ( $_aclData->fields[ $name ] == 'x' )
        {
            return 'reading is not allowed';
        }
        if ( $_aclData->fields[ $name ] !== 'e' )
        {
            return 'writing is not allowed';
        }

        return $this->getDataModelVerify()->__set( $name, $value );
    }

    public function __get( $name )
    {
        if ( in_array( $name, [ '_model', '_label', '_adapter' ] ) )
        {
            return $this->getDataModelVerify()->{$name};
        }
        $_aclData = $this->getAclDataVerify();
        if ( !is_array( $_aclData->permissions ) || !in_array( 'r', $_aclData->permissions ) )
        {
            throw new \Exception( 'reading is not allowed' );
        }
        if ( empty( $_aclData->fields[ $this->getDataModelVerify()->getFieldSource( $name ) ] ) )
        {
            return null;
        }
        if ( $_aclData->fields[ $this->getDataModelVerify()->getFieldSource( $name ) ] == 'x' )
        {
            return 'reading is not allowed';
        }

        return $this->getDataModelVerify()->__get( $name );

    }

    public function __call( $name, $arguments )
    {
        return $this->getDataModelVerify()->__call( $name, $arguments );
    }

    public function __isset( $name )
    {
        return $this->getDataModelVerify()->__isset( $name );
    }

    public function __unset( $name )
    {
        return $this->getDataModelVerify()->__unset( $name );
    }

}