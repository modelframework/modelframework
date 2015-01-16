<?php

namespace ModelFramework\AclService;

use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class AclDataModel implements DataModelInterface, DataModelAwareInterface,
                              AclConfigAwareInterface
{

    use DataModelAwareTrait, AclConfigAwareTrait;

    public function __clone()
    {
        $this->setDataModel( clone $this->getDataModel() );
        $this->setAclData( clone $this->getAclData() );
    }

    public function getModelName()
    {
        return $this->getDataModelVerify()->getModelName();
    }

    public function getTableName()
    {
        return $this->getDataModelVerify()->getTableName();
    }

    public function exchangeArray( array $data )
    {
        $this->getDataModelVerify()->exchangeArray( $data );

        return $this;
    }

    public function merge( $data )
    {
        $this->getDataModelVerify()->merge( $data );

        return $this;
    }

    public function split( $data )
    {
        $this->getDataModelVerify()->split( $data );

        return $this;
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
        if (!is_array( $_aclData->data ) ||
            !in_array( 'write', $_aclData->data )
        ) {
            throw new \Exception( 'writing is not allowed' );
        }
        if (empty( $_aclData->fields[ $name ] )) {
            return;
        }
        if ($_aclData->fields[ $name ] == 'x') {
            return 'reading is not allowed';
        }
        if ($_aclData->fields[ $name ] !== 'write') {
            return 'writing is not allowed';
        }

        return $this->getDataModelVerify()->__set( $name, $value );
    }

    public function __get( $name )
    {
        if (in_array( $name,
            [ '_model', '_label', '_adapter', '_acl', 'id' ] )) {
            return $this->getDataModelVerify()->{$name};
        }
        $_aclData = $this->getAclDataVerify();
        if (!is_array( $_aclData->data ) ||
            !in_array( 'read', $_aclData->data )
        ) {
            throw new \Exception( 'reading is not allowed' );
        }
        if (empty( $_aclData->fields[ $this->getDataModelVerify()
                                           ->getFieldSource( $name ) ] )
        ) {
            return;
        }
        if ($_aclData->fields[ $this->getDataModelVerify()
                                    ->getFieldSource( $name ) ] == 'x'
        ) {
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
