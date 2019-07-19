<?php
namespace ARC\ProductConfigurator\Controller;

use Cake\Datasource\ModelAwareTrait;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Response;
use Cake\Log\Log;

/**
 * Configurators Controller
 *
 * @property \ARC\ProductConfigurator\Model\Table\ConfiguratorsTable $Configurators
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ConfiguratorsController extends AppController
{
    use ModelAwareTrait;

    /** @var \ARC\ProductConfigurator\Model\Table\BuildsTable */
    public $Builds;

    /**
     * Initialize hook
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('ARC/ProductConfigurator.Builds');
    }

    /**
     * Public configurator builder
     *
     * @param int|null $id
     * @param int|null $buildId
     * @return \Cake\Http\Response|null
     */
    public function build($id = null, $buildId = null)
    {
        $configurator = $this->Configurators->get($id, [
            'contain' => [
                'Steps' => [
                    'sort' => ['Steps.sort' => 'asc'],
                ],
            ]
        ]);
        $context = $configurator->bootstrap;
        if ($this->request->getSession()->read('build')) {
            $context = json_decode($this->request->getSession()->read('build'), true);
        }

        $build = $this->Builds->newEntity();

        if ($buildId) {
            $build = $this->Builds->get($buildId);
            $context = $build->components;
        }

        if ($this->request->is(['post'])) {
            $build = $this->Builds->patchEntity($build, $this->request->getData());
            $this->request->getSession()->write('build', json_encode($build->components));
            if ($this->request->getData('extra.save') && $this->Builds->save($build)) {
                $this->Flash->success(__('Your build has been submitted!'));
                $result = EventManager::instance()->dispatch(new Event('ARC.ProductConfigurator.build', null, [
                    'id' => $build->id,
                    'configurator_id' => $id,
                    'data' => $this->request->getData()
                ]));

                if ($result->result instanceof Response) {
                    return $result->result;
                }

                return $this->redirect(['action' => 'build', $id]);
            } elseif ($this->request->getData('extra.save')) {
                Log::write(LOG_ALERT, json_encode($this->request->getData()));
                Log::write(LOG_ALERT, json_encode($build));
                $this->Flash->error(__('There was a problem submitting your build.'));
            }

            $this->set('build', $build);
            $this->set('_serialize', ['build']);
        }

        $this->set('context', $context);
        $this->set('configurator', $configurator);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $configurators = $this->paginate($this->Configurators);

        $this->set(compact('configurators'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $configurator = $this->Configurators->newEntity();

        if ($this->request->is('post')) {
            $configurator = $this->Configurators->patchEntity($configurator, $this->request->getData());

            if ($this->Configurators->save($configurator)) {
                $this->Flash->success(__('The configurator has been saved.'));

                return $this->redirect(['action' => 'edit', $configurator->id]);
            }

            $this->Flash->error(__('The configurator could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('configurator'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Edit method
     *
     * @param string|null $id Configurator id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $configurator = $this->Configurators->get($id, [
            'contain' => [
                'Steps' => [
                    'sort' => ['Steps.sort' => 'asc'],
                ],
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $configurator = $this->Configurators->patchEntity($configurator, $this->request->getData());

            if ($this->Configurators->save($configurator)) {
                $this->Flash->success(__('The configurator has been saved.'));

                return $this->redirect(['action' => 'edit', $configurator->id]);
            }

            $this->Flash->error(__('The configurator could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('configurator'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Delete method
     *
     * @param string|null $id Configurator id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $configurator = $this->Configurators->get($id);

        if ($this->Configurators->delete($configurator)) {
            $this->Flash->success(__('The configurator has been deleted.'));
        } else {
            $this->Flash->error(__('The configurator could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
