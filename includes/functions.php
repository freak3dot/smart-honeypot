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
                break;
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
                '" id="' . $newName . '" value="' . trim($field['value']) . '" />';
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

    /**
     * Sanitize the field using PHP5 5.2.0 filters
     * @author Ryan Johnston
     * @param $field array Field Params
     * @return string Field HTML
     */
    function sanitizeField($field, $key, $trim = true){
        $key = makeField($field['name'], $key);

        if($trim){
            $_POST[$key] = trim($_POST[$key]);
        }

        if (version_compare(phpversion(), '5.2.0', '<')) {
            // Fallback - PHP version isn't high enough
            // skip sanitization
            return $_POST[$key];
        }

        switch ($field['type']){
            case 'email':
                return filter_var($_POST[$key], FILTER_SANITIZE_EMAIL);
                break;
            case 'number':
                return filter_var($_POST[$key], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                break;
            case 'url':
                return filter_var($_POST[$key], FILTER_SANITIZE_URL);
                break;
            default:
                return filter_var($_POST[$key], FILTER_SANITIZE_STRING);
        }
        // Code should never get here but here is default
        return $_POST[$key];
    }

    /**
     * Check the field type and determine if it is valid
     * @author Ryan Johnston
     * @param $field array Field Params
     * @return string Field HTML
     */
    function isValidField($field, $sanitizedValue, $key){
        // Set defaults
        $valid = true;
        $message = '';
        $key = makeField($field['name'], $key);

        // Check if field is required
        if(isset($field['required']) && (!isset($sanitizedValue) || empty($sanitizedValue))){
            $valid = false;
            $message = 'Please enter ' . $field['label'] .'. This field is required.';
        }
        switch ($field['type']){
            case 'email':
                if (version_compare(phpversion(), '5.2.0', '<')) {
                    // @TODO
                } else {
                    if(!filter_var($sanitizedValue, FILTER_VALIDATE_EMAIL)){
                        $valid = false;
                        $message = 'Please enter a valid email.';
                    }
                }
                break;
            case 'number':
                if(!is_numeric($sanitizedValue)){
                    $valid = false;
                    $message = 'Please enter a valid number.';
                }
                break;
            case 'url':
                if (version_compare(phpversion(), '5.2.0', '<')) {
                    // @TODO
                } else {
                    if(!filter_var($sanitizedValue, FILTER_VALIDATE_URL)){
                        $valid = false;
                        $message = 'Please enter a valid web address.';
                    }
                }
                break;
            case 'select':
                if (is_array($field['options'])){
                    $inOptionsArray = false;
                    foreach($field['options'] as $value => $visible){
                        if(isset($field['value']) && $sanitizedValue == $value){
                            $inOptionsArray = true;
                        }
                    }
                    if(!$inOptionsArray) {
                        $valid = false;
                        $message = 'Please select a valid option.';
                    }
                }
                break;
        }
        return $message;
    }

?>