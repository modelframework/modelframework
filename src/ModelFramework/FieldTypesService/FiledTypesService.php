<?php

/**
 * Class FieldTypesService
 * @package ModelFramework\FieldTypesService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FieldTypesService;

class FiledTypesService implements FieldTypesServiceInterface
{

    /**
     * @var array
     */
    protected $_fieldTypes = [ ];

    /**
     * @param array $systemConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setSystemConfig( $systemConfig )
    {
        if ( !is_array( $systemConfig ) )
        {
            throw new \Exception( 'SystemConfig must be an array' );
        }
        $this->_fieldTypes = $systemConfig;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getInputFilter( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'inputFilter' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getInputFilter' );
        }

        return $this->_fieldTypes[ $type ][ 'inputFilter' ];
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getField( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'field' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getField' );
        }

        return $this->_fieldTypes[ $type ][ 'field' ];
    }

    /**
     * @param string $type
     *
     * @return array
     * @throws \Exception
     */
    public function getFormElement( $type )
    {
        if ( !isset( $this->_fieldTypes[ $type ][ 'formElement' ] ) )
        {
            throw new \Exception( 'Unknown type "' . $type . '" for getFormElement' );
        }

        return $this->_fieldTypes[ $type ][ 'formElement' ];
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
                    '_id' => [ 'type' => 'pk', 'datatype' => 'string', 'default' => '', 'label' => 'ID' ],
                    'acl' => [ 'type' => 'field', 'datatype' => 'array', 'default' => [ ], 'label' => 'acl' ],
                ],
            'filters' => [ '_id' => $this->getInputFilter( 'text' ) ],
        ];
    }

} 