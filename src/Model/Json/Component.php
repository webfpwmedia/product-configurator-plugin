<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Mask\Mask;
use ARC\ProductConfigurator\Model\Entity\Component as ComponentEntity;
use Cake\Core\InstanceConfigTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use JsonSerializable;
use LogicException;

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
     * For file uploads, token values will be selected as this string.
     *
     * @const string
     */
    const SELECTION_UPLOAD = 'UPLOAD';

    /**
     * Default config
     *
     * ### Configuration:
     *
     * - string $header: Component header (all option sets are "bundled" under this title).
     * - bool $showQty: Whether to show a qty field or not
     * - bool $showToggle: Whether to show a toggler for selecting/deselecting the component
     *
     * @var array
     */
    protected $_defaultConfig = [
        'header' => null,
        'showQty' => false,
        'showToggle' => false,
    ];

    /**
     * Supported keys for configured builds.
     *
     * @var array
     */
    private $data = [
        'qty' => 1,
        'selections' => [],
        'images' => [],
    ];

    /**
     * Index of images configured via user upload.
     *
     * Stores base64 encoded versions of each image, indexed by `token.position`.
     *
     * @var array
     */
    private $images = [];

    /** @var string */
    private $id;

    /** @var ComponentEntity */
    private $component;

    /** @var ComponentCollection */
    private $componentCollection;

    /**
     * Visible state
     *
     * @var bool
     */
    public $toggle = true;

    /**
     * Creates a component from an array
     *
     * @param ComponentCollection $componentCollection
     * @param array $jsonArray
     * @return Component
     */
    public static function fromArray(ComponentCollection $componentCollection, array $jsonArray) : Component
    {
        $id = key($jsonArray);
        $component = new self($componentCollection, $id);
        $component->addSelections($jsonArray[$id]['selections'] ?? []);
        if (isset($jsonArray[$id]['text'])) {
            $component->addCustomText($jsonArray[$id]['text']);
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
     * @param ComponentCollection $componentCollection
     * @param string $id
     */
    public function __construct(ComponentCollection $componentCollection, string $id)
    {
        $this->componentCollection = $componentCollection;
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
     * Adds selections to the `Component`.
     *
     * @param array $selections
     *
     * @return Component
     */
    public function addSelections(array $selections): Component
    {
        $this->data['selections'] = array_merge($this->data['selections'], $selections);

        return $this;
    }

    /**
     * Set binary image data to `Component`, indexed by `token.position`.
     *
     * @param string $path
     * @param array $image
     *      `name` The image name with extension.
     *      `data` Base64 encoded string of image.
     *
     * @return Component
     */
    public function addImage(string $path, array $image): Component
    {
        $this->images = array_merge($this->images, [$path => $image]);

        return $this;
    }

    /**
     * Set image name into saveable `uploads` key based on `token.position`.
     *
     * @param string $path
     *
     * @return Component
     *
     * @throws LogicException If trying to get-set an image that hasn't been added.
     */
    public function setImageName(string $path): Component
    {
        if (!isset($this->images[$path]['name'])) {
            throw new LogicException(__('Cannot persist an image name that does not exist.'));
        }

        $this->data['images'] = [$path => $this->images[$path]['name']];

        return $this;
    }

    /**
     * Get uploaded images.
     *
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
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
            $options[] = OptionSet::fromArray($this, $data);
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
     * Adds custom text
     *
     * @param string $text
     * @return void
     */
    public function addCustomText(string $text) : void
    {
        $this->data['text'] = $text;
    }

    /**
     * Gets custom text
     *
     * @return string|null
     */
    public function getCustomText() : ?string
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
     * Gets the description for the component from the selected options
     *
     * @return string
     */
    public function getDescription() : string
    {
        $descriptions = $this->data['selections'];
        foreach ($descriptions as $token => &$selection) {
            $optionSet = $this->getOptionSet($token);
            $selection = $optionSet->getOptionLabels()[$selection];
            if ($optionSet->isCustomizable() && $selection === $this->getCustomText()) {
                $selection = '"' . $selection . '"';
            }
        }

        preg_match_all(Mask::TOKEN_MATCHER, $this->getComponentEntity()->mask, $matches);

        $orderedValues = collection($matches[2])
            ->map(function ($token) use ($descriptions) {
                return $descriptions[$token];
            })
            ->prependItem($this->getComponentEntity()->name)
            ->toList();

        return implode(' ', $orderedValues);
    }

    /**
     * Gets the component collection
     *
     * @return ComponentCollection
     */
    public function getComponentCollection() : ComponentCollection
    {
        return $this->componentCollection;
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
