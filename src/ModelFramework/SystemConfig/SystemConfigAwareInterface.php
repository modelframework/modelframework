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
     *
     * @return array
     */
    public function getConfigDomainPart( $domain, $key  );

    /**
     * @param string $domain
     *
     * @return array
     */
    public function getConfigDomainSystem( $domain );

    /**
     * @param string $domain
     *
     * @return array
     */
    public function getConfigDomainCustom($domain);

}