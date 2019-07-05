<?php
namespace ARC\ProductConfigurator\View\Widget;

use Cake\View\Form\ContextInterface;
use Cake\View\Widget\TextareaWidget;

/**
 * JsonWidget
 *
 * Pretty prints JSON in a textarea
 */
class JsonWidget extends TextareaWidget
{
    /**
     * @param array $data Data
     * @param ContextInterface $context Context
     * @return string
     */
    public function render(array $data, ContextInterface $context)
    {
        $data['type'] = 'textarea';

        if (isset($data['val']) && !is_string($data['val'])) {
            $data['val'] = json_encode($data['val'], JSON_PRETTY_PRINT);
        }

        return parent::render($data, $context);
    }
}
