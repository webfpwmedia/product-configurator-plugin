/**
 * Sets up configurator form to request configuration from the server
 */

import $ from 'jquery';
import Text from './text';

const CUSTOM_TEXT_INPUT = '__customtext';
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
        customTextMap: {},
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

    this.$form.find('fieldset[data-requires]').each(function () {
        const $this = $(this);
        const $thisInput = $this.find(':input').not('[type=hidden]');
        const requires = $this.data('requires').split(':');
        $this.hide();

        const $requirement = c.getInput(requires[0], requires[1]);

        $requirement.change(function () {
            const $required = $(this);
            if ($required.is(':radio') && !$required.is(':checked')) {
                return;
            }
            $required.val() ? $this.show() : $this.hide();

            if ($this.is(':hidden')) {
                $thisInput.prop('checked', false);
                $thisInput.change();
            }
        });

        $requirement.filter(':checked').change();
    });

    this.$form.find('fieldset[data-inherits]').each(function () {
        const $this = $(this);
        const $thisInput = $this.find(':input');
        const inherits = $this.data('inherits').split(':');

        const $inherited = c.getInput(inherits[0], inherits[1]);

        $inherited.change(function () {
            const $inherit = $(this);
            if ($inherit.is(':radio') && !$inherit.is(':checked')) {
                return;
            }

            if ($thisInput.is(':radio')) {
                if ($thisInput.parent().is(':hidden')) {
                    $thisInput
                        .closest('fieldset')
                        .find(':radio')
                        .filter(function () {
                            return $(this).val() === $inherit.val()
                        })
                        .prop('checked', true)
                        .change();
                }
            } else {
                $thisInput
                    .val($inherit.val())
                    .change();
            }
        });

        $inherited.filter(':checked').change();
    });

    this.$form.find('[data-custom]').each(function () {
        const $this = $(this);
        const $fieldset = $this.closest('fieldset');
        const component = $fieldset.closest('.step-component').data('component');
        const customVal = $this.find('input').val();
        const $radios = $fieldset.find('input');
        const $customInput = $fieldset.find('input[name="' + component + '[' + CUSTOM_TEXT_INPUT + ']"]');

        $customInput.on('keydown', function (event) {
            if (event.which === 13) {
                event.preventDefault();
            }
        });

        $radios.change(function () {
            const $selected = $radios.filter(':checked');
            if ($selected.val() === customVal) {
                $customInput
                    .prop('hidden', false)
                    .prop('disabled', false);
            } else {
                $customInput
                    .prop('hidden', true)
                    .prop('disabled', true);
            }
        });

        $radios.filter(':checked').change();
    });

    const toggleStepComponent = function () {
        const $this = $(this);
        const $stepComponent = $this.closest('.step-body').find('.step-component');

        $this.is(':checked') ? $stepComponent.show() : $stepComponent.hide();
    };
    this.$form.find('[data-toggle]').click(toggleStepComponent);
    toggleStepComponent.call(this.$form.find('[data-toggle]'));

    if (this.$form.length) {
        this.$form.change();
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

            if (c.options.customTextMap.hasOwnProperty(image['component'])) {
                $img.on('load', function () {
                    const map = c.options.customTextMap[image['component']];
                    for (let token in map) {
                        const $fieldset = c.getFieldset(image['component'], token);
                        const $radios = c.getInput(image['component'], token);
                        const $selected = $radios.filter(':checked');
                        const $selectedLabel = $selected.closest('label');
                        const $customInput = $fieldset.find('input[name="' + image['component'] + '[' + CUSTOM_TEXT_INPUT + ']"]');

                        let text = $selectedLabel.text();
                        if ($selectedLabel.data('custom')) {
                            text = $customInput.val();
                        }

                        const vScale = $img.height() / c.options.originalImageSize.height;
                        const hScale = $img.width() / c.options.originalImageSize.width;
                        const SVGText = new Text(getComponent(image['component'], response).selections, map[token]);

                        const $svg = $(SVGText.render(text))
                            .css({
                                zIndex: parseInt(image['layer']) + 1,
                                top: vScale * SVGText.getOptions().y,
                                left: hScale * SVGText.getOptions().x,
                                height: vScale * SVGText.getOptions().h,
                                width: hScale * SVGText.getOptions().w
                            });

                        const customInputChange = function () {
                            $svg.find('.text').text($(this).val());
                        };
                        $customInput.off('keyup', customInputChange);
                        if ($selectedLabel.data('custom')) {
                            $customInput.on('keyup', customInputChange);
                        }

                        $html.append($svg);
                    }
                });
            }
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
        if (item.component === component) {
            carry = item;
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
