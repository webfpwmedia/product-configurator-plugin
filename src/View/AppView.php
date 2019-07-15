<?php
namespace ARC\ProductConfigurator\View;

use ARC\ProductConfigurator\View\Helper\UrlHelper;
use ARC\ProductConfigurator\View\Widget\JsonWidget;
use ARC\ProductConfigurator\View\Widget\RadioWidget;
use Cake\Core\Configure;
use Cake\View\View;

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

        $this->loadHelper('Url', [
            'className' => UrlHelper::class,
        ]);

        $this->loadHelper('Form', [
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
