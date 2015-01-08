<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\DataMapping\DataMappingConfig\DataMappingConfig;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\Arr;
use ModelFramework\ViewService\View;

class ConvertObserver extends AbstractObserver
{
    public function process($aclModel)
    {
        $model = $this->getModelData();

        /**
         * @var View $subject
         */
        $subject = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();

        /**
         * @var Logic $logic
         */
        $logic = $subject->getLogicServiceVerify()->get('convert', $model->getModelName());

        if ($subject->getParamsVerify()->fromPost('object_id', null) !== null) {
            //            $subject->getLogicServiceVerify()->get( 'preconvert', $model->getModelName() )
//                    ->trigger( $model );
            $logic->setData([ 'save' => true ]);
        }

        $logic->trigger($model);

        $d = $logic->getData();
        prn($d);

        if (Arr::getDoubtField($logic->getData(), 'save', false)) {
            //            $subject->getLogicServiceVerify()->get( 'postconvert', $model->getModelName() )
//                    ->trigger( $model );

            $url = $subject->getBackUrl();
            if ($url == null || $url == '/') {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute('common', [ 'data' => strtolower($viewConfig->model), 'view' => 'list' ]);
            }
            $subject->setRedirect($subject->refresh($model->getModelName().
                                                      ' data was successfully converted',
                                                      $url));
        }
    }

    public function update_b00bs(\SplSubject $subject)
    {
        $result     = [ ];
        $request    = $subject->getParams()->getController()->getRequest();
        $viewConfig = $subject->getViewConfigVerify();
        $modelName  = $viewConfig->model;
        $data       = strtolower($modelName);
        $id         = (string) $subject->getParams()->fromRoute('id', 0);
        $object     = $subject->getGatewayServiceVerify()->get($modelName)->get($id);

        $convertConfig =
            $subject->getConfigServiceVerify()->getByObject($modelName, new DataMappingConfig());

        prn($convertConfig);
        exit();

        $result[ 'convertedObjects' ] = [ ];
        foreach ($convertConfig->targets as $_key => $_value) {
            $convertObject = $subject->getGatewayServiceVerify()->get($_key)->model();
            foreach ($_value as $_k => $_v) {
                $convertObject->$_v = $object->$_k;
            }
            $result[ 'convertedObjects' ][ $_key ] = $convertObject;
        }
        $result[ 'model' ] = $object;
        $result[ 'id' ]    = $id;
        $subject->setData($result);

        if ($request->isPost()) {
            $subject->getLogicServiceVerify()->get('preconvert', $model->getModelName())
                    ->trigger($result[ 'convertedObjects' ]);

            foreach ($result[ 'convertedObjects' ] as $object) {
                $subject->getGatewayServiceVerify()->get($object->getModelName())->save($object);
            }

            $subject->getLogicServiceVerify()->get('postconvert', $model->getModelName())
                    ->trigger($result[ 'convertedObjects' ]);

            $url = $subject->getBackUrl();
            if ($url == null || $url == '/') {
                $url = $subject->getParams()->getController()->url()
                               ->fromRoute('common', [ 'data' => $data, 'view' => 'list' ]);
            }
            $subject->setRedirect($subject->refresh($modelName.
                                                      ' data was successfully converted',
                                                      $url));
        }

        return;
    }
}
