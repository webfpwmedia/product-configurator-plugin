<?php
namespace ARC\ProductConfigurator\Form;

use ARC\ProductConfigurator\Model\Json\Bootstrap;
use ARC\ProductConfigurator\Model\Table\BuildsTable;
use Cake\Http\ServerRequest;
use Cake\View\Form\ContextInterface;

/**
 * ConfiguratorContext
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
     * Context
     *
     * @var Bootstrap
     */
    protected $_context;

    /**
     * Constructor.
     *
     * @param ServerRequest $request The request object.
     * @param Bootstrap $context Context
     */
    public function __construct(ServerRequest $request, Bootstrap $context)
    {
        $this->_request = $request;
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
        list($componentId, $token) = explode('.', $field);

        $component = $this->_context
            ->getComponentCollection()
            ->getComponent($componentId);

        if (!$component) {
            return null;
        }

        if ($token === BuildsTable::TEXT_INPUT) {
            // let JS set this
            return null;
        }

        if ($token === BuildsTable::CUSTOM_TEXT_INPUT) {
            foreach ($component->getOptions() as $optionSet) {
                if ($optionSet->isCustomizable()) {
                    $customToken = $optionSet->getToken();
                    $customizableValue = $optionSet->getCustomValue();
                }
            }

            if ($component->getSelection($customToken) === $customizableValue) {
                return $component->getCustomText();
            }

            return null;
        }

        if ($token === BuildsTable::QTY_INPUT) {
            return $component->getQty();
        }

        if ($token === BuildsTable::TOGGLE_INPUT) {
            return (int)$component->toggle;
        }

        return $component->getSelection($token);
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
