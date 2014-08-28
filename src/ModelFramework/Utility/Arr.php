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
}
