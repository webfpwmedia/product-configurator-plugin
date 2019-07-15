<?php
namespace ARC\ProductConfigurator\Model\Json;

/**
 * Step
 *
 * Represents a step that contains multiple components:
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
class Step
{

    /** @var Component[] */
    private $components = [];

    /**
     * Creates a step from an array
     *
     * @param array $jsonArray
     * @return Step
     */
    public static function fromArray(array $jsonArray) : Step
    {
        $step = new self();
        foreach ($jsonArray as $data) {
            $step->addComponent(Component::fromArray($data));
        }

        return $step;
    }

    /**
     * Adds a component to the step
     *
     * @param Component $component
     * @return void
     */
    public function addComponent(Component $component) : void
    {
        $this->components[] = $component;
    }

    /**
     * Gets components included in this step
     *
     * @return Component[]
     */
    public function getComponents() : array
    {
        return $this->components;
    }
}
