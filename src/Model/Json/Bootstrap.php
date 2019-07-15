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
class Bootstrap implements JsonSerializable
{
    /** @var array */
    private $components = [];

    /**
     * Creates a bootstrap from an array
     *
     * @param array $jsonArray
     * @return Bootstrap
     */
    public static function fromArray($jsonArray) : Bootstrap
    {
        $bootstrap = new self();
        foreach ($jsonArray as $data) {
            $bootstrap->addComponent(Component::fromArray($data));
        }

        return $bootstrap;
    }

    /**
     * Adds a component to the bootstrap
     *
     * @param Component $component
     * @return void
     */
    public function addComponent(Component $component) : void
    {
        $this->components[] = $component;
    }

    /**
     * Gets the component if it exists
     *
     * @param string $id
     * @return Component|null
     */
    public function getComponents(string $id) : ?Component
    {
        return collection($this->components)
            ->filter(function (Component $component) use ($id) {
                return $component->getId() === $id;
            })
            ->first();
    }

    /**
     * @return array|\Cake\Collection\CollectionTrait|mixed
     */
    public function jsonSerialize() : array
    {
        return collection($this->components)
            ->indexBy('getId')
            ->toArray();
    }
}
