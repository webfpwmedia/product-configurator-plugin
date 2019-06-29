<?php
namespace ARC\ProductConfigurator\View\Widget;

use Cake\View\Widget\RadioWidget as BaseRadioWidget;

/**
 * Input widget class for generating a set of radio buttons.
 *
 * This class is intended as an internal implementation detail
 * of Cake\View\Helper\FormHelper and is not intended for direct use.
 *
 * @todo delete once this plugin supports >=3.8
 */
class RadioWidget extends BaseRadioWidget
{

    /**
     * Renders a label element for a given radio button.
     *
     * In the future this might be refactored into a separate widget as other
     * input types (multi-checkboxes) will also need labels generated.
     *
     * @param array $radio The input properties.
     * @param false|string|array $label The properties for a label.
     * @param string $input The input widget.
     * @param \Cake\View\Form\ContextInterface $context The form context.
     * @param bool $escape Whether or not to HTML escape the label.
     * @return string|bool Generated label.
     */
    protected function _renderLabel($radio, $label, $input, $context, $escape)
    {
        if (isset($radio['label'])) {
            $label = $radio['label'];
        } elseif ($label === false) {
            return false;
        }
        $labelAttrs = is_array($label) ? $label : [];
        $labelAttrs += [
            'for' => $radio['id'],
            'escape' => $escape,
            'text' => $radio['text'],
            'templateVars' => $radio['templateVars'],
            'input' => $input,
        ];

        return $this->_label->render($labelAttrs, $context);
    }
}
