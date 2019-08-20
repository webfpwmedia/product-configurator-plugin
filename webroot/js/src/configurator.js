/**
 * Sets up configurator form to request configuration from the server
 */

import $ from 'jquery';
import Text from './text';

const CUSTOM_TEXT_INPUT = '__customtext';
const TEXT_INPUT = '__text';
let preloaded = [];
let changes = [];

/**
 * @param {jQuery} $element
 * @param {object} options
 * @constructor
 */
window.Configurator = function Configurator($element, options) {
    options = $.extend({
        originalImageSize: {},
        imageBaseUrl: '/',
        formSelector: 'form',
        configurationSelector: '.image-stack',
        stateSelector: 'button.toggle-state',
        // optional query string to append to images (without preceding `?`)
        imageQueryString: null,
        frontLabel: 'Front',
        backLabel: 'Back',
        layerDirection: 'asc'
    }, options);

    this.options = options;
    this.state = 'front';
    this.lastResponse = {};
    this.$configuration = $element.find(options.configurationSelector);
    this.$form = $element.find(options.formSelector);
    this.$stateToggle = $element.find(options.stateSelector);

    let c = this;
    this.$form.find(':input').on('change', function () {
        if (changes.length === 0) {
            Promise.all(changes).then(function () {
                changes = [];
                getConfiguration(c);
            });
        }
        changes.push(new Promise(function (resolve, reject) {
            resolve();
        }));
    });
    this.$stateToggle.on('click', function () {
        c.toggleState();
    });
    this.toggleState = function () {
        c.state = c.state === 'front' ? 'back' : 'front';
        setState(c);
    };
    this.buildImageStack = function () {
        buildImageStack.apply(c, arguments);
    };

    /**
     * Gets the input for a component/token
     *
     * @param {string} component
     * @param {string} token
     * @returns {jQuery}
     */
    this.getInput = function (component, token) {
        return c.getFieldset(component, token).find(':input');
    };

    /**
     * Gets the fieldset for a component/token
     *
     * @param {string} component
     * @param {string} token
     * @returns {jQuery}
     */
    this.getFieldset = function (component, token) {
        return c.$form
            .find('[data-component="' + component + '"]')
            .find('fieldset[data-token="' + token + '"]');
    };

    /**
     * Dispatches a change event only on the checked radio button OR the hidden text field
     *
     * @param {jQuery} $inputs jQuery collection of inputs
     * @param {object} eventData Extra event data
     */
    const dispatchChange = function ($inputs, eventData = {}) {
        const $checked = $inputs.filter(':radio').filter(':checked');
        const $input = $inputs.filter(':input[hidden]').not('[disabled]');

        $input.trigger('change', eventData);
        $checked.trigger('change', eventData);
    };

    /**
     * Checks if a jQuery collection of inputs either has a checked radio option or a hidden text field with a value
     *
     * @param {jQuery} $inputs jQuery collection of inputs
     * @returns {boolean}
     */
    const hasValue = function ($inputs) {
        return $inputs.is(':checked') || ($inputs.prop('hidden') && $inputs.val());
    };

    /**
     * Hides a container
     *
     * - hides the container
     * - unchecks selected options
     * - empties hidden text inputs (inherits with showOptions:false)
     *
     * @param {jQuery} $container
     */
    const hide = function ($container) {
        let $fieldset = $container;
        if (!$fieldset.is('fieldset')) {
            $fieldset = $container.find('fieldset');
        }

        $container.hide();

        $fieldset.find(':radio').filter(':checked')
            .prop('checked', false)
            .change();

        $fieldset.find(':input[hidden]').not('[disabled]')
            .val('')
            .change();
    };

    /**
     * Shows a container
     *
     * - shows the container
     * - triggers inherited radio options
     *
     * @param {jQuery} $container
     */
    const show = function ($container) {
        let $fieldset = $container;
        if (!$fieldset.is('fieldset')) {
            $fieldset = $container.find('fieldset');
        }

        // MUST trigger inherited options change() event before showing container so the option is inherited
        // as only invisible inputs can inherit
        const inherits = $fieldset.data('inherits') ? $fieldset.data('inherits').split(':') : false;
        if (inherits) {
            const $inherited = c.getInput(inherits[0], inherits[1]);
            dispatchChange($inherited, {
                skipRequires: true
            });
        }

        dispatchChange($fieldset.find(':input'));

        $container.show();
    };

    this.$form.find('fieldset[data-requires]').each(function () {
        const $this = $(this);
        const requires = $this.data('requires').split(':');

        const $requirement = c.getInput(requires[0], requires[1]);

        $requirement.change(function (event, data = {}) {
            if (data.skipRequires) {
                return;
            }
            hasValue($(this)) ? show($this) : hide($this);
        });

        if (!hasValue($requirement)) {
            hide($this);
        }
    });

    this.$form.find('fieldset[data-inherits]').each(function () {
        const $this = $(this);
        const $thisInput = $this.find(':input').not('[type=hidden]');
        const inherits = $this.data('inherits').split(':');

        const $inherited = c.getInput(inherits[0], inherits[1]);

        $inherited.change(function () {
            const $inherit = $(this);

            if ($thisInput.is(':radio')) {
                if ($thisInput.parent().is(':hidden')) {
                    $thisInput
                        .filter(function () {
                            return $(this).val() === $inherit.val()
                        })
                        .prop('checked', true)
                        .trigger('change', {
                            skipRequires: true
                        });
                }
            } else {
                $thisInput.val($inherit.val());
                let eventData = {};
                if ($thisInput.parent().is(':hidden')) {
                    eventData = {
                        skipRequires: true
                    };
                }
                $thisInput.trigger('change', eventData);
            }
        });
    });

    this.$form.find('[data-custom]').each(function () {
        const $this = $(this);
        const $fieldset = $this.closest('fieldset');
        const component = $fieldset.closest('.component-options').data('component');
        const customVal = $this.find('input').val();
        const $radios = $fieldset.find('input');
        const $text = $fieldset.find('input[name="' + component + '[' + TEXT_INPUT + ']"]');
        const $customInput = $fieldset.find('input[name="' + component + '[' + CUSTOM_TEXT_INPUT + ']"]');

        $customInput.on('keydown', function (event) {
            if (event.which === 13) {
                event.preventDefault();
            }
        });

        $radios.change(function () {
            const $selected = $radios.filter(':checked');
            $text.val($selected.closest('label').text());
            if ($selected.val() === customVal) {
                $customInput
                    .prop('hidden', false)
                    .prop('disabled', false);
                $text.val($customInput.val());
            } else {
                $customInput
                    .prop('hidden', true)
                    .prop('disabled', true);
            }
        });
    });

    this.$form.find('[data-toggle]').each(function () {
        $(this).change(function () {
            const $this = $(this);
            const $componentOptions = $this
                .parents('#component-' + $this.data('component-id'))
                .find('.component-options');

            $this.is(':checked') ? show($componentOptions) : hide($componentOptions);
        });
    });

    if (this.$form.length) {
        dispatchChange(this.$form.find(':input'));
        this.$form.find('[data-toggle]').change();
    }

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
 * @param {jQuery} $element
 * @return void
 */
function buildImageStack(response, $element) {
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

            const image = componentImages[c.state];

            const $img = $('<img>')
                .prop('src', getImageSrc(image['path'], c.options))
                .css({
                    zIndex: image['layer'] * (c.options.layerDirection === 'asc' ? 1 : -1)
                });

            $html.append($img);

            const state = c.state;

            $img.on('load', function () {
                let svgOptions = {};

                const $component = c.$form.find('[data-component="' + image['component'] + '"]');
                const $radios = $component.find(':radio');
                const $selected = $radios.filter(':checked');

                const $customInput = $component.find('input[name="' + image['component'] + '[' + CUSTOM_TEXT_INPUT + ']"]');

                const customInputChange = function () {
                    $svg.find('.text').text($(this).val());
                };
                $customInput.off('keyup', customInputChange);

                let text = '';

                // merge all custom text options for selected radios
                $selected.each(function () {
                    const $s = $(this);
                    const textOptions = $s.data('text');
                    if (textOptions && textOptions.hasOwnProperty(state)) {
                        // check if we should use this label as the text content
                        if (textOptions[state].hasOwnProperty('content') && textOptions[state].content === true) {
                            text = $s.closest('label').text();
                            if ($s.closest('label').data('custom')) {
                                text = $customInput.val();
                            }
                        }

                        svgOptions = Object.assign(svgOptions, textOptions[state]);
                    }

                    if ($s.closest('label').data('custom')) {
                        $customInput.on('keyup', customInputChange);
                    }
                });

                if (!svgOptions || !text) {
                    return;
                }

                const SVGText = new Text(
                    c.options.originalImageSize.width,
                    c.options.originalImageSize.height,
                    svgOptions
                );

                const $svg = $(SVGText.render(text))
                    .css({
                        zIndex: parseInt(image['layer']) + 1,
                        width: $img.width()
                    });

                $html.append($svg);
            });
        });
    }

    if (typeof $element === 'undefined') {
        $element = this.$configuration;
    }

    $element.html($html);
}

/**
 * Extracts a component item from a build response
 *
 * @param {string} component
 * @param {object} response
 * @returns {object}
 */
function getComponent(component, response) {
    return response.build.components.reduce(function (carry, item) {
        if (item.hasOwnProperty(component)) {
            carry = item[component];
        }

        return carry;
    }, {});
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
