<?php
namespace ARC\ProductConfigurator\View\Widget;

use Cake\Utility\Hash;
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
        $data = Hash::merge([
            'templateVars' => [
                'class' => null,
            ],
        ], $data);

        $data['type'] = 'textarea';
        $data['templateVars']['class'] .= ' json-editor';

        if (!isset($data['val'])) {
            $data['val'] = '[]';
        }

        if (!is_string($data['val'])) {
            $data['val'] = json_encode($data['val'], JSON_PRETTY_PRINT);
        }

        return parent::render($data, $context);
    }
}
