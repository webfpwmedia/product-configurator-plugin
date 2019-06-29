<?php
namespace ARC\ProductConfigurator\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper\UrlHelper as BaseUrlHelper;

class UrlHelper extends BaseUrlHelper
{

    /**
     * Adds ImgIx query string arguments per `$options['size']` key.
     *
     * @param array|string $path
     * @param array $options
     * @return string
     * @link https://docs.imgix.com/apis/url
     */
    public function image($path, array $options = [])
    {
        $imgix = Configure::read('ARC.ProductConfigurator.imgix');

        if (isset($options['size']) && isset($imgix[$options['size']])) {
            $path .= '?' . http_build_query($imgix[$options['size']]);
            unset($options['size']);
        }

        return parent::image($path, $options);
    }
}
