<?php
namespace ARC\ProductConfigurator\View;

use ARC\ProductConfigurator\View\Helper\HtmlHelper;
use ARC\ProductConfigurator\View\Widget\JsonWidget;
use Cake\Core\Configure;
use Cake\View\View;

/**
 * Plugin default view class.
 *
 * @property HtmlHelper $Html
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

        $this->loadHelper('Html', [
            'className' => 'ARC/ProductConfigurator.Html',
        ]);

        $this->loadHelper('Form', [
            'templates' => Configure::read("ARC.ProductConfigurator.$templates"),
            'widgets' => [
                'json' => [JsonWidget::class],
            ],
        ]);

        Configure::write('App.imageBaseUrl', Configure::read('ARC.ProductConfigurator.imageBaseUrl'));
    }
}
