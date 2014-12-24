<?php

namespace ModelFramework\ConfigService;

use ModelFramework\Utility\Arr;

trait ConfigAwareTrait
{

    /**
     * @var array
     */
    private $_rootConfig = null;

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setRootConfig( $config )
    {
        if ( !is_array( $config ) )
        {
            throw new \Exception( 'Config must be an array' );
        }
        $this->_rootConfig = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getRootConfig()
    {
        return $this->_rootConfig;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRootConfigVerify()
    {
        $_rootConfig = $this->getRootConfig();
        if ( $_rootConfig == null || !is_array( $_rootConfig ) )
        {
            throw new \Exception( 'System config array does not set in the ConfigAware instance of ' .
                                  get_class( $this ) );
        }

        return $_rootConfig;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getConfigPart( $type )
    {
        return Arr::getDoubtField( $this->getRootConfigVerify(), $type, [ ] );
    }

    /**
     * @param string $domain
     * @param string $key
     * @param null $subKey
     *
     * @return null
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