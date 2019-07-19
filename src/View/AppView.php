<?php
namespace ARC\ProductConfigurator\View;

use ARC\ProductConfigurator\View\Helper\UrlHelper;
use ARC\ProductConfigurator\View\Widget\JsonWidget;
use ARC\ProductConfigurator\View\Widget\RadioWidget;
use Cake\Core\Configure;
use App\View\AppView as View;

/**
 * Plugin default view class.
 *
 * @property UrlHelper $Url
 */
class AppView extends View
{

    /**
     * initialize.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $templates = 'adminTemplates';
        $controller = $this->getRequest()->getParam('controller');
        $action = $this->getRequest()->getParam('action');

        if ($controller === 'Configurators' && $action === 'build') {
            $templates = 'buildTemplates';
        }

        try {
            $this->helpers()->unload('Url');
        } catch (\Exception $exception) {
            # May not actually be loaded.
        } finally {
            $this->loadHelper('Url', [
                'className' => UrlHelper::class,
            ]);
        }

        try {
            $this->loadHelper('Form');
        } catch (\Exception $exception) {
            # Configured below.
        }

        $this->Form->setConfig([
            'templates' => Configure::read("ARC.ProductConfigurator.$templates"),
            'widgets' => [
                'json' => [JsonWidget::class],
                'radio' => [RadioWidget::class, 'nestingLabel'],
            ],
        ]);

        Configure::write('App.imageBaseUrl', Configure::read('ARC.ProductConfigurator.imageBaseUrl'));
    }

    /**
     * Loads an element into the template, if defined.
     *
     * @param string $hook Element hook name (see config.php).
     *
     * @return string
     */
    public function elementHook(string $hook)
    {
        if (Configure::read('ARC.ProductConfigurator.elementHook.' . $hook)) {
            return $this->element(Configure::read('ARC.ProductConfigurator.elementHook.' . $hook));
        }

        return null;
    }
}
