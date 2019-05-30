/**
 * Sets up configurator form to request configuration from the server
 */

import $ from 'jquery';

let $configuration = $('#configuration');
let $form = $('form#configurator');
let state = 'front';

$form.find(':input').on('change', function () {
    showConfiguration();
});

function showConfiguration() {
    let request = $.ajax({
        url: $form.prop('action'),
        data: $form.serialize(),
        type: $form.prop('method'),
        dataType: 'json'
    });

    request.done(function (response) {
        let $html = $('<div></div>');

        response.data.forEach(function (componentImages) {
            if (!componentImages.hasOwnProperty(state)) {
                return;
            }

            let $img = $('<img>')
                .prop('src', componentImages[state]['path'])
                .css({
                    zIndex: componentImages[state]['layer']
                });

            $html.append($img);
        });

        $configuration.html($html);
    });
}

if ($form.length) {
    $(document).ready(function () {
        showConfiguration();
    });
}
