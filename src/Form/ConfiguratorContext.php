<?php
namespace ARC\ProductConfigurator\Form;

use ARC\ProductConfigurator\Model\Table\BuildsTable;
use Cake\Http\ServerRequest;
use Cake\View\Form\ContextInterface;

/**
 * ConfiguratorContext
 *
 * Context should be an array of arrays formatted like so:
 * ```
 * [
 *   {
 *     "qty": 1,
 *     "component": "1ca6664c-68d1-427e-90e1-addf1eca0019",
 *     "selections": {
 *       "size": "sm",
 *       "color": "RR",
 *       "length": "long",
 *       "closure": "T"
 *     }
 *   }
 * ]
 * ```
 *
 * Fields created in a configurator form should be named as:
 *
 * `$componentId.$mask`
 */
class ConfiguratorContext implements ContextInterface
{
    /**
     * The request object.
     *
     * @var ServerRequest
     */
    protected $_request;

    /**
     * Context data for this object.
     *
     * @var array
     */
    protected $_context;

    /**
     * Constructor.
     *
     * @param ServerRequest $request The request object.
     * @param array $context Context info.
     */
    public function __construct(ServerRequest $request, array $context)
    {
        $this->_request = $request;
        $context += [
            'schema' => [],
            'required' => [],
            'defaults' => [],
            'errors' => [],
        ];
        $this->_context = $context;
    }

    /**
     * Gets value from bootstrap JSON
     *
     * @param string $field
     * @return mixed
     */
    public function val($field)
    {
        list($componentId, $mask) = explode('.', $field);

        $selected = collection($this->_context)
            ->firstMatch([
                'component' => $componentId
            ]);

        if (!$selected) {
            return null;
        }

        if ($mask === BuildsTable::CUSTOM_TEXT_INPUT) {
            return $selected['text'] ?? null;
        }

        return $selected['selections'][$mask];
    }

    /**
     * @return array|string[]
     */
    public function fieldNames()
    {
        return [];
    }

    /**
     * @param string $field
     * @return bool
     */
    public function hasError($field)
    {
        return false;
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isRequired($field)
    {
        return false;
    }

    /**
     * @param string $field
     * @return array
     */
    public function attributes($field)
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isCreate()
    {
        return true;
    }

    /**
     * @param string $field
     * @return string|null
     */
    public function type($field)
    {
        return 'radio';
    }

    /**
     * @param string $field
     * @return array
     */
    public function error($field)
    {
        return [];
    }

    /**
     * @param string $field
     * @return bool
     */
    public function isPrimaryKey($field)
    {
        return false;
    }

    /**
     * @param $field
     * @return int|null
     */
    public function getMaxLength($field)
    {
        return 0;
    }

    /**
     * @return array|null
     */
    public function primaryKey()
    {
        return null;
    }

    /**
     * @param $field
     * @return string|null
     */
    public function getRequiredMessage($field)
    {
        return '';
    }
}
