<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Html;
use QCubed\HtmlAttributeManagerBase;
use QCubed\Project\Control\TextBox;
use QCubed\Project\Control\Checkbox;
use QCubed\Project\Control\ListBox;
use QCubed\Project\Control\RadioButton;
use QCubed\QString;
use QCubed\TagStyler;
use QCubed\Type;

/**
 * Class ControlTrait
 *
 * Base bootstrap control trait. The preferred method of adding bootstrap functionality is to make your Control class
 * inherit from the Control class in Control.class.php. Alternatively you can use this trait to make a control a
 * bootstrap control, but you have to be careful of method collisions. The best way to do this is probably to
 * use it in a derived class of the base class.
 *
 * @package QCubed\Bootstrap
 */
trait ControlTrait
{
    /** @var string|null */
    protected ?string $strValidationState = null;
    /** @var TagStyler|null */
    protected ?TagStyler $objLabelStyler = null;
    /** @var null|string */
    protected ?string $strHorizontalClass = null;


    /**
     * Retrieves the label styler object. If it does not exist, it initializes a new instance of TagStyler,
     * assigns a default CSS class 'control-label' to it, and returns the instance.
     *
     * @return TagStyler The label styler object associated with the current instance.
     */
    public function getLabelStyler(): TagStyler
    {
        if (!$this->objLabelStyler) {
            $this->objLabelStyler = new TagStyler();
            // initialize
            $this->objLabelStyler->addCssClass('control-label');
        }
        return $this->objLabelStyler;
    }

    /**
     * Adds a specified CSS class to the associated label's style.
     *
     * @param string $strNewClass The CSS class name to be added to the label.
     *
     * @return void
     */
    public function addLabelClass(string $strNewClass): void
    {
        $this->getLabelStyler()->addCssClass($strNewClass);
    }

    /**
     * Removes a specified CSS class from the label's styling.
     *
     * @param string $strCssClassName The name of the CSS class to be removed from the label.
     *
     * @return void
     */
    public function removeLabelClass(string $strCssClassName): void
    {
        $this->getLabelStyler()->removeCssClass($strCssClassName);
    }

    /**
     * Adds a grid setting to the control.
     * Generally, you only should do this on \QCubed\Control\Panel type classes. HTML form object classes drawn with RenderFormGroup
     * should generally not be given a column class, but rather wrapped in a div with a column class setting. Or, if
     * you are trying to achieve labels next to control objects, see the directions at FormHorizontal class.
     *
     * @param string $strDeviceSize
     * @param int $intColumns
     * @param int $intOffset
     * @param int $intPush
     * @return mixed
     */
    public function addColumnClass(string $strDeviceSize, int $intColumns = 0, int $intOffset = 0, int $intPush = 0): mixed
    {
        return ($this->addCssClass(Bootstrap::createColumnClass($strDeviceSize, $intColumns, $intOffset, $intPush)));
    }

    /**
     * Adds a class to the horizontal column classes, used to define the column breaks when drawing in a
     * horizontal mode.
     *
     * @param string $strDeviceSize
     * @param int $intColumns
     * @param int $intOffset
     * @param int $intPush
     */
    public function addHorizontalColumnClass(string $strDeviceSize, int $intColumns = 0, int $intOffset = 0, int $intPush = 0): mixed
    {
        $strClass = Bootstrap::createColumnClass($strDeviceSize, $intColumns, $intOffset, $intPush);
        $blnChanged = Html::addClass($this->strHorizontalClass, $strClass);
        if ($blnChanged) {
            $this->markAsModified();
        }
    }


    /**
     * Removes column-related CSS classes from a given string based on the specified device size.
     *
     * @param string $strHaystack The string containing CSS classes to be processed.
     * @param string $strDeviceSize Optional. The device size prefix (e.g., 'sm', 'md', 'lg')
     *                              used to identify column-related classes for removal.
     *                              Default is an empty string, which removes classes for all device sizes.
     *
     * @return string The resulting string after removing the column-related CSS classes.
     */
    public static function removeColumnClasses(string $strHaystack, string $strDeviceSize = ''): string
    {
        $strTest = 'col-' . $strDeviceSize;
        $aRet = array();
        if ($strHaystack) {
            foreach (explode(' ', $strHaystack) as $strClass) {
                if (strpos($strClass, $strTest) !== 0) {
                    $aRet[] = $strClass;
                }
            }
        }
        return implode(' ', $aRet);
    }

    /**
     * Sets the width of the label column and control column for horizontal forms based on the specified device size
     * and column configuration. Adjusts the classes for labels and controls to align properly within the grid system.
     *
     * @param string $strDeviceSize The device size (e.g., 'sm', 'md', 'lg', etc.) to define the column size classes.
     * @param int $intColumns The number of grid columns allocated for the label. The remaining grid columns (12 - $intColumns)
     *                        will be allocated for the control.
     *
     * @return void
     */
    public function setHorizontalLabelColumnWidth(string $strDeviceSize, int $intColumns): void
    {
        $intCtrlCols = 12 - $intColumns;
        if ($this->Name) { // label next to control
            $this->addLabelClass(Bootstrap::createColumnClass($strDeviceSize, $intColumns));
            $this->addHorizontalColumnClass($strDeviceSize, $intCtrlCols);
        } else { // no label, so shift control to other column
            $this->addHorizontalColumnClass($strDeviceSize, 0, $intColumns);
            $this->addHorizontalColumnClass($strDeviceSize, $intCtrlCols);
        }
    }

    /**
     * Renders the form group wrapper and its contents, including the control and its associated label.
     * Applies Bootstrap classes ensuring proper styling. Handles additional features like
     * help blocks, error handling, and horizontal layout if applicable.
     *
     * @param bool $blnDisplayOutput Whether to output the rendered HTML directly or return it as a string.
     *                               Defaults to true.
     *
     * @return string|null The rendered HTML as a string if $blnDisplayOutput is false, or null if the output is
     *                     sent directly.
     */
    public function renderFormGroup(bool $blnDisplayOutput = true): ?string
    {
        if ($this instanceof TextBox ||
            $this instanceof ListBox) {
            $this->addCssClass(Bootstrap::FORM_CONTROL); // make sure certain controls get a form control class
        }

        $this->blnUseWrapper = true;    // always use wrapper, because its part of the form group
        $this->getWrapperStyler()->addCssClass(Bootstrap::FORM_GROUP);

        $this->renderHelper(func_get_args(), __FUNCTION__);

        $blnIncludeFor = false;
        // Try to automatically detect the correct value
        if ($this instanceof TextBox ||
            $this instanceof ListBox ||
            $this instanceof Checkbox) {
            $blnIncludeFor = true;
        }

        $strLabel = $this->renderLabel($blnIncludeFor) . "\n";

        try {
            $strControlHtml = $this->getControlHtml();
        } catch (Caller $objExc) {
            $objExc->incrementOffset();
            throw $objExc;
        }

        $strHtml = $this->strHtmlBefore . $strControlHtml . $this->strHtmlAfter . $this->getHelpBlock();

        if ($this->strHorizontalClass) {
            $strHtml = Html::renderTag('div', ['class' => $this->strHorizontalClass], $strHtml);
        }

        $strHtml = $strLabel . $strHtml;

        return $this->renderOutput($strHtml, $blnDisplayOutput, true);
    }

    /**
     * Renders the label for the control. This method handles label rendering logic, including special cases for Checkbox and
     * RadioButton controls by adjusting the use of the Name and Text properties as appropriate. It also applies any styling
     * from the label styler and optionally includes the "for" attribute linking the label to the control.
     *
     * @param bool|null $blnIncludeFor Whether to include the "for" attribute in the label's HTML output, linking it to the control's ID.
     *
     * @return string Returns the rendered label HTML as a string. Returns an empty string if no label is to be rendered.
     */
    public function renderLabel(?bool $blnIncludeFor = false): string
    {
        if (!$this->strName) {
            return '';
        }

        $strLabelTag = "label";

        /* Checkboxes and RadioButtons present a special problem. Bootstrap really wants these to have a label drawn after
           the checkbox, which leaves us wondering what to do with the Name. So here, we try to use the Name as the label
           if there is no Text, otherwise it will let the Text handle the label tag, and instead draw a Span for the name.
        */
        if ($this instanceof Checkbox || $this instanceof RadioButton) {
            if ($this->Text) {
                $strLabelTag = "span";  // superclass will draw the label
            }
            else {
                // Note that this is going to change how the control is drawn, so it must come before the render function
                $this->Text = $this->Name;
                $this->Name = "";   // swap
                return "";
            }
        }

        $objLabelStyler = $this->getLabelStyler();
        $attrOverrides['id'] = $this->ControlId . '_label';

        if ($blnIncludeFor) {
            $attrOverrides['for'] = $this->ControlId;
        }

        return Html::renderTag($strLabelTag, $objLabelStyler->renderHtmlAttributes($attrOverrides), QString::htmlEntities($this->strName), false, true);
    }

    /**
     * Generates and returns the HTML content for a help block. The help block
     * can display a validation error, a warning message, or instructions
     * associated with the control.
     *
     * @return string The HTML string representing the appropriate help block
     *                content based on the current state of the control.
     */
    protected function getHelpBlock(): string
    {
        $strHtml = "";
        if ($this->strValidationError) {
            $strHtml .= Html::renderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_error'], $this->strValidationError);
        } elseif ($this->strWarning) {
            $strHtml .= Html::renderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_warning'], $this->strWarning);
        } elseif ($this->strInstructions) {
            $strHtml .= Html::renderTag('p', ['class'=>'help-block', 'id'=>$this->strControlId . '_help'], $this->strInstructions);
        }
        return $strHtml;
    }


    /**
     * Returns the attributes for the control.
     * @param bool $blnIncludeCustom
     * @param bool $blnIncludeAction
     * @return string
     */
    public function renderHtmlAttributes(?array $attributeOverrides = null, ?array $styleOverrides = null): string
    {
        if ($this->strValidationError) {
            $attributeOverrides['aria-describedby'] = $this->strControlId . '_error';
        } elseif ($this->strWarning) {
            $attributeOverrides['aria-describedby'] = $this->strControlId . '_warning';
        } elseif ($this->strInstructions) {
            $attributeOverrides['aria-describedby'] = $this->strControlId . '_help';
        }

        return parent::renderHtmlAttributes($attributeOverrides, $styleOverrides);
    }

    /**
     * Resets the validation state of the current object by removing any associated
     * CSS class and clearing the validation state. Also calls the parent's
     * validationReset method to ensure proper inheritance handling.
     *
     * @return void
     */
    public function validationReset(): void
    {
        if ($this->strValidationState) {
            $this->removeWrapperCssClass($this->strValidationState);
            $this->strValidationState = null;
        }
        parent::validationReset();
    }

    /**
     * Updates the validation state of the current object by applying the appropriate
     * CSS class based on the presence of errors, warnings, or success, provided the object
     * uses a wrapper and has no child controls. This method ensures that the validation
     * state is visually represented in the interface.
     *
     * @return void
     */
    public function reinforceValidationState(): void
    {
        $objChildControls = $this->getChildControls(false);
        if ($this->blnUseWrapper &&
                count($objChildControls) == 0) {    // don't apply states to parent controls
            if ($this->strValidationError) {
                $this->addWrapperCssClass(Bootstrap::HAS_ERROR);
                $this->strValidationState = Bootstrap::HAS_ERROR;
            } elseif ($this->strWarning) {
                $this->addWrapperCssClass(Bootstrap::HAS_WARNING);
                $this->strValidationState = Bootstrap::HAS_WARNING;
            } else {
                $this->addWrapperCssClass(Bootstrap::HAS_SUCCESS);
                $this->strValidationState = Bootstrap::HAS_SUCCESS;
            }
        }
        // TODO: Classes that don't use a wrapper
    }

    /**
     * Validates the current control and all of its child controls, ensuring that any
     * associated validation states are reinforced. Initially assumes the validation
     * state is true by invoking the parent's validateControlAndChildren method and
     * subsequently reinforces the validation state for the current control.
     *
     * @return bool Returns true if the validation is successful; otherwise, false.
     */
    public function validateControlAndChildren(): bool
    {
        // Initially Assume Validation is True
        $blnToReturn = parent::validateControlAndChildren();
        /*
                // Check the Control Itself
                if (!$blnToReturn) {
                    foreach ($this->getChildControls() as $objChildControl) {
                        $objChildControl->reinforceValidationState();
                    }
                }*/
        $this->reinforceValidationState();
        return $blnToReturn;
    }

    /**
     * Retrieves the style attributes for the wrapper element, optionally considering
     * whether the element is a block-level element.
     *
     * @param bool $blnIsBlockElement Determines if the wrapper is considered a block element.
     *                                Defaults to false.
     *
     * @return string The concatenated style attributes as a string.
     */
    protected function getWrapperStyleAttributes(?bool $blnIsBlockElement = false): string
    {
        return '';
    }

    // Abstract classes to squash warnings

    /**
     * Marks the current object as modified. This method should be implemented
     * by subclasses to define specific behavior for tracking or handling
     * modification states.
     *
     * @return void
     */
    abstract public function markAsModified(): void;

    /**
     * Adds the specified CSS class to the current object.
     * This method ensures that the provided class is applied to the object
     * for styling or behavior purposes.
     *
     * @param string $strClass The CSS class to be added.
     */
    abstract public function addCssClass(string $strClass): bool;

    /**
     * Retrieves the wrapper styler associated with the current object. This method
     * must be implemented by a subclass to define the specific behavior or styling
     * wrapper to be returned.
     *
     * @return mixed The wrapper styler object or configuration used for styling purposes.
     */
    abstract public function getWrapperStyler(): ?TagStyler;

    /**
     * Magic setter method for handling property changes. This method provides
     * custom handling for specific property names and delegates to the parent
     * setter when appropriate. It also applies additional logic based on the
     * property being modified, such as adjusting CSS classes or updating
     * validation states.
     *
     * @param string $strName The name of the property being set.
     * @param mixed $mixValue The value to set for the property.
     *
     * @return void
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'ValidationError':
                parent::__set($strName, $mixValue);
                $this->reinforceValidationState();
                break;

            case 'Warning':
                parent::__set($strName, $mixValue);
                $this->reinforceValidationState();
                break;

            case "Display":
                parent::__set($strName, $mixValue);
                if ($this->blnDisplay) {
                    $this->removeWrapperCssClass(Bootstrap::HIDDEN);
                    $this->removeCssClass(Bootstrap::HIDDEN);
                } else {
                    $this->addWrapperCssClass(Bootstrap::HIDDEN);
                    $this->addCssClass(Bootstrap::HIDDEN);
                }
                break;

            case "LabelCssClass":
                $strClass = Type::cast($mixValue, Type::STRING);
                $this->getLabelStyler()->setCssClass($strClass);
                break;

            case "HorizontalClass": // for wrapping a control with a div with this class, mainly for column control on horizontal forms
                $this->strHorizontalClass = Type::cast($mixValue, Type::STRING);
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
