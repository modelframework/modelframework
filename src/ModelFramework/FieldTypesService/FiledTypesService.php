<?php

/**
 * Class FieldTypesService
 * @package ModelFramework\FieldTypesService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FieldTypesService;

use ModelFramework\SystemConfig\SystemConfigAwareInterface;
use ModelFramework\SystemConfig\SystemConfigAwareTrait;

class FiledTypesService implements FieldTypesServiceInterface, SystemConfigAwareInterface
{
    use SystemConfigAwareTrait;

    /**
     * @param string $type
     * @param string $part
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldPart( $type, $part )
    {
        $_config = $this->getConfigPart( $type );
        if ( !isset( $_config[ $part ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for ' .  $part );
        }

        return $_config[ $part ];
    }


    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getInputFilter( $type )
    {
        return $this->getFieldPart( $type, 'inputFilter' );
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getField( $type )
    {
        return $this->getFieldPart( $type, 'field'  );
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getFormElement( $type )
    {
        return $this->getFieldPart( $type, 'formElement'  );
    }

    /**
     * @param string $modelName
     *
     * @return array
     */
    public function getUtilityFields( $modelName = '' )
    {
        return [
            'fields'  =>
                [
                    '_id' => [
                        'type' => 'pk', 'datatype' => 'string', 'default' => '', 'label' => 'ID', 'source' => '_id'
                    ],
                    'acl' => [
                        'type' => 'field', 'datatype' => 'array', 'default' => [ ], 'label' => 'acl', 'source' => 'acl'
                    ],
                ],
            'filters' => [ '_id' => $this->getInputFilter( 'text' ) ],
        ];
    }

} 