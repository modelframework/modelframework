<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\ModelService\ModelConfigParser;

interface ModelConfigParserAwareInterface
{
    /**
     * @param array $modelConfig
     *
     * @return $this
     */
    public function setModelConfigParser(ModelConfigParser $modelConfigParser);

    /**
     * @return array
     */
    public function getModelConfigParser();

    /**
     * @return array
     * @throws \Exception
     */
    public function getModelConfigParserVerify();
}
