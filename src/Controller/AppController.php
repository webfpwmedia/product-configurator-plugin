<?php
namespace ARC\ProductConfigurator\Controller;

use ARC\ProductConfigurator\View\AppView;
use App\Controller\AppController as BaseController;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @property \Cake\Controller\Component\SecurityComponent $Security
 * @property \Cake\Controller\Component\PaginatorComponent $Paginator
 */
class AppController extends BaseController
{

    /**
     * initialize.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        try {
            $this->loadComponent('RequestHandler', [
                'enableBeforeRedirect' => false,
            ]);

            $this->loadComponent('Flash');
            $this->loadComponent('Security');
            $this->loadComponent('Paginator');
        } catch (\Exception $exception) {
            $this->log($exception->getMessage());
        }
    }

    /**
     * beforeRender.
     *
     * @param Event $event
     *
     * @return void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        if (!$this->viewBuilder()->getClassName()) {
            $this
                ->viewBuilder()
                ->setClassName(AppView::class);
        }
    }
}
