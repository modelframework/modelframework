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

//    /**
//     * @var array
//     */
//    protected $_fieldTypes = [ ];
//
//    /**
//     * @param array $systemConfig
//     *
//     * @return $this
//     * @throws \Exception
//     */
//    public function setSystemConfig( $systemConfig )
//    {
//        if ( !is_array( $systemConfig ) )
//        {
//            throw new \Exception( 'SystemConfig must be an array' );
//        }
//        $this->_fieldTypes = $systemConfig;
//
//        return $this;
//    }

    /**
     * @param string $type
     * @param string $part
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldPart( $type, $part )
    {
        $_systemConfig = $this->getSystemConfigVerify();
        if ( !isset( $_systemConfig[ $type ][ $part ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for ' .  $part );
        }

        return $_systemConfig[ $type ][ $part ];
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
     * @return mixed
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