<?php
namespace ARC\ProductConfigurator\Model\Json;

use JsonSerializable;

/**
 * Bootstrap
 *
 * Represents a bootstrap object:
 *
 * ```
 * [
 *  {
 *      "7a7cf428-28dc-4bac-b186-cbd9345642ec": [ <component> ]
 *  },
 *  {
 *      "99fe8884-de7d-4a33-8a37-6fc6ee4263cf": [ <component> ]
 *  },
 *  {
 *      "04112148-11d0-444c-b3e1-b924efad3570": [ <component> ]
 *  }
 * ]
 * ```
 */
class Bootstrap
{

    /** @var ComponentCollection */
    private $componentCollection;

    /**
     * Creates a bootstrap from an array
     *
     * @param array $jsonArray
     * @return Bootstrap
     */
    public static function fromArray($jsonArray) : Bootstrap
    {
        $componentCollection = new ComponentCollection();
        foreach ($jsonArray as $data) {
            $componentCollection->addComponent(Component::fromArray($componentCollection, $data));
        }

        return new self($componentCollection);
    }

    /**
     * Constructor.
     *
     * @param ComponentCollection $componentCollection
     */
    public function __construct(ComponentCollection $componentCollection)
    {
        $this->componentCollection = $componentCollection;
    }

    /**
     * Gets the ComponentCollection
     *
     * @return ComponentCollection
     */
    public function getComponentCollection() : ComponentCollection
    {
        return $this->componentCollection;
    }
}
