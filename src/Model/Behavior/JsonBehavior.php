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

    /**
     * Marshals white-space chars into nothing on applicable fields.
     *
     * @param Event $event
     * @param \ArrayObject $data
     * @param \ArrayObject $options
     */
    public function beforeMarshal(Event $event, \ArrayObject $data, \ArrayObject $options)
    {
        $fields = $this->getConfig('fields', []);

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data[$field] = json_encode($data[$field]);
            }
        }
    }
}
