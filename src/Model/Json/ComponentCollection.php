<?php
namespace ARC\ProductConfigurator\Model\Json;

class ComponentCollection
{

    /** @var array */
    private $aliases;

    /** @var array */
    private $components = [];

    /**
     * Adds a component to the collection
     *
     * @param Component $component
     */
    public function addComponent(Component $component)
    {
        if ($component->getComponentEntity()->alias) {
            $this->aliases[$component->getComponentEntity()->alias] = $component->getId();
        }

        $this->components[$component->getId()] = $component;
    }

    /**
     * Gets a component id from its alias
     *
     * @param string $alias
     * @return string|null
     */
    public function getIdFromAlias($alias) : ?string
    {
        return $this->aliases[$alias] ?? null;
    }

    /**
     * Gets a component from the collection by alias or id
     *
     * @param string $id Alias or id
     * @return Component|null
     */
    public function getComponent($id) : ?Component
    {
        return $this->components[$this->getIdFromAlias($id) ?? $id] ?? null;
    }

    /**
     * Removes a component from the collection
     *
     * @param Component $component
     */
    public function removeComponent(Component $component) : void
    {
        unset($this->components[$component->getId()]);
    }

    /**
     * Gets all components in the collection
     *
     * @return Component[]
     */
    public function getComponents() : array
    {
        return array_values($this->components);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'aliases' => $this->aliases,
            'components' => collection($this->components)
                ->map(function () {
                    return '[Component]';
                })
                ->toArray()
        ];
    }
}
