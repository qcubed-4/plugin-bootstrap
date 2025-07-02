<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\ListControl;
use QCubed\Control\RadioButtonList;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\TagStyler;
use QCubed\Type;

/**
 * Class RadioList
 *
 * Bootstrap specific drawing of a \QCubed\Control\RadioButtonList
 *
 * Modes:
 * 	ButtonModeNone	Display as standard radio buttons using table styling if specified
 *  ButtonModeJq	Display as separate radio buttons styled with bootstrap styling
 *  ButtonModeSet	Display as a button group
 *  ButtonModeList	Display as standard radio buttons with no structure
 *
 * @property-write string ButtonStyle Bootstrap::ButtonPrimary, ButtonSuccess, etc.
 * @package QCubed\Bootstrap
 */
class RadioList extends RadioButtonList
{
    protected bool $blnWrapLabel = true;
    protected string $strButtonGroupClass = "radio";
    protected string $strButtonStyle = Bootstrap::BUTTON_DEFAULT;

    /**
     * Used by drawing routines to render the attributes associated with this control.
     *
     * @param null|array $attributeOverrides
     * @param null|array $styleOverrides
     * @return string
     */
    public function renderHtmlAttributes(?array $attributeOverrides = null, ?array $styleOverrides = null): string
    {
        if ($this->intButtonMode == RadioButtonList::BUTTON_MODE_SET) {
            $attributeOverrides["data-toggle"] = "buttons";
            $attributeOverrides["class"] = $this->CssClass;
            Html::addClass($attributeOverrides["class"], "btn-group");
        }
        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }

    /**
     * Retrieves the ending JavaScript script for the control, bypassing the default handling
     * specific to the \QCubed\Control\RadioButtonList.
     *
     * @return string The ending script for the control.
     */
    public function getEndScript(): string
    {
        $strScript = ListControl::getEndScript();    // bypass the \QCubed\Control\RadioButtonList end script
        return $strScript;
    }

    /**
     * Renders a button set by iterating through the available items, generating their HTML,
     * and wrapping the result inside a container element.
     *
     * @return string The HTML string representing the rendered button set.
     */
    public function renderButtonSet(): string
    {
        $count = $this->ItemCount;
        $strToReturn = '';
        for ($intIndex = 0; $intIndex < $count; $intIndex++) {
            $strToReturn .= $this->getItemHtml($this->getItem($intIndex), $intIndex, $this->getHtmlAttribute('tabindex'), $this->blnWrapLabel) . "\n";
        }
        $strToReturn = $this->renderTag('div', ['id'=>$this->strControlId], null, $strToReturn);
        return $strToReturn;
    }

    /**
     * Overrides the attributes of an individual item and its corresponding label in the control.
     *
     * @param mixed $objItem The item whose attributes are being modified. It contains the properties of the item, such as 'Selected'.
     * @param TagStyler $objItemAttributes The object handling the style and attributes of the item.
     * @param TagStyler $objLabelAttributes The object handling the style and attributes of the label associated with the item.
     *
     * @return void
     */
    protected function overrideItemAttributes(mixed $objItem, TagStyler $objItemAttributes, TagStyler $objLabelAttributes): void
    {
        if ($objItem->Selected) {
            $objLabelAttributes->addCssClass("active");
        }
    }

    /**
     * Updates the selection state of the control and marks it as modified.
     *
     * @return void
     */
    protected function refreshSelection(): void
    {
        $this->markAsModified();
    }

    /**
     * Sets the value of a property dynamically based on the property name.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     *
     * @throws InvalidCast Thrown when the provided value cannot be cast to the appropriate type.
     * @throws Caller Thrown when attempting to set an invalid or inaccessible property.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            // APPEARANCE
            case "ButtonStyle":
                try {
                    $this->objItemStyle->removeCssClass($this->strButtonStyle);
                    $this->strButtonStyle = Type::cast($mixValue, Type::STRING);
                    $this->objItemStyle->addCssClass($this->strButtonStyle);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case "ButtonMode":    // inherited
                try {
                    if ($mixValue === self::BUTTON_MODE_SET) {
                        $this->objItemStyle->setCssClass("btn");
                        $this->objItemStyle->addCssClass($this->strButtonStyle);
                        parent::__set($strName, $mixValue);
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}
