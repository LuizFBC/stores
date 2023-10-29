<?php
declare(strict_types=1);

namespace App\Controller;

use App\Services\GetCepServices;
use App\Model\Entity\Address;
use App\Model\Entity\Store;
use Cake\Http\Response;
use Cake\View\JsonView;

/**
 * Stores Controller
 *
 * @property \App\Model\Table\StoresTable $Stores
 * @method \App\Model\Entity\Store[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class StoresController extends AppController
{
    public function viewClasses(): array
{
    return [JsonView::class];
}

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $stores = $this->paginate($this->Stores);

        $this->set(compact('stores'));
    }

    /**
     * View method
     *
     * @param string|null $id Store id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $store = $this->Stores->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('store'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    
     public function add()
{
    $this->request->allowMethod(['post']);
    $jsonData = $this->request->input('json_decode');

    if ($jsonData === null) {
        $this->setErrorMessageResponse('Invalid JSON data.');
        return;
    }

    $cepService = new GetCepServices();
    $cep = $cepService->findCep($jsonData->cep);
    $cepObject = (object)$cep;

    if (!$cep) {
        $this->setErrorMessageResponse('CEP not found.');
        return;
    }

    $jsonDataArray = json_decode(json_encode($jsonData), true);

    $store = $this->Stores->newEntity($jsonDataArray);

    if ($store->hasErrors()) {
        $this->setErrorMessageResponse('Validation errors.');
        return;
    }

    $address = new Address([
        'postal_code' => $jsonData->cep,
        'street_number' => $jsonData->street_number,
        'state' => $cepObject->uf,
        'street' => $cepObject->logradouro,
        'city' => $cepObject->localidade,
        'complement' => $jsonData->complement,
    ]);

    $store->addresses = [$address]; // Use o nome da associação definida em Store

    if ($this->Stores->save($store, ['associated' => ['Addresses']])) {
        $this->Flash->success('The store has been saved.');
        $this->set('store', $store);
        return;
    }

    $this->setErrorMessageResponse('The store could not be saved. Please, try again.');
}

    /**
     * Edit method
     *
     * @param string|null $id Store id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $store = $this->Stores->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $store = $this->Stores->patchEntity($store, $this->request->getData());
            if ($this->Stores->save($store)) {
                $this->Flash->success(__('The store has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The store could not be saved. Please, try again.'));
        }
        $this->set(compact('store'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Store id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $store = $this->Stores->get($id);
        if ($this->Stores->delete($store)) {
            $this->Flash->success(__('The store has been deleted.'));
        } else {
            $this->Flash->error(__('The store could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    private function setErrorMessageResponse(string $message) {
        $this->set([ 'message' => $message ]);
        $this->response = $this->response->withStatus(422);

    } 
}
