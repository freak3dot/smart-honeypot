<?php
/** Simple upload form demonstrating a smart honeypot.
 * Processes the form to stop spam bots. Renames the normal fields on this page.
 * Then, creates a honeypot with one of the names of the original fields.
 * The Honeypot is removed with JavaScript.
 *
 * The simple form is intended to show the smart honeypot concepts only. In
 * light of that, it does not include important things such as sanitation and
 * validation. Please see hardened form for use in a production environment.
 *
 * @author Ryan Johnston
 * @copyright 2014 Ryan Johnston
 * @license MIT
 * @link https://github.com/freak3dot/smart-honeypot
 * @version 0.1.0-beta
 */

    include('includes/functions.php');
    include('includes/header.php');

    // Change the salt when you install this: http://www.sethcardoza.com/tools/random-password-generator/
    $salt = '(xPj(77ios0V5iikTZ9W!K1NQ)0aLexnLuKGNam1am7$(pO74KFf&22@m2rRgze&';
    $addOn = substr(sha1(time() . $salt), 0, 6);
    $recipients = 'test@example.com';
    $senderName = 'Administrator';
    $sender = 'admin@example.com';
    $subject = 'Simple Smart Honeypot';

    $form = [
        // Action set to current page
        'action' => htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']),
        'method' => 'post',
        'fields' =>[
            [
                'type' => 'text',
                'name' => 'name',
                'id' => 'id_name',
                'label' => 'Name',
                'placeholder' => 'Name',
                'value' => 'Smart Honeypot'
            ],
            [
                'type' => 'email',
                'name' => 'email',
                'id' => 'id_email',
                'label' => 'Email',
                'placeholder' => 'Email'
            ],
            [
                'type' => 'phone',
                'name' => 'phone',
                'id' => 'id_phone',
                'label' => 'Phone',
                'placeholder' => 'Phone'
            ],
            [
                'type' => 'select',
                'name' => 'hear',
                'id' => 'id_phone',
                'label' => 'How did you hear about us?',
                'value' => 'value3',
                'options' => [
                    'value' => 'Shown Value',
                    'value2' => 'Shown Value 2',
                    'value3' => 'Shown Value 3'
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'message',
                'id' => 'id_message',
                'label' => 'Message',
                'placeholder' => 'Message'
            ],
            [
                'type' => 'checkbox',
                'name' => 'newsletter',
                'id' => 'id_newsletter',
                'label' => 'Sign up for Newsletter',
                'value' => 'true',
                'checked' => true,
                'labelOrder' => 'before'
            ],
            [
                'type' => 'submit',
                'name' => 'submit',
                'id' => 'id_submit',
                'label' => 'Sign up for Newsletter',
                'value' => 'true',
                'checked' => true,
                'labelOrder' => 'before'
            ]
        ]
    ];


    // Determine where we will put the honeypot
    $insertAt = rand()&count($form['fields']);
    // Steal a label from one of the fields but not the honeypot
    $stealLabel = rand()&(count($form['fields'])-1);

    // Process Form
    $emailSent = false;
    $honeyApprove = true;
    // @todo send email with form
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $encoded = base64_decode($_POST['enc-type']);
        $encoded = rtrim($encoded, '');
        $postAddOn = substr($encoded, 0, -1);

        if (!empty($_POST[strtolower($form['fields'][substr($encoded, -1, 1)]['name'])])) {
            $honeyApprove = false;
        }

        foreach($form['fields'] as $field){

            // Sanitation
            $$field['name'] = sanitizeField($field, $postAddOn);
            // Validation
            ${'error' . $field['name']} = isValidField($field, $$field['name'],  $postAddOn);

         }

        // send email
        if(!isset($hasError) && $_POST[strtolower($form['fields'][substr($encoded, -1, 1)]['name'])] == '') {
            if (isset($recipients) && ($recipients !== '') ){
                $body = '';
                foreach($form['fields'] as $field){
                    $body .= $field['label'] . ':';
                    $body .= $_POST[makeField($field['name'], $postAddOn)] . "\n";
                }

                // Add some information to determine if form is legit
                $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
                $body .= "Browser: " . $_SERVER['HTTP_USER_AGENT'];

                // Add some headers to help it get through filters
                $headers = 'From: ' . $senderName . ' <' . $sender. '>' . "\r\n";
                $headers .= 'Return-Path: ' . $senderName . ' <' . $sender. '>' . "\r\n";
                $headers .= 'Reply-To: ' . $senderName . ' <' . $sender. '>' . "\r\n";

                mail($recipients, $subject, $body, $headers);
                $emailSent = true;
            }

        }
        if(!$honeyApprove){
            $emailSent = true; //lie to spam bots
        }
    }


    echo '<h1>Hardened Smart Honeypot Form</h1>';

    if(!$emailSent){
        echo '<form action="' . $form['action'] . '" method="' . $form['method'] . '"' . ( formHasFiles($form) ? ' enctype="multipart/form-data"': '') . '/>';
        // Render form

        $output = base64_encode($addOn . $stealLabel);
        echo '<input type="hidden" name="enc-type" value="' .$output . '"/>';
        $c = 0;
        foreach($form['fields'] as $field){
            // Add the honeypot at it's random location
            if($c==$insertAt){
                echo '<div>';
                echo '<label for="' . strtolower($form['fields'][$stealLabel]['id']) . '">' .
                    $form['fields'][$stealLabel]['label'] . '</label>';
                echo '<input type="text" name="' . $form['fields'][$stealLabel]['name'] .
                    '" id="' . strtolower($form['fields'][$stealLabel]['id']) . '" value="" placeholder="' .
                    $form['fields'][$stealLabel]['label'] . '" aria-required="true"/><br/>';
                echo '</div>';
            }

            // Insert field with encoded name/id
            $fieldName = makeField($field['name'], $addOn);
            echo '<label for="' . $fieldName . '">' .
                $field['label'] . '</label>';
            echo getFieldByType($field, $fieldName);

            $c++;
        }
        echo '</form>';
        // Add some raw JavaScript to remove the honeypot
        echo '<script type="text/JavaScript">tkvrmhp = document.getElementById("' . strtolower($form['fields'][$stealLabel]['id']) . '"); tkvrmhpp = tkvrmhp.parentNode; tkvrmhppp = tkvrmhpp.parentNode.removeChild(tkvrmhpp);</script>';
    } else {
        echo '<p>email sent</p>';
    }

  include('includes/footer.php');
?>