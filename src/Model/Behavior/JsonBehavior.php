<?php
namespace ARC\ProductConfigurator\Model\Behavior;

use Cake\Event\Event;
use Cake\ORM\Behavior;

/**
 * Class JsonBehavior.
 *
 * @package ARC\ProductConfigurator\Model\Behavior
 */
class JsonBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * `fields` array
     * List of fields to apply behavior logic to.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'fields' => [],
    ];

    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        $fields = $this->getConfig('fields');

        foreach ($data as $field => $value) {
            if (in_array($field, $fields)) {
                $data[$field] = str_replace(["\n", "\r", "\t"], '', $value);
            }
        }
    }
}
