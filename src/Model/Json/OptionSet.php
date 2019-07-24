<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\View\Helper\UrlHelper;
use Cake\View\View;

/**
 * OptionSet
 *
 * Represents an option set:
 *
 * ```
 * {
 *  "name": "Patch Text",
 *  "text": {
 *      "map": {
 *          "{{size}}": {
 *              "LG": {
 *                  "h": 75,
 *                  "w": 300,
 *                  "x": 350,
 *                  "y": 712,
 *                  "size": 32
 *              },
 *              "SM": {
 *                  "h": 75,
 *                  "w": 300,
 *                  "x": 350,
 *                  "y": 712,
 *                  "size": 21
 *              }
 *          },
 *          "{{color}}": {
 *              "GB": {
 *                  "color": "#000000"
 *              },
 *              "GW": {
 *                  "color": "#ffffff"
 *              }
 *          }
 *      },
 *      "code": "CUS",
 *      "default": "(Enter your custom text!)",
 *      "maxLength": 12
 *  },
 *  "token": "{{text}}",
 *  "options": [
 *      {
 *          "code": "POL",
 *          "name": "Police"
 *      },
 *      {
 *          "code": "SHE",
 *          "name": "Sheriff"
 *      }
 *  ],
 * "requires": {
 *      "token": "{{color}}",
 *      "component": "831d8e5f-68bd-44cd-8725-3a759422c048"
 *  }
 * }
 * ```
 */
class OptionSet
{
    /** @var string */
    const SELF = 'self';

    /** @var array */
    private $data;

    /** @var Component */
    private $component;

    /** @var UrlHelper */
    private $Url;

    /**
     * Creates an OptionSet from an array
     *
     * @param Component $component
     * @param array $jsonArray
     * @return OptionSet
     */
    public static function fromArray(Component $component, array $jsonArray) : OptionSet
    {
        return new self($component, $jsonArray);
    }

    /**
     * Constructor.
     *
     * @param Component $component
     * @param array $data
     */
    public function __construct(Component $component, array $data)
    {
        $this->component = $component;
        $this->data = $data + [
            'name' => null,
            'token' => null,
            'options' => [],
        ];
        $this->Url = new UrlHelper(new View());
    }

    /**
     * Gets the label
     *
     * @return string
     */
    public function getLabel() : string
    {
        return $this->data['name'];
    }

    /**
     * Gets human readable labels for all options. If this option set is customizable, the custom code
     * label will be set to the component's text value
     *
     * @return array
     */
    public function getOptionLabels() : array
    {
        $inherits = $this->getInherits();
        if ($inherits) {
            $inheritedComponent = $this->component
                ->getComponentCollection()
                ->getComponent(key($inherits));

            return $inheritedComponent
                ->getOptionSet(current($inherits))
                ->getOptionLabels();
        }

        $options = collection($this->data['options'])->combine('code', 'name');
        if ($this->isCustomizable()) {
            $options = $options->appendItem($this->component->getText(), $this->getCustomizableToken());
        }

        return $options->toArray();
    }

    /**
     * Gets the radio options for this option set
     *
     * @return array
     */
    public function getOptions() : array
    {
        $inherits = $this->getInherits();
        if ($inherits) {
            $inheritOptions = $this->getInheritsOptions();
            if ($inheritOptions['showOptions']) {
                $inheritedComponent = $this->component
                    ->getComponentCollection()
                    ->getComponent(key($inherits));

                return $inheritedComponent
                    ->getOptionSet(current($inherits))
                    ->getOptions();
            } else {
                return [];
            }
        }

        $options = collection($this->data['options'])
            ->map(function (array $option) {
                $radioOptions = [
                    'value' => $option['code'],
                    'text' => $option['name'],
                    'label' => []
                ];

                if (isset($option['swatch'])) {
                    $radioOptions['text'] = '';
                    $radioOptions['label'] += [
                        'class' => 'swatch',
                        'style' => "background-image:url('" . $this->Url->image($option['swatch'], ['size' => 'swatch']) . "')",
                    ];
                }

                return $radioOptions;
            })
            ->toList();

        if ($this->isCustomizable()) {
            $options[] = [
                'value' => $this->getCustomizableToken(),
                'text' => 'Custom',
                'label' => [
                    'data-custom' => true,
                ],
            ];
        }

        return $options;
    }

    /**
     * Checks if this option is customizable
     *
     * @return bool
     */
    public function isCustomizable() : bool
    {
        return isset($this->data['text']);
    }

    /**
     * Gets the token that indicates it's a customized text selection
     *
     * @return string|null
     */
    public function getCustomizableToken() : ?string
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return $this->data['text']['code'];
    }

    /**
     * Gets the custom text input options if this is customizable
     *
     * @return array|null
     */
    public function getTextOptions() : ?array
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return [
            'default' => $this->data['text']['default'] ?? null,
            'maxlength' => $this->data['text']['maxLength'] ?? 25,
        ];
    }

    /**
     * Gets the text map if this option set is customizable
     *
     * @return array|null
     */
    public function getTextMap() : ?array
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return [
            $this->getToken() => $this->data['text']['map']
        ];
    }

    /**
     * Gets the token name
     *
     * @return string
     */
    public function getToken() : string
    {
        return str_replace(['{', '}'], '', $this->data['token']);
    }

    /**
     * Gets the key=>value requires option where key is the component and value is the token
     *
     * @return array|null
     */
    public function getRequires() : ?array
    {
        if (!isset($this->data['requires'])) {
            return null;
        }

        $component = $this->data['requires']['component'] ?? self::SELF;

        return [
            $component => str_replace(['{', '}'], '', $this->data['requires']['token'])
        ];
    }

    /**
     * Gets the key=>value inherits option where key is the component and value is the token
     *
     * @return array|null
     */
    public function getInherits() : ?array
    {
        if (!isset($this->data['inherits'])) {
            return null;
        }

        return [
            $this->data['inherits']['component'] => str_replace(['{', '}'], '', $this->data['inherits']['token'])
        ];
    }

    /**
     * Gets inherits options
     *
     * @return array
     */
    public function getInheritsOptions() : array
    {
        if (!isset($this->data['inherits'])) {
            return [];
        }

        $options = $this->data['inherits'];
        unset($options['component']);
        unset($options['token']);

        return $options + [
            'showOptions' => false
        ];
    }
}
