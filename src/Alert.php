<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\ApplicationBase;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Control\Panel;
use QCubed\Exception\Caller;
use QCubed\Html;
use QCubed\Js;
use QCubed\Project\Application;
use QCubed\Type;

/**
 * Class Alert
 *
 * Implements the Bootstrap "Alert" functionality. This can be a static block of text, or can alternately have a close
 * button that automatically hides the alert.
 *
 * Per Bootstraps documentation, you MUST specify an alert type class. Do this by using AddCssClass, or the CssClass
 * Attribute with a plus in front of the class. For example:
 * 	$objAlert->CssClass = '+' . Bootstrap::AlertSuccess;
 *
 * Use Display or Visible to show or hide the alert as needed. Or, set the
 * Dismissable attribute.
 *
 * Since its a \QCubed\Control\Panel, you can put text, template or child controls in it.
 *
 * By default, alerts will fade on close. Remove the fade class if you want to turn this off.
 *
 * Call Close() to close the alert manually.
 *
 */
class Alert extends Panel
{
    protected string $strCssClass = 'alert fade in';

    protected ?bool $blnDismissable = false;

    /**
     * Alert constructor.
     * @param ControlBase|FormBase $objParent
     * @param null $strControlId
     */
    public function __construct(ControlBase|FormBase$objParent, ?string $strControlId = null)
    {
        parent::__construct ($objParent, $strControlId);

        $this->setHtmlAttribute("role", "alert");
        Bootstrap::loadJS($this);
    }

    /**
     * Retrieves the inner HTML content for the current element, optionally appending
     * a dismissable button if the element is configured as dismissable.
     *
     * @return string The processed inner HTML content, including any additional
     *                dismissable button if applicable.
     */
    protected function getInnerHtml(): string
    {
        $strText = parent::getInnerHtml();

        if ($this->blnDismissable) {
            $strText = Html::renderTag('button',
                ['type'=>'button',
                'class'=>'close',
                'data-dismiss'=>'alert',
                'aria-label'=>"Close",
                ],
                '<span aria-hidden="true">&times;</span>', false, true)
            . $strText;
        }
        return $strText;
    }

    /**
     * Initializes the jQuery widget for the control and, if the control is configured as dismissable,
     * binds a JavaScript event to handle the widget's `closed.bs.alert` event. This ensures that the visibility
     * state of the control is recorded when dismissed.
     *
     * @return void
     */
    protected function makeJqWidget(): void
    {
        parent::makeJqWidget();
        if ($this->blnDismissable) {
            Application::executeControlCommand($this->ControlId, 'on', 'closed.bs.alert',
                new Js\Closure("qcubed.recordControlModification ('{$this->ControlId}', '_Visible', false)"), ApplicationBase::PRIORITY_HIGH);
        }
    }

    /**
     * Closes the alert using the Bootstrap javascript mechanism to close it. Removes the alert from the DOM.
     * Bootstrap has no mechanism for showing it again, so you will need
     * to redraw the control to show it.
     *
     * @return void This method does not return a value.
     */
    public function close(): void
    {
        $this->blnVisible = false;
        Application::executeControlCommand($this->ControlId, 'alert', 'close');
    }

    /**
     * Magic method for retrieving the value of a property by name.
     * It supports specific property names for internal control
     * and delegates other property retrievals to the parent class.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the requested property, or the result
     *               from the parent class's __get method if the property
     *               is not specifically handled.
     * @throws Caller If the requested property is not defined.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case "Dismissable":
            case "HasCloseButton": // QCubed synonym
                return $this->blnDismissable;

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
     * Magic method to set the value of a property dynamically based on its name.
     * Handles specific logic for predefined property names, including modifying
     * visibility, dismissable state, and applicable CSS classes.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     *
     * @throws Caller Thrown when a non-existent property is accessed or modified.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'Dismissable':
            case "HasCloseButton": // QCubed synonym
                $blnDismissable = Type::cast($mixValue, Type::BOOLEAN);
                if ($blnDismissable != $this->blnDismissable) {
                    $this->blnDismissable = $blnDismissable;
                    $this->blnModified = true;
                    if ($blnDismissable) {
                        $this->addCssClass(Bootstrap::ALERT_DISMISSABLE);
                        Bootstrap::loadJS($this);
                    } else {
                        $this->removeCssClass(Bootstrap::ALERT_DISMISSABLE);
                    }
                }
                break;

            case '_Visible':	// Private attribute to record the visible state of the alert
                $this->blnVisible = $mixValue;
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