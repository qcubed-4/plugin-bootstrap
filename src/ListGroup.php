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
use QCubed\Control\DataRepeater;
use QCubed\Control\FormBase;
use QCubed\Control\Proxy;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Event\Click;
use QCubed\Action\AjaxControl;
use QCubed\Action\ActionBase;
use QCubed as Q;

/**
 * Class ListGroup
 * Implements a list group as a data repeater.
 * If you set SaveState = true, it will remember what was clicked and make it active.
 * Uses a proxy to display the items and process clicks.
 *
 * @property string $SelectedId
 *
 * @package QCubed\Bootstrap
 */
class ListGroup extends DataRepeater
{
    /** @var Proxy */
    protected Proxy|null $prxButton = null;

    protected string|int|null $strSelectedItemId = null;
    /** @var  callable */
    protected mixed $itemParamsCallback = null;

    /**
     * Constructs a new instance of the control, initializing it within the parent object.
     *
     * @param ControlBase|FormBase $objParentObject The parent object that this control or form belongs to.
     * @param string|null $strControlId Optional control ID for uniquely identifying this control.
     *
     * @return void
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        try {
            parent::__construct($objParentObject, $strControlId);
        } catch (Caller  $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        $this->prxButton = new Proxy($this);
        $this->setup();
        $this->addCssClass("list-group");
    }

    /**
     * Configures the necessary setup for the component, including event bindings.
     *
     * @return void
     */
    protected function setup(): void
    {
        // Setup Pagination Events
        $this->prxButton->addAction(new Click(), new AjaxControl($this, 'itemClick'));
    }

    /**
     * Associates a click action with the button.
     *
     * @param ActionBase $action The action to be executed when the button is clicked.
     *
     * @return void
     */
    public function addClickAction(ActionBase $action)
    {
        $this->prxButton->addAction(new Click(), $action);
    }

    /**
     * Generates the HTML for a single item.
     *
     * @param mixed $objItem The item data used to generate HTML, passed to the callback for additional parameters.
     *
     * @return string The HTML representation of the item.
     *
     * @throws Exception If the required itemParamsCallback is not provided.
     */
    protected function getItemHtml(mixed $objItem): string
    {
        if (!$this->itemParamsCallback) {
            throw new Exception("Must provide an itemParamsCallback");
        }

        $params = call_user_func($this->itemParamsCallback, $objItem, $this->intCurrentItemIndex);
        $strLabel = "";
        if (isset($params["html"])) {
            $strLabel = $params["html"];
        }
        $strId = "";
        if (isset($params["id"])) {
            $strId = $params["id"];
        }
        $strActionParam = $strId;
        if (isset($params["action"])) {
            $strActionParam = $params["action"];
        }

        $attributes = [];
        if (isset($params["attributes"])) {
            $attributes = $params["attributes"];
        }

        if (isset($attributes["class"])) {
            $attributes["class"] .= " list-group-item";
        } else {
            $attributes["class"] = "list-group-item";
        }

        if ($this->blnSaveState && $this->strSelectedItemId !== null && $this->strSelectedItemId == $strId) {
            $attributes["class"] .= " active";
        }
        $strLink = $this->prxButton->renderAsLink($strLabel, $strActionParam, $attributes, "a", false);

        return $strLink;
    }

    /**
     * Handles the click event on an item and updates the internal state.
     *
     * @param mixed $params The parameters associated with the item click event, typically including action-specific data.
     *
     * @return void
     */
    public function itemClick(mixed $params): void
    {
        if ($params) {
            $this->strSelectedItemId = $params->{ControlBase::ACTION_PARAM};
            if ($this->blnSaveState) {
                $this->blnModified = true;
            }
        }
    }

    /**
     * Set the item params callback. The callback should be of the form:
     *  func($objItem, $intCurrentItemIndex)
     * The callback will be give the current item from the data source, and the item's index visually.
     * The function should return a key/value array with the following possible items:
     *	html - the html to display as the innerHtml of the row.
     *  id - the id for the row tag
     *  attributes - Other attributes to put in the row tag.
     *
     * The callback is a callable, so can be of the form [$objControl, "func"]
     *
     * The row will automatically be given a class of "list-group-item", and the active row will also get the "active" class.
     *
     * @param callable $callback
     */
    public function setItemParamsCallback(callable $callback): void
    {
        $this->itemParamsCallback = $callback;
    }

    /**
     * Assigns a value to a class property dynamically based on its name.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property. May vary depending on the property type.
     *
     * @return void
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'SelectedId':
                $this->blnModified = true;
                $this->strSelectedItemId = $mixValue; // could be string or integer
                break;

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
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property being accessed.
     *
     * @return mixed Returns the value of the requested property. If the property does not exist, it attempts to call the parent implementation.
     *
     * @throws Caller Throws an exception if the property is not found or accessible, with an incremented offset.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'SelectedId':
                return $this->strSelectedItemId;

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
