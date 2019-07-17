<?php
namespace ARC\ProductConfigurator\Model\Json;

use ARC\ProductConfigurator\Model\Entity\Step as StepEntity;
use Cake\Collection\Collection;

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
}
