<?php
/**
 * Class FormService
 *
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\AclService\AclConfig\AclConfig;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\Utility\Arr;
use Wepo\Lib\Acl;

class FormService
    implements FormServiceInterface, FieldTypesServiceAwareInterface,
               ConfigServiceAwareInterface, AclServiceAwareInterface,
               ModelServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ConfigServiceAwareTrait,
        AclServiceAwareTrait, DataModelAwareTrait, ModelServiceAwareTrait;

    private $_limitFields = [];

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function limitFields(array $fields = [])
    {
        $this->_limitFields = $fields;
        return $this;
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function get(DataModelInterface $model, $mode, array $fields = [])
    {
        return $this->getForm($model, $mode, $fields);
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function getForm(
        DataModelInterface $model,
        $mode,
        array $fields = []
    ) {
        return $this->createForm($model, $mode, $fields);
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function createForm(
        DataModelInterface $model,
        $mode,
        array $fields = []
    ) {

        $parsedFormConfig = $this
            ->setDataModel($model)
            ->limitFields($fields)
            ->parse();

        $form = new DataForm();
        $form->parseconfig($parsedFormConfig);
        return $model;
    }


    public function parse()
    {
        $dataModel = $this->getDataModelVerify();
        $modelName = $dataModel->getModelName();

        $formConfigParser = new FormConfigParser();

        $formConfigParser->setModelConfig(
            $this->getModelServiceVerify()->getModelConfig($modelName)
        );

        $aclData = null;
        if ($dataModel instanceof AclDataModel) {
            /**
             * @var AclConfig $aclData
             */
            $aclData = $dataModel->getDataPermissions();
            $formConfigParser->setAclData($aclData);
        }

        $formConfigParser->setFieldTypesService($this->getFieldTypesServiceVerify());
//        $formConfigParser->setModelConfi($modelConfig);
        $formConfigParser->init()->notify();

        return $formConfigParser;

    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function createForm0(
        DataModelInterface $model,
        $mode,
        array $fields = []
    ) {
        $configData = $this->getPermittedConfig($model, $mode);

        if (count($fields)) {
            $configFields       = $configData->fields;
            $configData->fields = [];
            foreach ($fields as $fieldName) {
                if (isset($configFields[$fieldName])) {
                    $configData->fields[$fieldName]
                        = $configFields[$fieldName];
                }
            }
        }

        $cf   = $this->getFormConfigParserServiceVerify()
            ->getFormConfig($configData);
        $form = new DataForm();

        return $form->parseconfig($cf);
    }

    /**
     * @param $model
     * @param $mode
     *
     * @return DataModelInterface|null
     * @throws \Exception
     */
    public function getPermittedConfig($model, $mode)
    {
        $fieldPermissions = $this->getFieldPermissions($model, $mode);
        $cd               = $this->getConfigServiceVerify()
            ->getByObject($model->getModelName(), new ModelConfig());
        $allowedFields    = [];
        foreach ($cd->fields as $k => $v) {
            if (in_array($k, $fieldPermissions)) {
                $allowedFields[$k] = $v;
            }
        }
        $cd->fields = $allowedFields;

        return $cd;
    }

    /**
     * @param $model
     * @param $mode
     *
     * @return array
     * @throws \Exception
     */
    public function getFieldPermissions($model, $mode)
    {
        $user = $this->getAuthServiceVerify()->getUser();
        $acl  = $model->getAclData();
        if ($acl) {
            $dataPermissions = $acl->data;
            $modePermissions = $acl->modes;
            $groups          = $user->groups;
            $groups[]        = $user->_id;
            if (is_array($model->_acl)) {
                foreach ($groups as $group_id) {
                    foreach ($model->_acl as $_acl) {
                        if ( !empty($_acl['role_id'])
                            && $_acl['role_id'] == $group_id
                        ) {
                            $dataPermissions = array_merge($dataPermissions,
                                Arr::getDoubtField($_acl, 'data', []));
                            $modePermissions = array_merge($modePermissions,
                                Arr::getDoubtField($_acl, 'modes', []));
                        }
                    }
                }
            }
            $dataPermissions = array_unique($dataPermissions);
            $modePermissions = array_unique($modePermissions);
            if ( !in_array(Acl::getDataPerm($mode), $dataPermissions)) {
                throw new \Exception("This data is not allowed for you");
            }
            if ( !in_array($mode, $modePermissions)) {
                throw new \Exception("This mode is not allowed for you");
            }
            $fieldPermissions = [];
            $fieldModes       = Acl::getFieldPerms($mode);
            foreach ($acl->fields as $k => $v) {
                if (in_array($v, $fieldModes)) {
                    $fieldPermissions[] = $k;
                }
            }
        } else {
            throw new \Exception("Incorrect acl data is in your account");
        }

        return $fieldPermissions;
    }
}
