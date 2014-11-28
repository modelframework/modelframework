<?php

namespace ModelFramework\SystemConfig;

use ModelFramework\Utility\Arr;

trait SystemConfigAwareTrait
{

    /**
     * @var array
     */
    private $_systemConfig = null;

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
        $this->_systemConfig = $systemConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getSystemConfigVerify()
    {
        $_systemConfig = $this->getSystemConfig();
        if ( $_systemConfig == null || !is_array( $_systemConfig ) )
        {
            throw new \Exception( 'System config array does not set in the SystemConfigAware instance of ' .
                                  get_class( $this ) );
        }

        return $_systemConfig;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getConfigPart( $type )
    {
        return Arr::getDoubtField( $this->getSystemConfigVerify(), $type, [ ] );
    }

    /**
     * @param string $domain
     * @param string $key
     *
     * @return array
     */
    public function getConfigDomainPart( $domain, $key, $subKey = null )
    {
        $domainConfig = Arr::getDoubtField( $this->getConfigPart( $domain ), $key, [ ] );

        if ( $subKey !== null )
        {
            $domainConfig = Arr::getDoubtField( $domainConfig, $subKey, [ ] );
        }

        return $domainConfig;

    }

    /**
     * @param string $domain
     * @param string $key
     *
     * @return array
     */
    public function getConfigDomainSystem( $domain, $key = null )
    {
        return $this->getConfigDomainPart( $domain, 'system', $key );
    }

    /**
     * @param string $domain
     * @param string $key
     *
     * @return array
     */
    public function getConfigDomainCustom( $domain, $key = null )
    {
        return $this->getConfigDomainPart( $domain, 'custom', $key );
    }

}