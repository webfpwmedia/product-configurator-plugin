<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Step as StepEntity;
use Cake\Collection\Collection;

class StepCollection extends Collection
{

    /**
     * The collection of all components for all steps
     *
     * @var ComponentCollection
     */
    private $componentCollection;

    /**
     * Constructor.
     *
     * @param StepEntity[] $stepEntities
     */
    public function __construct($stepEntities)
    {
        $items = [];
        $this->componentCollection = new ComponentCollection();
        foreach ($stepEntities as $stepEntity) {
            $step = new Step($this, $stepEntity);
            $items[] = $step;
            foreach ($step->getComponents() as $component) {
                $this->componentCollection->addComponent($component);
            }
        }

        parent::__construct($items);
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
     * Gets a component entity id from its alias if it's part of this StepCollection
     *
     * @param string $alias
     * @return string
     */
    public function getIdFromAlias($alias) : string
    {
        return $this->aliases[$alias];
    }

    /**
     * Gets the OptionSet for a component/token combination
     *
     * @param $componentId
     * @param $token
     * @return OptionSet
     * @throws RuntimeException If component/token combination is not configured
     */
    public function getComponentTokenOptionSet($componentId, $token) : OptionSet
    {
        $components = $this->getComponentConfigurations($componentId);

        if (empty($components)) {
            throw new RuntimeException(sprintf('Component#%s is not configured in any steps.', $componentId));
        }

        foreach ($components as $component) {
            if ($component->getOptionSet($token)) {
                return $component->getOptionSet($token);
            }
        }

        throw new RuntimeException(sprintf('Token "%s" for Component#%s is not configured in any steps.', $token, $componentId));
    }

    /**
     * Gets component configurations for a component
     *
     * Since components can be configured in more than one step this will return an array of those
     * configurations
     *
     * @param string $componentId
     * @return Component[]
     */
    public function getComponentConfigurations($componentId) : array
    {
        $components = $this
            ->extract(function (Step $step) use ($componentId) {
                return $step->getComponents();
            })
            ->unfold();

        return collection($components->toList())
            ->filter(function (Component $component) use ($componentId) {
                return $component->getId() == $componentId;
            })
            ->toList();
    }
}
