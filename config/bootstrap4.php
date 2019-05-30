<?php
/**
 * Bootstrap 4 form templates.
 *
 * @return array
 */

return [

    'button' => '<button class="btn btn-primary text-uppercase {{class}}" {{attrs}}>{{text}}</button>',
    'checkbox' => '<input class="form-check-input {{class}}" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
    'checkboxFormGroup' => '{{label}}',
    'checkboxWrapper' => '<div class="form-check">{{label}}</div>',
    'dateWidget' => '{{year}}{{month}}{{day}}{{hour}}{{minute}}{{second}}{{meridian}}',
    'error' => '<div class="error-message">{{content}}</div>',
    'errorList' => '<ul>{{content}}</ul>',
    'errorItem' => '<li>{{text}}</li>',
    'file' => '<input type="file" name="{{name}}"{{attrs}}>',
    'fieldset' => '<fieldset{{attrs}}>{{content}}</fieldset>',
    'formStart' => '<form{{attrs}}>',
    'formEnd' => '</form>',
    'formGroup' => '{{afterOpen}}{{label}}{{input}}{{beforeClose}}',
    'hiddenBlock' => '<div style="display:none;">{{content}}</div>',
    'input' => '<input class="form-control {{class}}" type="{{type}}" name="{{name}}"{{attrs}}/>',
    'inputSubmit' => '<input class="btn btn-primary text-uppercase {{class}}" type="{{type}}"{{attrs}}/>',
    'inputContainer' => '<div class="form-group {{type}}{{required}} {{classContainer}}"{{attrs}}>{{content}}</div>',
    'inputContainerError' => '<div class="form-group {{type}}{{required}} has-error">{{content}}{{error}}</div>',
    'label' => '<label{{attrs}}>{{text}}</label>{{help}}',
    'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
    'legend' => '<legend>{{text}}</legend>',
    'multicheckboxTitle' => '<legend>{{text}}</legend>',
    'multicheckboxWrapper' => '<fieldset{{attrs}}>{{content}}</fieldset>',
    'option' => '<option value="{{value}}"{{attrs}}>{{text}}</option>',
    'optgroup' => '<optgroup label="{{label}}"{{attrs}}>{{content}}</optgroup>',
    'select' => '<select class="form-control {{class}}" name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
    'radio' => '<input type="radio" name="{{name}}" value="{{value}}"{{attrs}}>',
    'radioWrapper' => '{{label}}',
    'textarea' => '<textarea class="form-control {{class}}" name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'submitContainer' => '<div class="form-group submit">{{content}}</div>',

];
