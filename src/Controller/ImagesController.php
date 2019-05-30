<?php
namespace App\Controller;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

/**
 * Images Controller
 *
 * @property \App\Model\Table\ImagesTable $Images
 *
 * @method \App\Model\Entity\Image[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImagesController extends AppController
{

    /**
     * Index
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $images = $this->Images->find();
        $this->set('images',  $this->paginate($images));
    }

    /**
     * ListBucket
     *
     * @return \Cake\Http\Response|void
     */
    public function listBucket()
    {
        $client = new S3Client([
            'credentials' => [
                'key'    => env('AMAZON_S3_KEY'),
                'secret' => env('AMAZON_S3_SECRET'),
            ],
            'region' => env('AMAZON_S3_REGION'),
            'version' => '2006-03-01',
        ]);

        $adapter = new AwsS3Adapter($client, env('AMAZON_S3_BUCKET'), env('AMAZON_S3_PATH'));
        $filesystem = new Filesystem($adapter);

        $images = $this->Images
            ->find()
            ->select(['name'])
            ->enableHydration(false)
            ->extract('name')
            ->toList();

        $allFiles = collection($filesystem->listContents())
            ->reject(function ($file) use ($images) {
                return in_array($file['path'], $images);
            })
            ->toList();

        $this->set(compact('allFiles'));
    }

    /**
     * Add
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $image = $this->Images->newEntity();

        if ($this->request->is(['post'])) {
            $image = $this->Images->patchEntity($image, $this->request->getData());

            if ($this->Images->save($image)) {
                return $this->redirect(['action' => 'edit', $image->id]);
            }

            $this->Flash->error(__('The image could not be indexed. Please, try again.'));
        }

        $this->set('image', $image);
        $this->set('errors', $image->getErrors());
        $this->set('_serialize', ['image', 'errors']);
    }

    /**
     * @param int|null $id
     * @return \Cake\Http\Response|null
     */
    public function edit($id = null)
    {
        $image = $this->Images->get($id);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $image = $this->Images->patchEntity($image, $this->getRequest()->getData());
            if ($this->Images->save($image)) {
                $this->Flash->success(__('The image has been updated.'));

                return $this->redirect(['action' => 'edit', $id]);
            }

            $this->Flash->error(__('The image could not be updated. Please, try again.'));
        }

        $this->set('image', $image);
    }
}
