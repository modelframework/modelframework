<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:09
 */

namespace ModelFramework\FormService\FormField\Strategy;

use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigInterface;
use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigInterface;
use ModelFramework\ModelService\ModelField\FieldConfig\FieldConfigInterface;
use Wepo\Model\Status;

class JLookupStrategy extends AbstractFormFieldStrategy
{

    public function s(
        FieldConfigInterface $conf,
        FormElementConfigInterface $_formElement,
        InputFilterConfigInterface $_inputFilter
    ) {
        $name = $this->getName() . '_id';
        if (!$this->isAllowed( $name ) || !$this->isNotLimited( $name )) {
            return [
                'elements' => [ ],
                'filters'  => [ ]
            ];
        }
        $_inputFilter->name                 = $name;
        $_formElement->options[ 'label' ]   = !empty( $conf->label )
            ? $conf->label : ucfirst( $this->getName() );
        $filter[ 'name' ]                   = $name;
        $_formElement->attributes[ 'name' ] = $name;
        if (!empty( $conf->required )) {
            $_formElement->attributes[ 'required' ] = 'required';
            if (!empty( $_formElement->options[ 'label_attributes' ][ 'class' ] )
                &&
                strlen( $_formElement->options[ 'label_attributes' ][ 'class' ] )
            ) {
                $_formElement->options[ 'label_attributes' ][ 'class' ] .= ' required';
            } else {
                $_formElement->options[ 'label_attributes' ]
                    = [ 'class' => 'required' ];
            }
        }
        $result = [
            'filters'  => [ $name => $_inputFilter ],
            'elements' => [ $name => $_formElement ]
        ];

        return $result;
    }

}
