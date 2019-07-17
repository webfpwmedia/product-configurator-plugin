<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Step as StepEntity;

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

    /** @var StepEntity */
    private $step;

    /**
     * Constructor.
     *
     * @param StepEntity $step
     */
    public function __construct(StepEntity $step)
    {
        $this->step = $step;

        foreach ($this->step->config as $data) {
            $this->addComponent(Component::fromArray($data));
        }
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

    /**
     * Gets step entity id
     *
     * @return string
     */
    public function getId()
    {
        return $this->step->id;
    }

    /**
     * Gets step entity name
     *
     * @return string
     */
    public function getName()
    {
        return $this->step->name;
    }
}
