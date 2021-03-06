<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Step as StepEntity;

/**
 * Step
 *
 * Represents a step that contains multiple components. See Component::$_defaultConfig for configuration options.
 *
 * ```
 * [
 *  {
 *      "7a7cf428-28dc-4bac-b186-cbd9345642ec": [ <component config> ]
 *  },
 *  {
 *      "99fe8884-de7d-4a33-8a37-6fc6ee4263cf": [ <component config> ]
 *  },
 *  {
 *      "04112148-11d0-444c-b3e1-b924efad3570": [ <component config> ]
 *  }
 * ]
 * ```
 */
class Step
{

    /** @var Component[] */
    private $components = [];

    /** @var ComponentCollection */
    private $stepCollection;

    /** @var StepEntity */
    private $step;

    /**
     * Constructor.
     *
     * @param StepCollection $stepCollection
     * @param StepEntity $step
     */
    public function __construct(StepCollection $stepCollection, StepEntity $step)
    {
        $this->stepCollection = $stepCollection;
        $this->step = $step;

        $this->components = [];
        foreach ($this->step->config as $data) {
            $this->components[] = Component::fromArray($this->stepCollection->getComponentCollection(), $data);
        }
    }

    /**
     * Gets the StepCollection
     *
     * @return StepCollection
     */
    public function getStepCollection() : StepCollection
    {
        return $this->stepCollection;
    }

    /**
     * Gets components for the step
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
