<?php
namespace ARC\ProductConfigurator\Form;

use ARC\ProductConfigurator\Model\Table\ComponentsTable;
use ARC\ProductConfigurator\Model\Table\ImagesTable;
use Cake\Core\Configure;
use Cake\Datasource\ModelAwareTrait;
use Cake\Event\EventManager;
use Cake\Form\Form;
use Cake\Utility\Hash;
use Cake\View\StringTemplate;
use function http_build_query;

class ConfiguratorForm extends Form
{
    use ModelAwareTrait;

    /**
     * @var ComponentsTable
     */
    public $Components;

    /**
     * @var ImagesTable
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

        $this->loadModel('ARC/ProductConfigurator.Components');
        $this->loadModel('ARC/ProductConfigurator.Images');
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

            $imgBaseUrl = Configure::read('ARC.ProductConfigurator.imageBaseUrl');

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
                    $path = [
                        $imgBaseUrl,
                        $imagesByPosition[0]['name'],
                        '?',
                        http_build_query(Configure::read('ARC.ProductConfigurator.imgix.md')),
                    ];

                    return [
                        'path' => join(null, $path),
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
