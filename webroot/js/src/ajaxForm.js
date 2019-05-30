/**
 * Turns forms with the 'ajax-form' class into ajax forms that submit
 * to the form's action and triggers callbacks based on responses:
 *
 * - onCakeError: Attribute on form that references a global function
 *   to call when there are CakePHP errors in the response (form, flash)
 * - onCakeSuccess: Attribute on form that references a global function
 *   to call when the response is successful
 *
 * For each callback, `this` is the original form and the response is
 * passed to the callback.
 */

import $ from 'jquery';

$(document).on('submit', 'form.ajax-form', function (event) {
    event.preventDefault();
    let $form = $(this);

    let ajax = $.ajax({
        url: $form.prop('action'),
        type: $form.prop('method'),
        data: $form.serialize()
    });

    $form.find('.form-group').removeClass('error');
    $form.find('.error-message').remove();

    ajax.done(function (response) {
        if ($form.attr('oncakeerror') && hasCakeErrors(response)) {
            window[$form.attr('oncakeerror')].call($form, response);
        } else if ($form.attr('oncakesuccess')) {
            window[$form.attr('oncakesuccess')].call($form, response);
        }
    });
});

/**
 * Checks if the response, whether JSON or HTML, has errors
 *
 * @param {string|object} response
 * @returns {boolean}
 */
function hasCakeErrors(response) {
    if (typeof response === 'object') {
        return response.hasOwnProperty('errors') && Object.keys(response.errors).length > 0;
    }

    let $response = $('<div>' + response + '</div>');
    let errorSelectors = ['.form-group.has-error', '.error-message'];

    return $response.find(errorSelectors.join(', ')).length > 0;
}
