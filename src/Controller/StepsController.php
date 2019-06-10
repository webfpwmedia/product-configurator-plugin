<?php
namespace ARC\ProductConfigurator\Controller;

/**
 * Steps Controller
 *
 * @property ARC\ProductConfigurator\Model\Table\StepsTable $Steps
 *
 * @method ARC\ProductConfigurator\Model\Entity\Step[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StepsController extends AppController
{

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $configurator = $this->Steps->Configurators->get($this->getRequest()->getQuery('configurator_id'));

        $step = $this->Steps->newEntity();

        if ($this->getRequest()->is('post')) {
            $data = $this->getRequest()->getData();
            $data['configurator_id'] = $configurator->id;

            $step = $this->Steps->patchEntity($step, $data);

            if ($this->Steps->save($step)) {
                $this->Flash->success(__('The step has been saved.'));

                return $this->redirect(['action' => 'edit', $step->id]);
            }
            $this->Flash->error(__('The step could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('step', 'configurator'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Edit method
     *
     * @param string|null $id Step id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $step = $this->Steps->get($id, [
            'contain' => ['Configurators'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $step = $this->Steps->patchEntity($step, $this->request->getData());

            if ($this->Steps->save($step)) {
                $this->Flash->success(__('The step has been saved.'));

                return $this->redirect(['action' => 'edit', $step->id]);
            }
            $this->Flash->error(__('The step could not be saved. Please, try again.'));
        }

        $this
            ->set(compact('step'))
            ->viewBuilder()
            ->setTemplate('manage');
    }

    /**
     * Delete method
     *
     * @param string|null $id Step id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $step = $this->Steps->get($id);

        if ($this->Steps->delete($step)) {
            $this->Flash->success(__('The step has been deleted.'));
        } else {
            $this->Flash->error(__('The step could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Configurators', 'action' => 'edit', $step->configurator_id]);
    }
}
