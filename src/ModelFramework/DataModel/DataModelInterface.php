<?php

namespace ModelFramework\DataModel;

interface DataModelInterface
{

    public function getModelName();

    public function getTableName();

    public function exchangeArray( array $data );

    public function getArrayCopy();

    public function toArray();

    public function __set( $name, $value );

    public function __get( $name );

    public function __call( $name, $arguments );

    public function __isset( $name );

    public function __unset( $name );

}