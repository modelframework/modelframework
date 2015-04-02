<?php

namespace ModelFramework\ListParamsService;



use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\Utility\Params\ParamsAwareInterface;
use ModelFramework\Utility\Params\ParamsAwareTrait;

class ListParamsService implements ListParamsServiceInterface, GatewayServiceAwareInterface, ParamsAwareInterface {
    use GatewayServiceAwareTrait, ParamsAwareTrait;

    public function getListParams( $hash )
    {
        $listParams = $this->getGatewayServiceVerify()->get('ListParams')
                          ->find(['label' => $hash]);
        if ($listParams->count() > 0) {
            $listParams = $listParams->current();
        } else {
            $listParams = null;
        }

        return $listParams;
    }

    public function generateLabel($viewConfig, $qparams)
    {
        $listParams = $this->getGatewayServiceVerify()->get('ListParams');
        $params        = $listParams->model();
        $params->rows = $qparams->fromQuery('rowcount',$viewConfig->rows);
        $params->p = $qparams->fromRoute('page','1');
        $params->sort = $qparams->fromRoute('sort','created_dtm');
        $params->desc = $qparams->fromRoute('desc','1');
        $params->letter = $qparams->fromQuery('letter',null);
        $params->w = $qparams->fromQuery('q',null);
        $checkParam     = $listParams->findOne([
            'rows' => $params->rows,
            'p' => $params->p,
            'sort' => $params->sort,
            'desc' => $params->desc,
            'letter' => $params->letter,
            'w' => $params->w,
        ]);
        if ($checkParam) {
            return $checkParam->label;
            }else
        {
            $params->label = md5(md5(time().uniqid()));
            try {
                $listParams->save($params);
            } catch (\Exception $ex) {
                $listParams->label = null;
            }
            return $params->label;
        }
    }
}
