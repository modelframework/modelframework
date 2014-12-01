<?php

namespace ModelFramework\ConfigService;


interface ConfigAwareInterface {

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setRootConfig( $config );

    /**
     * @return array
     */
    public function getRootConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getRootConfigVerify();

    /**
     * @param string $type
     * @throws \Exception
     *
     * @return array
     */
    public function getConfigPart( $type );

    /**
     * @param string $domain
     * @param string $key
     * @param null $subKey
     *
     * @return null
     */
    public function getConfigDomainPart( $domain, $key, $subKey = null );

    /**
     * @param string $domain
     * @param string $key
     *
     * @return array
     */
    public function getConfigDomainSystem( $domain, $key = null );

    /**
     * @param string $domain
     * @param string $key
     *
     * @return array
     */
    public function getConfigDomainCustom( $domain, $key = null );

}