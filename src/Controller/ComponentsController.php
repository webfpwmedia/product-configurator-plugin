<?php
namespace ARC\ProductConfigurator\Controller;

/**
 * Components Controller
 *
 * @property \ARC\ProductConfigurator\Model\Table\ComponentsTable $Components
 *
 * @method \ARC\ProductConfigurator\Model\Entity\Component[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ComponentsController extends AppController
{

    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        $components = $this->Components
            ->find()
            ->order(['name' => 'asc', 'mask' => 'asc']);

        $this->set('components', $components);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $component = $this->Components->newEntity();

        if ($this->getRequest()->is('post')) {
            $component = $this->Components->patchEntity($component, $this->getRequest()->getData());

            if ($this->Components->save($component)) {
                $this->Flash->success(__('The component has been saved.'));

                return $this->redirect(['action' => 'edit', $component->id]);
            }

            $this->Flash->error(__('The component could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('component'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Edit method
     *
     * @param string|null $id Component id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $component = $this->Components->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $component = $this->Components->patchEntity($component, $this->request->getData());

            if ($this->Components->save($component)) {
                $this->Flash->success(__('The component has been saved.'));

                # Invalidate user's session to prevent false-positive build pages.
                $this->request->getSession()->delete('build');

                return $this->redirect(['action' => 'edit', $component->id]);
            }

            $this->Flash->error(__('The component could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('component'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Delete method
     *
     * @param string|null $id Component id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $component = $this->Components->get($id);

        if ($this->Components->delete($component)) {
            $this->Flash->success(__('The component has been deleted.'));

            # Invalidate user's session to prevent false-positive build pages.
            $this->request->getSession()->delete('build');
        } else {
            $this->Flash->error(__('The component could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Components', 'action' => 'index']);
    }
}
