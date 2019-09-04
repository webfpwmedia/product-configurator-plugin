<?php
namespace ARC\ProductConfigurator\View\Widget;

use ARC\ProductConfigurator\Filesystem\AmazonS3;
use Cake\View\Form\ContextInterface;
use Cake\View\Widget\BasicWidget;
use League\Flysystem\FileNotFoundException;

/**
 * Class BlobWidget.
 *
 * Parses a cloud-persisted file into blob data for form posting.
 *
 * @package ARC\ProductConfigurator\View\Widget
 */
class BlobWidget extends BasicWidget
{
    /**
     * Base64 encode data from cloud storage into form value.
     *
     * @param array $data Data
     * @param ContextInterface $context Context
     *
     * @return string
     *
     * @throws FileNotFoundException
     */
    public function render(array $data, ContextInterface $context)
    {
        if (isset($data['val'])) {
            $filesystem = AmazonS3::get(env('AMAZON_S3_PATH_UPLOAD'));

            $blob = [
                'data:' . $filesystem->getMimetype($data['val']) . ';base64',
                base64_encode($filesystem->read($data['val'])),
            ];

            $data['val'] = join(',', $blob);
        }

        return parent::render($data, $context);
    }
}
