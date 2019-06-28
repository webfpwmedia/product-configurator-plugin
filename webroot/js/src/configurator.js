/**
 * Sets up configurator form to request configuration from the server
 */

import $ from 'jquery';

/**
 * @param {jQuery} $element
 * @param {object} options
 * @constructor
 */
window.Configurator = function Configurator($element, options) {
    options = $.extend({
        imageBaseUrl: '/',
        formSelector: 'form',
        configurationSelector: '.image-stack',
        stateSelector: 'button.toggle-state',
        // optional query string to append to images (without preceding `?`)
        imageQueryString: null
    }, options);

    this.options = options;
    this.state = 'front';
    this.lastResponse = {};
    this.$configuration = $element.find(options.configurationSelector);
    this.$form = $element.find(options.formSelector);
    this.$stateToggle = $element.find(options.stateSelector);

    let c = this;
    this.$form.find(':input').on('change', function () {
        getConfiguration(c);
    });
    this.$stateToggle.on('click', function () {
        toggleState.call(c);
    });

    this.toggleState = function () {
        toggleState.call(c);
    };
    this.buildImageStack = function (response) {
        buildImageStack.call(c, response);
    };

    getConfiguration(c);
}

/**
 * Toggles state using the last response
 *
 * @return void
 */
function toggleState() {
    this.state = this.state === 'front' ? 'back' : 'front';
    this.buildImageStack(this.lastResponse)
}

/**
 * Builds image stack from a response and places it in the $configuration element
 *
 * @param {object} response
 * @return void
 */
function buildImageStack(response) {
    if (!response.hasOwnProperty('build')) {
        return;
    }

    let $html = $('<div></div>');

    if (response.build.hasOwnProperty('images')) {
        let c = this;
        response.build.images.forEach(function (componentImages) {
            if (!componentImages.hasOwnProperty(c.state)) {
                return;
            }

            let src = c.options.imageBaseUrl + componentImages[c.state]['path'];
            if (c.options.imageQueryString !== '') {
                src += '?' + c.options.imageQueryString;
            }

            let $img = $('<img>')
                .prop('src', src)
                .css({
                    zIndex: componentImages[c.state]['layer']
                });

            $html.append($img);
        });
    }

    this.$configuration.html($html);
}

/**
 * Triggers ajax request to fetch configuration response from selected options
 *
 * @param {Object<Configurator>} Configurator
 */
function getConfiguration(Configurator) {
    let request = $.ajax({
        url: Configurator.$form.prop('action'),
        data: Configurator.$form.serialize(),
        type: Configurator.$form.prop('method'),
        dataType: 'json'
    });

    request.done(function (response) {
        Configurator.lastResponse = response;
        Configurator.buildImageStack(response);
    });
}
