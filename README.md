smart-honeypot
==============

PHP Script demonstrating a smart honey pot.


Gettings Started
---------------

1. Download the latest release.
2. Choose one of the forms: simple-form.php, hardened-form.php or bootstrap-form.php (simple-form.php is intended as a simple way to understand the script only. It is not intended for use in production.)
3. Set the contants
    1. Set the $salt. I recomend http://www.sethcardoza.com/tools/random-password-generator/ as a quick tool to get some random stuff to put in there.
    2. Set $recipients to a comma seperated list of emails (as accepted by php mail function)
    3. Set $senderName. This will be included in the from address of the email.
    4. Set $sender. This is the email address where the form originates. Typically something like noreply@example.com.
    5. Set $subject. This is the email subject.
4. Modify the form to include the fields you need.
     1. `action` defaults to the current script address
     2. `method` should remain set at post. Changing this will cause the form not to send emails.
     3. `fields` is an array of inputs (and form elements) that you would like in your form.
        * You can use the following form elements: `text`, `url`, `email`, `number`, `phone`/`tel`, `checkbox`, `file`, `textarea`, `select`, and `submit` 
        * The last form input should always be the `submit`.
5. Put the form on your server.
6. Test the form. Because, you could break something when you set the above. This especially applies to the `fields`.



Form Elements
-------------

Every form element is expected to have a `name` and `id`. The script may throw errors if these values are not set.
The text based inputs allow you to set a `placeholder` and default `value`. I have tried to make these names intuitive and based on the HTML input attributes.

To make an element required, simply add `required => true`.

#### text ####

Supports `name`, `id`, `value` and `placeholder`. 

#### url ####

Supports `name`, `id`, `value` and `placeholder`. 

#### email ####

Supports `name`, `id`, `value` and `placeholder`. 

#### number####

Supports `name`, `id`, `value` and `placeholder`. 

#### phone ####

Supports `name`, `id`, `value` and `placeholder`. 

There is no _validation_ on a phone number.

#### tel ####

Synonym of phone. See above.

#### checkbox ####

Supports `name`, `id`,  and `value`. 

#### file ####

Supports `name` and `id`.

There is no _validation_ on a file field. However, if you use a file file the enctype will be set on the form automatically.


#### textarea ####

Supports `name` and `id` and default `value`. Does *not* currently support rows and cols.

#### select ####

Supports `name`, `id`, default `value`. The default `value` set on the select will be selected in the options array.

You can specify the `options` as value => display array. The value will be used as the value on the option and the display will be shown to the end user.

In the hardened-form and the bootstrap-form, the script will ensure that the user submited a valid choice from the options.

#### submit ####

Required final element.


Unsupported Form Elements
-------------------------

#### radio ####

Radio buttons are tricky to implement under this model due to the use of the same name across radios. They will be added at some point. Currently, using `radio` will trigger and exception.

#### range ####

HTML5 added range as a possible input type. It appears to be pretty well supported accross browser. Having never used it or seen it in use on a website, I did not ad it to this script. Using `range` will trigger and exception.


Todos
-----

#### Testing ####
I last left off having written this script and getting to a good stopping point. I need to do some more testing.

#### jQuery Validaton Plugin ####

I wrote some server side validation and would like to extend that to some client side validation. The hardened-form and bootstrap-form should both dynamically generate jQuery to validate the form client side.

#### Bootstrap ####

There are references to bootstrap in the code and this readme. I plan to add a bootstrap-form script. It is dependent on testing the hardened-form and adding jQuery validation to the hardened-form.



