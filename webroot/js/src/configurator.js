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
        stateSelector: 'button.toggle-state'
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
    this.buildImageStack = function () {
        buildImageStack.call(c);
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
    if (!response.hasOwnProperty('build') || !response.build.hasOwnProperty('images')) {
        return;
    }

    let $html = $('<div></div>');

    response.build.images.forEach(function (componentImages) {
        if (!componentImages.hasOwnProperty(this.state)) {
            return;
        }

        let $img = $('<img>')
            .prop('src', this.options.imageBaseUrl + componentImages[this.state]['path'])
            .css({
                zIndex: componentImages[this.state]['layer']
            });

        $html.append($img);
    });

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
        buildImageStack(response);
    });
}
