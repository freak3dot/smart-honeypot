<?php
/** Functions used in smart honeypot scirpts.
 *
 * @author Ryan Johnston
 * @copyright 2014 Ryan Johnston
 * @license MIT
 * @link https://github.com/freak3dot/smart-honeypot
 * @version 0.1.0-beta
 */


    /**
     * Determine if form has file for enctype=multipart in the form
     * @author Ryan Johnston
     * @param $form object Form Object
     * @return boolean
     */
    function formHasFiles($form){
        if (is_array($form['fields'])){
            foreach($form['fields'] as $field){
                if($field['type'] === 'file'){
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    /**
     * Determine if form has file for enctype=multipart in the form
     * @author Ryan Johnston
     * @param $name string Field Name
     * @param $customAddOn string Salt (optional)
     * @return string md5ed representation of field name
     */
    function makeField($name, $customAddOn = false){
        if($customAddOn === false){
            $customAddOn = $addOn;
        }
        return md5($name . $customAddOn);
    }

    /**
     * Create a field based on params
     * @author Ryan Johnston
     * @param $field array Field Params
     * @param $newName string Name of field pre-passed through makeField
     * @return string Field HTML
     */
    function getFieldByType($field, $newName){
        $html = '';
        switch ($field['type']){
            case 'phone':
            case 'email':
            case 'number':
            case 'tel':
            case 'url':
            case 'text':
                $html = '<input type="text" name="' . $newName .
                '" id="' . $newName . '" value="' . $field['value']
                . '" placeholder="' . $field['placeholder'] . '"/>';
                break;
            case 'file':
                $html = '<input type="file" name="' . $newName .
                '" id="' . $newName . '"/>';
            case 'checkbox':
                $html = '<input type="checkbox" name="' . $newName .
                '" id="' . $newName . '"/>';
                break;
            case 'textarea':
                $html = '<textarea name="' . $newName . '" id="' . $newName
                . '">' . $field['value'] . '</textarea>';
                break;
            case 'select':
                $html = '<select name="' . $newName . '" id="' . $newName
                . '">';
                if (is_array($field['options'])){
                    foreach($field['options'] as $value => $visible){
                        $html .= '<option value="' . $value . '"';
                        if(isset($field['value']) && $field['value'] == $value){
                            $html .= ' selected="selected"';
                        }
                        $html .= '>' . $visible . '</option>';
                    }
                }
                $html .= '</select>';
                break;
            case 'submit':
                $html = '<input type="submit" name="' . $newName .
                '" id="' . $newName . '" value="' . $field['value'] . '" />';
                break;
            case 'radio':
                throw new Exception('Radio button not implemented.');
                break;
            case 'range':
                throw new Exception('HTML5 range not implemented.');
                break;
        }
        $html .= '<br/>';
        return $html;
    }

?>