<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Project\Control\TextBox as TextBoxBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;

/**
 * TextBox
 * Text boxes can be parts of input groups (implemented), and can have feedback icons (not yet implemented).
 *
 * Two ways to create a textbox with input groups: Either use this class, or use the InputGroup trait in your base
 * \QCubed\Project\Control\TextBox class.
 *
 * @property string $SizingClass Bootstrap::InputGroupLarge, Bootstrap::InputGroupMedium or Bootstrap::InputGroupSmall
 * @property string $LeftText Text to appear to the left of the input item.
 * @property string $RightText Text to appear to the right of the input item.
 *
 */

class TextBox extends TextBoxBase
{
    use InputGroupTrait;

    public function __construct(ControlBase|FormBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);

        Bootstrap::loadJS($this);

        $this->addCssClass(Bootstrap::FORM_CONTROL);
    }


    /**
     * Generates and returns the HTML for the control, wrapped in an input group.
     *
     * @return string The formatted HTML string for the control.
     */
    protected function getControlHtml(): string
    {
        $strToReturn = parent::getControlHtml();

        return $this->wrapInputGroup($strToReturn);
    }

    /**
     * Handles the retrieval of property values dynamically.
     *
     * @param string $strName The name of the property being accessed.
     *
     * @return mixed The value of the requested property, if it exists.
     * @throws Caller If the property does not exist or is inaccessible.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "SizingClass": return $this->sizingClass();
            case "LeftText": return $this->leftText();
            case "RightText": return $this->rightText();
            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Handles the dynamic setting of property values.
     *
     * @param string $strName The name of the property being assigned a value.
     * @param mixed $mixValue The value to assign to the specified property.
     *
     * @return void
     * @throws InvalidCast If the provided value cannot be cast to the appropriate type for the property.
     * @throws Caller If the property does not exist or is otherwise inaccessible.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "SizingClass": // Bootstrap::InputGroupLarge, Bootstrap::InputGroupMedium or Bootstrap::InputGroupSmall
                try {
                    $this->setSizingClass($mixValue);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "LeftText":
                try {
                    $this->setLeftText($mixValue);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "RightText":
                try {
                    $this->setRightText($mixValue);
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
