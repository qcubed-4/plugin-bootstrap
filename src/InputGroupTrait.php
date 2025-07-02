<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\Panel;
use QCubed\Html;
use QCubed\Jqui\Action\Size;
use QCubed\QString;
use QCubed\Type;

/**
 * Class InputGroupTrait
 *
 * Adds input group functionality to a control. Specifically designed for \QCubed\Project\Control\TextBox controls and subclasses.
 *
 * @package QCubed\Bootstrap
 */
trait InputGroupTrait
{
    /** @var  string|null */
    protected ?string $strSizingClass = null;
    /** @var  string|null */
    protected ?string $strLeftText = null;
    /** @var  string|null */
    protected ?string $strRightText = null;
    /** @var bool */
    protected ?bool $blnInputGroup = false;    // for subclasses
    /** @var  Panel|null */
    protected ?Panel $pnlLeftButtons = null;
    /** @var  Panel|null */
    protected ?Panel $pnlRightButtons = null;

    /**
     * Wraps the provided control HTML in an input group if specific conditions are met.
     *
     * @param string $strControlHtml The HTML content for the control to be wrapped.
     *
     * @return string The modified HTML with an input group wrapper if applicable.
     */
    protected function wrapInputGroup(string $strControlHtml): string
    {
        if ($this->strLeftText ||
            $this->strRightText ||
            $this->blnInputGroup ||
            $this->pnlLeftButtons ||
            $this->pnlRightButtons
        ) {
            $strClass = 'input-group';
            if ($this->strSizingClass) {
                Html::addClass($strClass, $this->strSizingClass);
            }

            $strControlHtml = Html::renderTag('div', ['class' => $strClass],
                $this->getLeftHtml() .
                $strControlHtml .
                $this->getRightHtml());
        }

        return $strControlHtml;
    }

    /**
     * Generates and returns the HTML content for the left section of an input group.
     *
     * @return string The HTML string representing the left section, which may include text or rendered buttons. Returns an empty string if neither is available.
     */
    protected function getLeftHtml(): string
    {
        if ($this->strLeftText) {
            return sprintf('<span class="input-group-addon">%s</span>', QString::htmlEntities($this->strLeftText));
        } elseif ($this->pnlLeftButtons) {
            return $this->pnlLeftButtons->render(false);
        }
        return '';
    }

    /**
     * Generates and returns the HTML content for the right section of an input group.
     *
     * @return string The HTML string representing the right section, which may include text or rendered buttons. Returns an empty string if neither is available.
     */
    protected function getRightHtml(): string
    {
        if ($this->strRightText) {
            return sprintf('<span class="input-group-addon">%s</span>', QString::htmlEntities($this->strRightText));
        } elseif ($this->pnlRightButtons) {
            return $this->pnlRightButtons->render(false);
        }
        return '';
    }

    /**
     * Retrieves the sizing class for the component.
     *
     * @return string|null The sizing class as a string if set, or null if no sizing class is specified.
     */
    public function sizingClass(): ?string
    {
        return $this->strSizingClass;
    }

    /**
     * Retrieves the text content intended for the left section of the input group.
     *
     * @return string|null The text content if available, or null if no text is set.
     */
    public function leftText(): ?string
    {
        return $this->strLeftText;
    }

    /**
     * Retrieves the text content intended for the right section.
     *
     * @return string|null The text for the right section if available, or null if no text is set.
     */
    public function rightText(): ?string
    {
        return $this->strRightText;
    }

    /**
     * Marks the current entity or object as modified, typically to indicate that it has unsaved changes or needs to be processed further.
     *
     * @return void
     */
    abstract public function markAsModified(): void;

    /**
     * Sets the sizing class for an element and marks it as modified if the value changes.
     *
     * @param string $strSizingClass The sizing class to be assigned. It will be type-cast to a string.
     *
     * @return void
     */
    public function setSizingClass(string $strSizingClass): void
    {
        $strSizingClass = Type::cast($strSizingClass, Type::STRING);
        if ($strSizingClass != $this->strSizingClass) {
            $this->markAsModified();
            $this->strSizingClass = $strSizingClass;
        }
    }

    /**
     * Sets the text for the left section of an input group.
     *
     * @param string $strLeftText The text to be displayed on the left side of the input group.
     *
     * @return void
     */
    public function setLeftText(string $strLeftText): void
    {
        $strText = Type::cast($strLeftText, Type::STRING);
        if ($strText != $this->strLeftText) {
            $this->markAsModified();
            $this->strLeftText = $strText;
        }
    }

    /**
     * Sets the text for the right section of an input group and marks the object as modified if the text is changed.
     *
     * @param string $strRightText The new text to be set for the right section.
     *
     * @return void
     */
    public function setRightText(string $strRightText): void
    {
        $strText = Type::cast($strRightText, Type::STRING);
        if ($strText != $this->strRightText) {
            $this->markAsModified();
            $this->strRightText = $strText;
        }
    }

    /**
     * Sets the left button panel for the input group.
     *
     * @param Panel $panel The Panel object to be used as the left button panel. The method adds the necessary CSS class to the panel before assigning it.
     *
     * @return void
     */
    public function setLeftButtonPanel(Panel $panel): void
    {
        $panel->addCssClass('input-group-btn');
        $this->pnlLeftButtons = $panel;
    }

    /**
     * Sets the right button panel for the input group.
     *
     * @param Panel $panel The panel to be added as the right button panel. The method applies the required CSS class to ensure proper styling within the input group.
     *
     * @return void
     */
    public function setRightButtonPanel(Panel $panel): void
    {
        $panel->addCssClass('input-group-btn');
        $this->pnlRightButtons = $panel;
    }

}
