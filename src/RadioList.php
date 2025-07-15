<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Bootstrap as Bs;
use QCubed\Control\ListControl;
use QCubed\Control\RadioButtonList;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Html;
use QCubed\Type;

/**
 * Class RadioList
 *
 * Bootstrap specific drawing of a \QCubed\Control\RadioButtonList
 *
 * Modes:
 * 	ButtonModeNone Display as standard radio buttons using table styling if specified
 *  ButtonModeJq Display as separate radio buttons styled with bootstrap styling
 *  ButtonModeSet Display as a button group
 *  ButtonModeList Display as standard radio buttons with no structure
 *
 * @property string $ButtonGroupClass Allows you to set the theme.
 * @property string $GroupName assigns the radio button into a radio button group (optional) so that no more than one radio in that group may be selected at a time.
 * @property boolean $Checked specifies whether or not the radio is selected
 *
 * @property-write string ButtonStyle Bootstrap::ButtonPrimary, ButtonSuccess, etc.
 * @package QCubed\Bootstrap
 */
class RadioList extends RadioButtonList
{
    protected string $strButtonGroupClass = "radio";
    protected bool $blnChecked;
    protected string $strButtonStyle = Bs\Bootstrap::BUTTON_DEFAULT;
    /**
     * Group to which this radio button belongs
     * Groups determine the 'radio' behavior wherein you can select only one option out of all buttons in that group
     * @var null|string Name of the group
     */
    protected ?string $strGroupName = null;

    /**
     * Renders the HTML attributes for the control, with optional overrides for attributes and styles.
     * When rendered in button mode, it adds a toggle and class attributes specific to the button group styling.
     *
     * @param array|null $attributeOverrides Optional overrides for the HTML attributes.
     * @param array|null $styleOverrides Optional overrides for the inline styles.
     *
     * @return string The rendered HTML attributes as a string.
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
     * Retrieves the end script for the control bypassing the RadioButtonList-specific implementation.
     *
     * @return string The end script for the control.
     */
    public function getEndScript(): string
    {
        // bypass the \QCubed\Control\RadioButtonList end script
        return ListControl::getEndScript();
    }

    /**
     * Magic method to set the value of a property dynamically based on its name. Handles specific property cases
     * like appearance settings, group configuration, button modes, and more.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to be set for the property.
     *
     * @return void
     *
     * @throws InvalidCast If the provided value cannot be cast to the expected type.
     * @throws Caller If the property is not recognized and cannot be handled by the parent class.
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
            case "GroupName":
                try {
                    $strGroupName = Type::cast($mixValue, Type::STRING);
                    if ($this->strGroupName != $strGroupName) {
                        $this->strGroupName = $strGroupName;
                        $this->blnModified = true;
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "Checked":
                try {
                    $val = Type::cast($mixValue, Type::BOOLEAN);
                    if ($val != $this->blnChecked) {
                        $this->blnChecked = $val;
                        if ($this->GroupName && $val) {
                            Application::executeJsFunction('qcubed.setRadioInGroup', $this->strControlId);
                        } else {
                            $this->addAttributeScript('prop', 'checked', $val); // just set the one radio
                        }
                    }
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case "ButtonGroupClass":
                $this->strButtonGroupClass = Type::cast($mixValue, Type::STRING);
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

    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the requested property.
     *               Returns the value of "GroupName" or "Checked" if accessed directly.
     *               Falls back to the parent::__get method for other properties.
     * @throws Caller If the property does not exist or is inaccessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "GroupName": return $this->strGroupName;
            case "Checked": return $this->blnChecked;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
}