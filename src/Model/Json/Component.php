<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Component as ComponentEntity;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\StringTemplate;
use JsonSerializable;
use RuntimeException;

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
    use LocatorAwareTrait;

    /** @var array */
    private $data = [
        'selections' => []
    ];

    /** @var string */
    private $id;

    /** @var ComponentEntity */
    private $component;

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
     * Gets the filled image template for the component
     *
     * @return string
     */
    public function getImageTemplate() : string
    {
        $this->checkMaskTokens($this->getComponentEntity()->image_mask);

        $stringTemplate = new StringTemplate(['mask' => $this->getComponentEntity()->image_mask]);

        return $stringTemplate->format('mask', $this->data['selections']);
    }

    /**
     * Gets the filled option template for the component
     *
     * @return string
     */
    public function getOptionTemplate() : string
    {
        $this->checkMaskTokens($this->getComponentEntity()->mask);

        $stringTemplate = new StringTemplate(['mask' => $this->getComponentEntity()->mask]);

        return $stringTemplate->format('mask', $this->data['selections']);
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
    private function getComponentEntity() : ComponentEntity
    {
        if (!$this->component instanceof ComponentEntity) {
            $this->component = $this->getTableLocator()->get('ARC/ProductConfigurator.Components')->get($this->id);
        }

        return $this->component;
    }

    /**
     * Checks a mask to see if all tokens are included in selections and that those tokens are not empty
     *
     * @param string $mask Mask to check
     * @throws RuntimeException When mask doesn't have any tokens
     * @throws TokensMissingException When mask tokens are missing from selections
     */
    private function checkMaskTokens($mask)
    {
        preg_match_all('/({{([A-Z]+)}})/i', $mask, $matches);

        if (empty($matches)) {
            throw new RuntimeException(sprintf('"%s" does not have any tokens.', $mask));
        }

        $requiredTokens = $matches[2];
        $selectedTokens = array_keys($this->getSelections());

        if (array_diff($requiredTokens, $selectedTokens)) {
            throw new TokensMissingException();
        }

        foreach ($requiredTokens as $token) {
            if (!$this->getSelection($token)) {
                throw new TokensMissingException();
            }
        }
    }
}
