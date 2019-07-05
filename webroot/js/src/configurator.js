/**
 * Sets up configurator form to request configuration from the server
 */

import $ from 'jquery';

let preloaded = [];

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
        imageQueryString: null,
        frontLabel: 'Front',
        backLabel: 'Back'
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
        c.toggleState();
    });
    this.toggleState = function () {
        c.state = c.state === 'front' ? 'back' : 'front';
        setState(c);
    };
    this.buildImageStack = function (response) {
        buildImageStack.call(c, response);
    };

    this.$form.find('[data-requires]').each(function () {
        const $this = $(this);
        const requires = $this.data('requires').split(':');
        $this.hide();

        const $requirement = $this
            .siblings('[data-component="' + requires[0] + '"][data-token="' + requires[1] + '"]')
            .find(':input');

        $requirement.change(function () {
            const $required = $(this);
            $required.val() ? $this.show() : $this.hide();

            if ($this.is(':hidden')) {
                const $thisInput = $this.find(':input');
                $thisInput.prop('checked', false);
                $thisInput.garlic('destroy');
                $thisInput.change();
            }
        });

        $requirement.filter(':checked').change();
    });

    getConfiguration(c);
    setState(c);
}

/**
 * Sets image state and changes state toggle label
 *
 * @param {Object<Configurator>} Configurator
 * @return void
 */
function setState(Configurator) {
    Configurator.buildImageStack(Configurator.lastResponse);
    Configurator.$stateToggle.html(Configurator.state === 'front' ? Configurator.options.backLabel : Configurator.options.frontLabel);
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
            // preload images
            if (componentImages.hasOwnProperty('front')) {
                preloadImage(getImageSrc(componentImages['front']['path'], c.options));
            }
            if (componentImages.hasOwnProperty('back')) {
                preloadImage(getImageSrc(componentImages['back']['path'], c.options));
            }

            if (!componentImages.hasOwnProperty(c.state)) {
                return;
            }

            let $img = $('<img>')
                .prop('src', getImageSrc(componentImages[c.state]['path'], c.options))
                .css({
                    zIndex: componentImages[c.state]['layer']
                });

            $html.append($img);
        });
    }

    this.$configuration.html($html);
}

/**
 * Gets an image src from a path and options
 *
 * @param {string} path
 * @param {object} options
 * @returns {string}
 */
function getImageSrc(path, options) {
    let src = options.imageBaseUrl + path;
    if (options.imageQueryString !== '') {
        src += '?' + options.imageQueryString;
    }

    return src;
}

/**
 * Preloads an image
 *
 * @param {string} src
 * @return void
 */
function preloadImage(src) {
    if (preloaded.indexOf(src) !== -1) {
        // image was already preloaded during this session
        return;
    }
    let img = new Image();
    img.src = src;
    preloaded.push(src);
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
