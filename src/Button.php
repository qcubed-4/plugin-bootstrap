<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class Button
 *
 * Bootstrap styled buttons
 * FontAwesome styled icons
 *
 * @property string $StyleClass
 * @property string $SizeClass
 * @property string $Glyph
 * @property boolean $Tip
 *
 * Here has been implemented Bootstrap tooltip function. Where appropriate, you can activate Tooltip as follows:
 * $objButton->Tip = true;
 * $objButton->ToolTip = t('Text');
 *
 * @package QCubed\Bootstrap
 */
class Button extends \QCubed\Project\Control\Button
{
    protected string $strButtonStyle = Bootstrap::BUTTON_DEFAULT;
    protected string $strCssClass = "btn btn-default";
    protected ?string $strButtonSize = '';
    protected ?string $strGlyph = '';
    protected bool $blnTip = false;

    /**
     * Sets the CSS class for the style and updates the internal button style property.
     *
     * @param string $strStyleClass The CSS class to be assigned.
     *
     * @return void
     */
    public function setStyleClass(string $strStyleClass): void
    {
        $this->removeCssClass($this->strButtonStyle);
        $this->strButtonStyle = Type::cast($strStyleClass, Type::STRING);
        $this->addCssClass($this->strButtonStyle);
    }

    /**
     * Updates the size class of a button element by removing the current class,
     * setting the new size class, and adding the updated class.
     *
     * @param string $strSizeClass The new size class to be applied to the button.
     *
     * @return void
     */
    public function setSizeClass(string $strSizeClass): void
    {
        $this->removeCssClass($this->strButtonStyle);
        $this->strButtonSize = Type::cast($strSizeClass, Type::STRING);
        $this->addCssClass($this->strButtonSize);
    }

    /**
     * Initializes a jQuery widget for the control, particularly setting it up as a tooltip if applicable.
     *
     * @return void
     */
    protected function makeJqWidget(): void
    {
        if ($this->blnTip) {
            $this->setDataAttribute('toggle', 'tooltip');
            Application::executeControlCommand($this->ControlId, "bootstrapTooltip", Application::PRIORITY_HIGH);
        }
    }

    /**
     * Sets the value of a property based on the provided name and value.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "StyleClass":    // One of Bootstrap::ButtonDefault, ButtonPrimary, ButtonSuccess, ButtonInfo, ButtonWarning, ButtonDanger
                $this->setStyleClass($mixValue);
                break;

            case "SizeClass": // One of Bootstrap::ButtonLarge, ButtonMedium, ButtonSmall, ButtonExtraSmall
                $this->setSizeClass($mixValue);
                break;

            case "Glyph": // One of the glyph icons
                $this->strGlyph = Type::cast($mixValue, Type::STRING);
                break;

            case "Tip":
                $this->blnTip = Type::cast($mixValue, Type::BOOLEAN);
                break;

            case "PrimaryButton":
                try {
                    $this->blnPrimaryButton = Type::cast($mixValue, Type::BOOLEAN);
                    $this->setStyleClass(Bootstrap::BUTTON_PRIMARY);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }

    /**
     * Retrieves the inner HTML for the current object instance, appending a glyph icon
     * if it is defined.
     *
     * @return string The formatted inner HTML string, including the glyph icon if set.
     */
    protected function getInnerHtml(): string
    {
        $strToReturn = parent::getInnerHtml();
        if ($this->strGlyph) {
            $strToReturn = sprintf('<i class="%s" aria-hidden="true"></i>', $this->strGlyph) . $strToReturn;
        }
        return $strToReturn;
    }
}
