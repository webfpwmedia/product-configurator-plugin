<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Step as StepEntity;
use Cake\Collection\Collection;
use RuntimeException;

class StepCollection extends Collection
{

    /** @var array */
    private $aliases;

    /**
     * Constructor.
     *
     * @param StepEntity[] $stepEntities
     */
    public function __construct($stepEntities)
    {
        $items = [];
        foreach ($stepEntities as $stepEntity) {
            $step = new Step($stepEntity);
            $items[] = $step;
            foreach ($step->getComponents() as $component) {
                if ($component->getComponentEntity()->alias) {
                    $this->aliases[$component->getComponentEntity()->alias] = $component->getId();
                }
            }
        }

        parent::__construct($items);
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
