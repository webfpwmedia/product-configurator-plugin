<?php
namespace ARC\ProductConfigurator\View\Widget;

use Cake\View\Widget\FileWidget;

/**
 * Class InsecureFileWidget.
 *
 * Prevents the `name`, `type`, `tmp_name`, `error` & `size` fields from
 * being treated as secure fields.
 *
 * Only use when explicitly wanting to _not_ send the user-selected
 * file to the server.
 *
 * @package ARC\ProductConfigurator\View\Widget
 */
class InsecureFileWidget extends FileWidget
{
    /**
     * {@inheritDoc}
     */
    public function secureFields(array $data)
    {
        return [];
    }
}
