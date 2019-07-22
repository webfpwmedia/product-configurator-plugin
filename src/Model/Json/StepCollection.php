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
}
