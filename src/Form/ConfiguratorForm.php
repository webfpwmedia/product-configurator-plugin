<?php
namespace ARC\ProductConfigurator\Form;

use Cake\Core\Configure;
use Cake\Datasource\ModelAwareTrait;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Utility\Hash;
use Cake\View\StringTemplate;

class ConfiguratorForm extends Form
{
    use ModelAwareTrait;

    /**
     * @var ARC\ProductConfigurator\Model\Table\ComponentsTable
     */
    public $Components;

    /**
     * @var ARC\ProductConfigurator\Model\Table\ImagesTable
     */
    public $Images;

    /**
     * Constructor.
     *
     * @param EventManager|null $eventManager
     */
    public function __construct(EventManager $eventManager = null)
    {
        parent::__construct($eventManager);

        $this->loadModel('Components');
        $this->loadModel('Images');
    }

    /**
     * Returns an array of images for the component selections submitted
     *
     * @param array $data
     * @return array|bool
     */
    protected function _execute(array $data)
    {
        $return = [];
        foreach ($data as $componentId => $selections) {
            $selections = $this->__withInheritance($data, $selections);

            $component = $this->Components->get($componentId);

            $stringTemplate = new StringTemplate(['mask' => $component->image_mask]);

            $imgBaseUrl = Configure::read('App.imageBaseUrl');

            $images = $this->Images
                ->find()
                ->select([
                    'position',
                    'name',
                    'layer',
                ])
                ->where([
                    'mask' => $stringTemplate->format('mask', $selections)
                ])
                ->enableHydration(false)
                ->groupBy('position')
                ->map(function ($imagesByPosition) use ($imgBaseUrl) {
                    return [
                        'path' => $imgBaseUrl . $imagesByPosition[0]['name'],
                        'layer' => $imagesByPosition[0]['layer'],
                    ];
                })
                ->toArray();

            if ($images) {
                $return[] = $images;
            }
        }

        return $return;
    }

    /**
     * Replaces inherited attributes with their submitted values
     *
     * @param array $data Full submitted data for all components
     * @param array $selections User selections for this component
     * @return array
     */
    private function __withInheritance($data, $selections)
    {
        foreach ($selections as $token => $selection) {
            if (stripos($selection, 'inherits:') === false) {
                continue;
            }

            list(, $inheritableComponentId, $inheritableToken) = explode(':', $selection);

            $selections[$token] = Hash::get($data, "$inheritableComponentId.$inheritableToken");
        }

        return $selections;
    }
}
