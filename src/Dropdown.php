<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Action\ActionBase;
use QCubed\Action\ActionParams;
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\HList;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\Project\Application;
use QCubed\Type;
use QCubed\Js;

/**
 * Class Dropdown
 *
 * Implements a standalone dropdown button. This can be styled as a typical bootstrap button, with the dropdown list
 * inside the button, or without a button surrounding the list, which makes it look more like a typical menu list.
 *
 * @package QCubed\Bootstrap
 *
 * @property string  	$StyleClass		The button style. i.e. Bootstrap::ButtonPrimary
 * @property string  	$SizeClass 		The button size class. i.e. Bootstrap::ButtonSmall
 * @property bool  		$AsButton 		Whether to show it as a button, or a just a menu.
 * @property bool  		$Split 			Whether to split the button into a button and a menu.
 * @property bool  		$Up 			Whether to pop up the menu above or below the button.
 * @property bool  		$Text 			The text to appear on the button. Synonym of Name.
 */
class Dropdown extends HList {
	/** @var bool Whether to show it as a button tag or anchor tag */
    protected ?bool $blnAsButton = false;
    protected ?bool $blnSplit = false;
    protected ?bool $blnUp = false;
    protected string $strButtonStyle = Bootstrap::BUTTON_DEFAULT;
    protected ?string $strButtonSize = '';

    /**
     * Constructor for the class.
     *
     * @param ControlBase|FormBase $objParentObject The parent object that contains this control.
     * @param string|null $strControlId Optional control ID. If not provided, a new ID will be generated.
     *
     * @return void
     */
    public function __construct(ControlBase|FormBase $objParentObject, ?string $strControlId = null)
    {
        parent::__construct($objParentObject, $strControlId);

        // utilize the wrapper to group the components of the button
        $this->blnUseWrapper = true;
        $this->addWrapperCssClass("dropdown"); // default to menu type of drowdown
    }

    /**
     * Generates the HTML content for the control.
     *
     * This method creates the appropriate HTML structure for the control, taking into account
     * its properties, such as whether it should render as a button, include dropdown elements,
     * or split button styles. It also handles binding data and rendering child items as part
     * of the control's dropdown list.
     *
     * @return string The generated HTML content for the control.
     */
    public function getControlHtml(): string
    {
        $strHtml = Html::renderString($this->Name);
        if (!$this->blnAsButton) {
            $strHtml .= ' <span class="caret"></span>';
            $strHtml = $this->renderTag("a", ["href"=>"#", "data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"], null, $strHtml);
        } else {
            if (!$this->blnSplit) {
                $strHtml .= ' <span class="caret"></span>';
                $strHtml = $this->renderTag("button", ["data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"], null, $strHtml);
            } else {
                $strHtml = $this->renderTag("button", null, null, $strHtml);
                $strClass = "btn dropdown-toggle " . $this->strButtonSize . " " . $this->strButtonStyle;
                $strHtml .= Html::renderTag("button", ["class" => $strClass, "data-toggle"=>"dropdown", "aria-haspopup"=>"true", "aria-expanded"=>"false"]);
            }
        }
        if ($this->hasDataBinder()) {
            $this->callDataBinder();
        }
        if ($this->getItemCount()) {
            $strListHtml = '';
            foreach ($this->getAllItems() as $objItem) {
                $strListHtml .= $this->getItemHtml($objItem);
            }

            $strHtml .= Html::renderTag("ul", ["id"=>$this->ControlId . "_list", "class"=>"dropdown-menu", "aria-labelledby" => $this->ControlId], $strListHtml);
        }
        if ($this->hasDataBinder()) {
            $this->removeAllItems();
        }

        return $strHtml;
    }

    /**
     * Adds a menu item to the dropdown.
     *
     * @param DropdownItem $objMenuItem The menu item to be added.
     *
     * @return void
     */
    public function addMenuItem(DropdownItem $objMenuItem): void
    {
        parent::addItem($objMenuItem);
    }

    /**
     * Retrieves the text representation of a dropdown item.
     *
     * @param DropdownItem $objItem The dropdown item for which the text is to be retrieved.
     *
     * @return string The text associated with the provided dropdown item.
     */
    protected function getItemText(mixed $objItem): string
    {
        return $objItem->getItemText();	// redirect to subclasses of item
    }

    /**
     * Retrieves the sub-tag attributes from the provided object.
     *
     * @param mixed $objItem The object from which the sub-tag attributes will be obtained.
     *
     * @return mixed The sub-tag attributes of the provided object.
     */
    public function getSubTagAttributes(mixed $objItem): array|string|null
    {
        return $objItem->getSubTagAttributes();
    }

    /**
     * Sets the style class for the button, updating the CSS classes applied.
     *
     * @param string $strStyleClass The new style class to be applied to the button.
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
     * Sets the size class for the button and updates its CSS classes accordingly.
     *
     * @param string $strSizeClass The size class to apply to the button.
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
     * Initializes and binds the jQuery widget to the control's dropdown list element.
     * This includes setting up event handling for the selection mechanism.
     *
     * @return void
     */
    protected function makeJqWidget(): void
    {
        // Trigger the dropdown select event on the main control
        Application::executeSelectorFunction('#' . $this->ControlId . "_list", 'on', 'click', 'li',
            new Js\Closure("\njQuery('#$this->ControlId').trigger ('bsdropdownselect', {id:this.id, value:\$j(this).data('value')});\n"), Application::PRIORITY_HIGH);
    }

    /**
     * Processes action parameters passed to the method.
     *
     * @param ActionParams $params Action parameters containing the necessary data for processing actions.
     *
     * @return void
     */
    protected function processActionParameters(ActionParams $params): void
    {
        parent::processActionParameters($params);
        if ($this->blnEncryptValues) {
            $actionParam = $params->ActionParameter;

            $actionParam['value'] = $this->decryptValue($actionParam['value']); // Decrypt the value if needed.
            $params->ActionParameter = $actionParam;
        }
    }

    /**
     * Magic method __get to retrieve the value of a property.
     *
     * @param string $strName The name of the property to be retrieved.
     *
     * @return mixed The value of the requested property. If the property is undefined, it attempts to call the parent __get method.
     * Raises an exception if the property does not exist in both local and parent implementations.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            // APPEARANCE
            case "StyleClass": return $this->strButtonStyle;
            case "SizeClass": return $this->strButtonSize;
            case "AsButton": return $this->blnAsButton;
            case "Split": return $this->blnSplit;
            case "Up": return $this->blnUp;
            case "Text": return $this->strName;
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
     * Magic method to set the properties of the class dynamically.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case "StyleClass":	// One of Bootstrap::ButtonDefault, ButtonPrimary, ButtonSuccess, ButtonInfo, ButtonWarning, ButtonDanger
                $this->setStyleClass($mixValue);
                break;

            case "SizeClass": // One of Bootstrap::ButtonLarge, ButtonMedium, ButtonSmall, ButtonExtraSmall
                $this->setSizeClass($mixValue);
                break;

            case "AsButton":
                $this->blnAsButton = Type::cast($mixValue, Type::BOOLEAN);
                if ($this->blnAsButton) {
                    $this->addCssClass("btn");
                    $this->addCssClass($this->strButtonStyle);
                    if ($this->strButtonSize) {
                        $this->addCssClass($this->strButtonSize);
                    }
                    if (!$this->blnSplit) {
                        $this->addCssClass("dropdown-toggle");
                    }
                    $this->removeWrapperCssClass("dropdown");
                    $this->addWrapperCssClass("btn-group");
                } else {
                    $this->removeCssClass("btn");
                    $this->removeCssClassesByPrefix("btn-");
                    $this->addWrapperCssClass("dropdown");
                    $this->removeWrapperCssClass("btn-group");
                }
                break;

            case "Split":
                $this->blnSplit = Type::cast($mixValue, Type::BOOLEAN);
                if (!$this->blnSplit) {
                    $this->addCssClass("dropdown-toggle");
                } else {
                    $this->removeCssClass("dropdown-toggle");
                }

                break;

            case "Up":
                $this->blnUp = Type::cast($mixValue, Type::BOOLEAN);
                if ($this->blnUp) {
                    $this->addWrapperCssClass("dropup");
                } else {
                    $this->removeWrapperCssClass("dropup");
                }
                break;

            case "Text":
                // overload Name as Text too.
                parent::__set("Name", $mixValue);
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
}

