<?php
/**
 * Class FormServiceInterface
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

interface FormServiceInterface
{

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function get( $modelName );

    /**
     * @param string $modelName
     *
     * @return DataForm
     */
    public function getForm( $modelName );

}