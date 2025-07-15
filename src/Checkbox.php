<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Project\Control\Checkbox as CheckboxBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Type;
use Throwable;

/**
 * Class Checkbox
 *
 * Outputs a bootstrap style checkbox
 *
 * @property-write boolean $Inline whether checkbox should be displayed inline or wrapped in a div
 * @package QCubed\Bootstrap
 */
class Checkbox extends CheckboxBase
{
    protected ?bool $blnInline = false;
    protected bool $blnWrapLabel = true;

    /**
     * Renders a button element, with an optional wrapper if not inline.
     *
     * @param array $attrOverride An array of attributes to override the default button attributes.
     *
     * @return string The HTML string of the rendered button, optionally wrapped in a 'div' with the class 'checkbox'.
     */
    protected function renderButton(array $attrOverride): string
    {
        if (!$this->blnInline) {
            $strHtml = parent::renderButton($attrOverride);
            return Html::renderTag('div', ['class'=>'checkbox'], $strHtml);
        }
        return parent::renderButton($attrOverride);
    }

    /**
     * Renders the attributes for the label, applying additional styling for inline configuration.
     *
     * @return string The HTML string of the rendered label attributes, with inline-specific styling applied if necessary.
     */
    protected function renderLabelAttributes(): string
    {
        if ($this->blnInline) {
            $this->getCheckLabelStyler()->addCssClass(Bootstrap::CHECKBOX_INLINE);
        }
        return parent::renderLabelAttributes();
    }

    /**
     * Sets the value of a property by dynamically handling the specified property name.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     * @throws Caller
     * @throws InvalidCast
     * @throws Throwable
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "Inline":
                try {
                    $this->blnInline = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

}
