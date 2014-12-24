<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class ViewObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $subject->setData( $query->getData() );

        $result = [ ];
        $model  = $subject->getGatewayVerify()->findOne( $query->getWhere() );
        if ( !$model )
        {
            throw new \Exception( 'Data not found' );
        }

        $data = $subject->getData();

        foreach ( [ 'actions', 'links' ] as $datapartam )
        {
            foreach ( $data[ $datapartam ] as $key => $link )
            {
                foreach ( [ 'routeparams', 'queryparams' ] as $keyparams )
                {
                    foreach ( $link[ $keyparams ] as $paramkey => $param )
                    {
                        if ( $param{0} == ':' )
                        {

                            $_f = substr( $param, 1 );
                            $data[ $datapartam ][ $key ][ $keyparams ][ $paramkey ] = $model -> $_f;
//                                $subject->getParam( substr( $param, 1 ), '' );

                        }
                    }

                }
            }
        }

        $result[ 'fieldsets' ] = [ ];

        $aclData              = $subject->getAclServiceVerify()->getAclData( $viewConfig->model );
        $modelFields           = $subject->getModelConfigVerify()[ 'fields' ];

        $usedGroups = [ ];
        foreach ( $viewConfig->fields as $field )
        {
            if ( !array_key_exists( $field, $modelFields ) )
            {
                continue;
            }
            $fConfig = $modelFields[ $field ];
            if ( $fConfig[ 'type' ] == 'field' )
            {
                //check $field in acl
                if ( !array_key_exists( $field, $aclData->fields )
                     || !in_array( $aclData->fields[ $field ], [ 'r', 'e' ] )
                )
                {
                    continue;
                }
            }
            if ( $fConfig[ 'type' ] == 'alias' )
            {
                //check $fConfig['source'] in acl
                if ( !array_key_exists( $fConfig[ 'source' ], $aclData->fields )
                     || !in_array( $aclData->fields[ $fConfig[ 'source' ] ], [ 'r', 'e' ] )
                )
                {
                    continue;
                }
            }
            if ( $fConfig[ 'type' ]=='source' )
            {
                if ( $fConfig['source']!==$field
                     || !array_key_exists( $fConfig[ 'source' ], $aclData->fields )
                     || !in_array( $aclData->fields[ $fConfig[ 'source' ] ], [ 'r', 'e' ] )
                )
                {
                    continue;
                }
            }
            if ( $fConfig[ 'type' ] == 'pk' )
            {
                continue;
            }

            $usedGroups[ $modelFields[ $field ][ 'group' ] ][ ] = $field;
        }

        $chosenGroups = [ ];
        $fieldSet2Group = [];
        foreach ( $viewConfig->groups as $grKey => $groupBlock )
        {
            if ( is_array($groupBlock) )
            {
                foreach ( $groupBlock as $group )
                {
                    if ( array_key_exists( $group, $usedGroups ) )
                    {
                        $chosenGroups[ $group ] = $usedGroups[ $group ];
                        $fieldSet2Group[ $group ] = $grKey;
                    }
                }

                continue;
            }

            if ( array_key_exists( $groupBlock, $usedGroups ) )
            {
                $chosenGroups[ $groupBlock ] = $usedGroups[ $groupBlock ];
                $fieldSet2Group[ $groupBlock ] = $groupBlock;
            }

        }

        foreach ( $chosenGroups as $group => $groupElements )
        {
            $fieldSet = $subject->getModelConfigVerify()[ 'fieldsets' ][ $group ];
            $elements = $fieldSet['elements'];
            $fieldSet['elements'] = [];
            foreach ( $groupElements as $field )
            {
                $fieldSet['elements'][ $field ] = $elements[ $field ];
            }
            $fieldSet['group'] = $fieldSet2Group[ $group ];

            $chosenGroups[ $group ] = $fieldSet;
        }

        $result[ 'fieldsets' ] = $chosenGroups;

        $subject->getLogicServiceVerify()->get( 'preview', $viewConfig->model )
                ->trigger( $model );

        $result[ 'model' ]   = $model;
        $result[ 'title' ]   = $viewConfig->title . ': ' . $model->title;
        $result[ 'actions' ] = $data[ 'actions' ];
        $result[ 'links' ]   = $data[ 'links' ];

        $subject->setData( $result );

        $subject->getLogicServiceVerify()->get( 'postview', $viewConfig->model )
                ->trigger( $model );
    }

}