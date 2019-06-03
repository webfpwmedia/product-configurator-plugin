<?php
namespace ARC\ProductConfigurator\Controller;

use ARC\ProductConfigurator\Form\ConfiguratorForm;

/**
 * Configurators Controller
 *
 * @property \ARC\ProductConfigurator\Model\Table\ConfiguratorsTable $Configurators
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Configurator[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ConfiguratorsController extends AppController
{

    /**
     * Public configurator builder
     *
     * @param int|null $id
     * @return \Cake\Http\Response|void
     */
    public function build($id = null)
    {
        $configurator = $this->Configurators->get($id, [
            'contain' => [
                'Steps' => [
                    'sort' => ['Steps.sort' => 'asc'],
                ],
            ]
        ]);

        if ($this->request->is(['post'])) {
            $form = new ConfiguratorForm();
            $data = $form->execute($this->request->getData());

            $this->set('data', $data);
            $this->set('_serialize', ['data']);
        }

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
     * View method
     *
     * @param string|null $id Configurator id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $configurator = $this->Configurators->get($id, [
            'contain' => [
                'Components' => [
                    'sort' => ['Components.name' => 'asc'],
                ],
                'Steps' => [
                    'sort' => ['Steps.sort' => 'asc'],
                ],
            ]
        ]);

        $this
            ->set('configurator', $configurator)
            ->viewBuilder()
            ->setTemplate('manage');
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

                return $this->redirect(['action' => 'view', $configurator->id]);
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
            'contain' => []
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $configurator = $this->Configurators->patchEntity($configurator, $this->request->getData());

            if ($this->Configurators->save($configurator)) {
                $this->Flash->success(__('The configurator has been saved.'));

                return $this->redirect(['action' => 'view', $configurator->id]);
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
