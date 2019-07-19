<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Mask\Mask;
use ARC\ProductConfigurator\Model\Entity\Component as ComponentEntity;
use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use JsonSerializable;

/**
 * Component
 *
 * Represents a component:
 *
 * ```
 * {
 *  "99fe8884-de7d-4a33-8a37-6fc6ee4263cf": {
 *      "selections": {
 *          "weight": "ST"
 *      }
 *  }
 * }
 * ```
 */
class Component implements JsonSerializable
{
    use InstanceConfigTrait;
    use LocatorAwareTrait;

    /**
     * Default config
     *
     * ### Configuration:
     * - bool $showQty: Whether to show a qty field or not
     *
     * @var array
     */
    protected $_defaultConfig = [
        'showQty' => false,
        'showToggle' => false,
    ];

    /** @var array */
    private $data = [
        'qty' => 1,
        'selections' => []
    ];

    /** @var string */
    private $id;

    /** @var ComponentEntity */
    private $component;

    /**
     * Visible state
     *
     * @var bool
     */
    public $toggle = true;

    /**
     * Creates a component from an array
     *
     * @param array $jsonArray
     * @return Component
     */
    public static function fromArray(array $jsonArray) : Component
    {
        $id = key($jsonArray);
        $component = new self($id);
        $component->addSelections($jsonArray[$id]['selections'] ?? []);
        if (isset($jsonArray[$id]['text'])) {
            $component->addText($jsonArray[$id]['text']);
        }
        if (isset($jsonArray[$id]['qty'])) {
            $component->setQty((int)$jsonArray[$id]['qty']);
        }
        if (isset($jsonArray[$id]['toggle'])) {
            $component->toggle = $jsonArray[$id]['toggle'];
        }

        unset($jsonArray[$id]['selections']);
        unset($jsonArray[$id]['text']);
        unset($jsonArray[$id]['qty']);
        unset($jsonArray[$id]['toggle']);

        $component->setConfig($jsonArray[$id]);

        return $component;
    }

    /**
     * Constructor.
     *
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the component id
     *
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * Adds selections
     *
     * @param array $selections
     * @retur void
     */
    public function addSelections(array $selections) : void
    {
        $this->data['selections'] = array_merge($this->data['selections'], $selections);
    }

    /**
     * Gets selections
     *
     * @return array
     */
    public function getSelections() : array
    {
        return $this->data['selections'];
    }

    /**
     * Gets options
     *
     * @return OptionSet[]
     */
    public function getOptions() : array
    {
        $options = [];

        foreach ($this->getComponentEntity()->options as $data) {
            $options[] = OptionSet::fromArray($data);
        }

        return $options;
    }

    /**
     * Gets the option set for a token
     *
     * @param string $token
     * @return OptionSet|null
     */
    public function getOptionSet($token) : ?OptionSet
    {
        foreach ($this->getOptions() as $optionSet) {
            if ($optionSet->getToken() === $token) {
                return $optionSet;
            }
        }

        return null;
    }

    /**
     * Gets the selection for a particular token
     *
     * @param string $token
     * @return string|null
     */
    public function getSelection(string $token) : ?string
    {
        return $this->data['selections'][$token] ?? null;
    }

    /**
     * Adds a text label
     *
     * @param string $text
     * @return void
     */
    public function addText(string $text) : void
    {
        $this->data['text'] = $text;
    }

    /**
     * Gets the text label
     *
     * @return string|null
     */
    public function getText() : ?string
    {
        return $this->data['text'] ?? null;
    }

    /**
     * Sets the quantity
     *
     * @param int $qty
     * @param void
     */
    public function setQty(int $qty) : void
    {
        $this->data['qty'] = $qty;
    }

    /**
     * Gets the quantity
     *
     * @return int
     */
    public function getQty() : int
    {
        return $this->data['qty'];
    }

    /**
     * Gets the filled image template for the component
     *
     * @return string
     */
    public function getImageTemplate() : string
    {
        return (new Mask($this->getComponentEntity()->image_mask))->format($this->data['selections']);
    }

    /**
     * Gets the filled option template for the component
     *
     * @return string
     */
    public function getOptionTemplate() : string
    {
        return (new Mask($this->getComponentEntity()->mask))->format($this->data['selections']);
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize() : array
    {
        return [
            $this->id => $this->data
        ];
    }

    /**
     * Lazy loads the DB component entity
     *
     * @return ComponentEntity
     */
    public function getComponentEntity() : ComponentEntity
    {
        if (!$this->component instanceof ComponentEntity) {
            $this->component = $this->getTableLocator()->get('ARC/ProductConfigurator.Components')->get($this->id);
        }

        return $this->component;
    }
}
