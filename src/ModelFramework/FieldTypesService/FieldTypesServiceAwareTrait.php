<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 10:52 AM
 */

namespace ModelFramework\FieldTypesService;

trait FieldTypesServiceAwareTrait
{

    private $_fieldTypesService = null;

    /**
     * @param FieldTypesServiceInterface $fieldTypesService
     *
     * @return $this
     */
    public function setFieldTypesService( FieldTypesServiceInterface $fieldTypesService )
    {
        $this->_fieldTypesService = $fieldTypesService;

        return $this;
    }

    /**
     * @return FieldTypesServiceInterface
     */
    public function getFieldTypesService()
    {
        return $this->_fieldTypesService;
    }

    /**
     * @return FieldTypesServiceInterface
     * @throws \Exception
     */
    public function getFieldTypesServiceVerify()
    {
        $fieldTypesService = $this->getFieldTypesService();
        if ( $fieldTypesService == null || !$fieldTypesService instanceof FieldTypesServiceInterface )
        {
            throw new \Exception( 'FieldTypesService does not set in the FieldTypesServiceAware instance of ' .
                                  get_class( $this ) );
        }

        return $fieldTypesService;
    }

    protected function getUtilityFields( $modelName )
    {
        return $this->getFieldTypesServiceVerify()->getUtilityFields( $modelName );
    }

    protected function getField( $type )
    {
        return $this->getFieldTypesServiceVerify()->getField( $type );
    }

    protected function getInputFilter( $type )
    {
        return $this->getFieldTypesServiceVerify()->getInputFilter( $type );
    }
} 