<?php

namespace ModelFramework\Utility;

class Arr
{

    public static function getDoubtField( $array, $key, $default = null )
    {
        if ( !empty( $array[ $key ] ) )
        {
            $result = $array[ $key ];
        }
        else
        {
            $result = $default;
        }

        return $result;
    }

    public static function addNotNull( $array, $key, $value )
    {
        if ( $value !== null )
        {
//            if ( !is_array( $array ) )
//            {
//                $array = [ ];
//            }
            $array[ $key ] = $value;
        }

        return $array;

    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays and preserveNumericKeys is false, the value
     * from the second array will be appended to the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the one of the first array.
     *
     * @param  array $a
     * @param  array $b
     * @param  bool  $preserveNumericKeys
     *
     * @return array
     *
     */
    public static function merge( array $a, array $b, $preserveNumericKeys = false )
    {
        return \Zend\Stdlib\ArrayUtils::merge( $a, $b, $preserveNumericKeys );
    }
}
