<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Mask\Mask;
use ARC\ProductConfigurator\View\Helper\UrlHelper;
use Cake\Core\InstanceConfigTrait;
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
    use InstanceConfigTrait;

    /** @var string */
    const SELF = 'self';

    protected $_defaultConfig = [];

    /** @var array */
    private $knownKeys = [
        'name',
        'token',
        'options',
        'inherits',
        'requires',
        'custom',
    ];

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
        foreach ($data as $key => $config) {
            if (!in_array($key, $this->knownKeys)) {
                $this->setConfig($key, $config);
            }
        }

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
            $options = $options->appendItem($this->component->getCustomText(), $this->getCustomValue());
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

                unset($option['swatch']);
                unset($option['code']);
                unset($option['name']);
                $dataAttributes = collection($option)
                    ->map(function ($value, &$key) {
                        $key = "data-$key";

                        return h($value);
                    })
                    ->toArray();

                return $radioOptions + $dataAttributes;
            })
            ->toList();

        if ($this->isCustomizable()) {
            $options[] = [
                'value' => $this->getCustomValue(),
                'text' => $this->getCustomLabel(),
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
        return isset($this->data['custom']);
    }

    /**
     * Gets the radio value that indicates it's a customized text selection
     *
     * @return string|null
     */
    public function getCustomValue() : ?string
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return $this->data['custom']['code'];
    }

    /**
     * Gets the radio label for a custom radio
     *
     * @return string|null
     */
    public function getCustomLabel() : ?string
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return $this->data['custom']['name'] ?? 'Custom';
    }

    /**
     * Gets the custom text input options if this is customizable
     *
     * @return array|null
     */
    public function getCustomOptions() : ?array
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return [
            'default' => $this->data['custom']['default'] ?? null,
            'maxlength' => $this->data['custom']['maxLength'] ?? 25,
        ];
    }

    /**
     * Gets the map if this option set is customizable
     *
     * @return array|null
     */
    public function getCustomMap() : ?array
    {
        if (!$this->isCustomizable()) {
            return null;
        }

        return [
            $this->getToken() => $this->data['custom']['map']
        ];
    }

    /**
     * Gets the token name
     *
     * @return string
     */
    public function getToken() : string
    {
        $tokens = (new Mask($this->data['token']))->getTokens();

        return $tokens[0];
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
