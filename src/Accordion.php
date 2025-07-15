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
use QCubed\Control\DataRepeater;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use Throwable;
use QCubed\Html;
use QCubed\Type;

/**
 * Accordion class
 * A wrapper class for objects that will be displayed using the RenderFormGroup method, and that will be drawn using
 * the "form-inline" class for special styling.
 *
 * You should set the PreferredRenderMethod attribute for each of the objects you add.
 *
 * Also, for objects that will be drawn with labels, use the "sr-only" class to hide the labels so that they are
 * available for screen readers.
 *
 * @property-write string $PanelStyle
 */

class Accordion extends DataRepeater
{
    const string RENDER_HEADER = 'header';
    const string RENDER_BODY = 'body';
    const string RENDER_FOOTER = 'footer';

    protected string $strCssClass = Bootstrap::PANEL_GROUP;
    protected int $intCurrentOpenItem = 0;
    protected mixed $drawingCallback;
    protected string $strPanelStyle = Bootstrap::PANEL_DEFAULT;

    /**
     * Constructor for initializing the control.
     *
     * @param ControlBase|FormBase $objParent The parent control or form.
     * @param string|null $strControlId Optional control ID.
     *
     * @return void
     * @throws Caller
     */
    public function __construct(ControlBase|FormBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);

        $this->strTemplate = __DIR__ . '/accordion.tpl.php';
        $this->setHtmlAttribute("role", "tablist");
        $this->setHtmlAttribute("aria-multiselectable", "true");
        Bootstrap::loadJS($this);
    }

    /**
     * Sets a callback function to be used during the drawing process.
     *
     * @param callable $callable The callback function to be executed. It should be a valid callable.
     *
     * @return void
     */
    public function setDrawingCallback(callable $callable): void
    {
        $this->drawingCallback = $callable;
    }

    /**
     * Callback from the standard template to render the header HTML. Calls the callback. The call callback should
     * call the RenderToggleHelper to render the toggling portion of the header.
     *
     * @param mixed $objItem
     *
     * @return void
     */
    protected function renderHeading(mixed $objItem): void
    {
        if ($this->drawingCallback) {
            call_user_func_array($this->drawingCallback, [$this, self::RENDER_HEADER, $objItem, $this->intCurrentItemIndex]);
        }
    }

    /**
     * Renders the body of an accordion item. Calls the callback to do so. You have some options here:
     * 	Draw just text. You should surround your text with <div class="panel-body"></div>
     *  Draw an item list. You should output a <ul class="list-group"> list (no panel-body needed). See the Bootstrap doc.
     *
     * @param mixed $objItem
     *
     * @return void
     */
    protected function renderBody(mixed $objItem): void
    {
        if ($this->drawingCallback) {
            call_user_func_array($this->drawingCallback, [$this, self::RENDER_BODY, $objItem, $this->intCurrentItemIndex]);
        }
    }

    /**
     * Renders the footer of an accordion item. Calls the callback to do so.
     * You should surround the content with a <div class="panel-footer"></div>.
     * If you don't want a footer, do nothing in response to the callback call.
     * @param mixed $objItem
     *
     * @return void
     */
    protected function renderFooter(mixed $objItem): void
    {
        if ($this->drawingCallback) {
            call_user_func_array($this->drawingCallback, [$this, self::RENDER_FOOTER, $objItem, $this->intCurrentItemIndex]);
        }
    }

    /**
     * Renders the given HTML with an anchor wrapper that will make it toggle the currently drawn item. This should be called
     * from your drawing callback when drawing the heading. This could span the entire heading or just a portion.
     *
     * @param string $strHtml
     * @param bool $blnRenderOutput
     * @return string
     */
    public function renderToggleHelper(string $strHtml, bool $blnRenderOutput = true): string
    {
        if ($this->intCurrentItemIndex == $this->intCurrentOpenItem) {
            $strClass = '';
            $strExpanded = 'true';
        } else {
            $strClass = 'collapsed';
            $strExpanded = 'false';
        }
        $strCollapseId = $this->strControlId . '_collapse_' . $this->intCurrentItemIndex;

        $strOut = _nl(Html::renderTag('a',
                ['class'=>$strClass,
                'data-toggle'=>'collapse',
                'data-parent'=>'#' . $this->strControlId,
                'href'=>'#' . $strCollapseId,
                'aria-expanded'=>$strExpanded,
                'aria-controls'=>$strCollapseId],
                $strHtml, false, true));

        if ($blnRenderOutput) {
            echo $strOut;
            return '';
        } else {
            return $strOut;
        }
    }

    /**
     * Handles the dynamic setting of properties on the object.
     * This method processes specific property names and assigns values to them
     * or delegates the assignment to the parent method if the property does not match.
     * If an invalid property is accessed, an exception is thrown with an adjusted stack trace.
     *
     * @param string $strName The name of the property being set.
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
            case 'PanelStyle':
                $this->strPanelStyle = Type::cast($mixValue, Type::STRING);
                break;

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
