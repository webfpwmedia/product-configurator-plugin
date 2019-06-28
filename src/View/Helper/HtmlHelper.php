<?php
namespace ARC\ProductConfigurator\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper\HtmlHelper as BaseHtmlHelper;
use function http_build_query;

/**
 * Class HtmlHelper.
 *
 * @package App\View\Helper
 */
class HtmlHelper extends BaseHtmlHelper
{

    /**
     * Adds ImgIx query string arguments per `$options['size']` key.
     *
     * @link https://docs.imgix.com/apis/url
     *
     * @param array|string $path
     * @param array $options
     *
     * @return string
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
