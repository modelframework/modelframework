<?php

namespace ModelFramework\SystemConfig;


interface SystemConfigAwareInterface {

    /**
     * @param array $systemConfig
     *
     * @return $this
     * @throws \Exception
     */
    public function setSystemConfig( $systemConfig );

    /**
     * @return array
     */
    public function getSystemConfig();

    /**
     * @return array
     * @throws \Exception
     */
    public function getSystemConfigVerify();

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