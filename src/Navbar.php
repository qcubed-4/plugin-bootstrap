<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Js;
use QCubed\Type;
use QCubed as Q;

/**
 * Class Navbar
 *
 * A control that implements a Bootstrap Navbar
 * The "HeaderHtml" attribute will be used as the header text, and the child controls will be used as the
 * "collapse" area. To render an image in the header, set the "HeaderHtml" attribute to the image html.
 *
 * Usage: Create a Navbar object, and add a NavbarList for drop down menus, adding a NavbarItem to the list for each
 *          item in the list. You can also add NavbarItems directly to the Navbar object for a link in the navbar.
 *
 * @property string $ContainerClass
 * @property string $HeaderText
 * @property string $HeaderAnchor
 * @property string $Value
 * @property string $SelectedId
 * @property string $StyleClass
 *
 * @package QCubed\Bootstrap
 */
class Navbar extends ControlBase
{
    protected string $strHeaderAnchor;
    protected string $strHeaderText;
    protected string $strCssClass = 'navbar navbar-default';


    protected string $strStyleClass = 'navbar-default';
    protected string $strContainerClass = Bootstrap::CONTAINER_FLUID;
    protected string $strSelectedId;

    /**
     * Constructor for initializing the class.
     *
     * @param ControlBase|FormBase $objParent The parent control or form instance.
     * @param string|null $strControlId Optional control ID for identifying the instance.
     *
     * @return void
     */
    public function __construct(ControlBase|FormBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);

        $this->addCssFile(QCUBED_BOOTSTRAP_CSS);
        Bootstrap::loadJS($this);
    }

    /**
     * Validates the current instance or context.
     *
     * @return bool Returns true if validation passes.
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Parses the post data and processes it accordingly.
     *
     * @return void
     */
    public function parsePostData(): void
    {
    }

    /**
     * @return string
     */
    protected function getControlHtml(): string
    {
        $strChildControlHtml = $this->renderChildren(false);

        $strHeaderText = '';
        if ($this->strHeaderText) {
            $strAnchor = 'href="#"';
            if ($this->strHeaderAnchor) {
                $strAnchor = 'href="' . $this->strHeaderAnchor . '"';
            }
            $strHeaderText = '<a class="navbar-brand" ' . $strAnchor . '>' . $this->strHeaderText . '</a>';
        }

        $strHtml = <<<TMPL
<div class="$this->strContainerClass">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#{$this->strControlId}_collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		 </button>

		$strHeaderText

	</div>
	<div class="collapse navbar-collapse" id="{$this->strControlId}_collapse">
		$strChildControlHtml
	</div>
</div>
TMPL;

        return $this->renderTag('nav', ['role' => 'navigation'], null, $strHtml);
    }

    /**
     * Prepares the jQuery widget for the control and attaches custom behavior.
     *
     * @return void
     */
    protected function makeJqWidget(): void
    {
        parent::makeJqWidget();
        Application::executeControlCommand(
            $this->ControlId, 'on', 'click', 'li',
            new Js\Closure("qcubed.recordControlModification ('{$this->ControlId}', 'SelectedId', this.id); jQuery(this).trigger ('bsmenubarselect', {id: this.id, value: jQuery(this).data('value')})"),
            Application::PRIORITY_HIGH);
    }


    /**
     * Magic method to retrieve the value of a property dynamically.
     *
     * @param string $strText The name of the property to retrieve.
     *
     * @return mixed The value of the requested property, or processes inheritance if property is not defined.
     *
     * @throws Caller When the property does not exist or cannot be resolved.
     */
    public function __get(string $strText): mixed
    {
        switch ($strText) {
            case "ContainerClass":
                return $this->strContainerClass;
            case "HeaderText":
                return $this->strHeaderText;
            case "HeaderAnchor":
                return $this->strHeaderAnchor;
            case "Value":
            case "SelectedId":
                return $this->strSelectedId;
            case "StyleClass":
                return $this->strStyleClass;

            default:
                try {
                    return parent::__get($strText);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    /**
     * Overrides the magic __set method to handle setting of specific properties.
     *
     * @param string $strText The name of the property to set.
     * @param mixed $mixValue The value to assign to the property.
     *
     * @return void
     * @throws InvalidCast Thrown when the value cannot be properly cast to the expected type.
     * @throws Caller Thrown when the property does not exist in the parent class.
     */
    public function __set(string $strText, mixed $mixValue): void
    {
        switch ($strText) {
            case "ContainerClass":
                try {
                    // Bootstrap::ContainerFluid or Bootstrap::Container
                    $this->strContainerClass = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HeaderText":
                try {
                    $this->strHeaderText = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "HeaderAnchor":
                try {
                    $this->strHeaderAnchor = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "Value":
            case "SelectedId":
                try {
                    $this->strSelectedId = Type::cast($mixValue, Type::STRING);
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            case "StyleClass":
                try {
                    $mixValue = Type::cast($mixValue, Type::STRING);
                    $this->removeCssClass($this->strStyleClass);
                    $this->addCssClass($mixValue);
                    $this->strStyleClass = $mixValue;
                    break;
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }


            default:
                try {
                    parent::__set($strText, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}



/*
 *
 * Custom Navbar Creation Code for BS v3
 *
 *
 *
 @bgDefault      : #9b59b6;
@bgHighlight    : #8e44ad;
@colDefault     : #ecf0f1;
@colHighlight   : #ecdbff;
.navbar-XXX {
    background-color: @bgDefault;
    border-color: @bgHighlight;
    .navbar-brand {
        color: @colDefault;
        &:hover, &:focus {
            color: @colHighlight; }}
    .navbar-text {
        color: @colDefault; }
    .navbar-nav {
        > li {
            > a {
                color: @colDefault;
                &:hover,  &:focus {
                    color: @colHighlight; }}}
        > .active {
            > a, > a:hover, > a:focus {
                color: @colHighlight;
                background-color: @bgHighlight; }}
        > .open {
            > a, > a:hover, > a:focus {
                color: @colHighlight;
                background-color: @bgHighlight; }}}
    .navbar-toggle {
        border-color: @bgHighlight;
        &:hover, &:focus {
            background-color: @bgHighlight; }
        .icon-bar {
            background-color: @colDefault; }}
    .navbar-collapse,
    .navbar-form {
        border-color: @colDefault; }
    .navbar-link {
        color: @colDefault;
        &:hover {
            color: @colHighlight; }}}
@media (max-width: 767px) {
    .navbar-default .navbar-nav .open .dropdown-menu {
        > li > a {
            color: @colDefault;
            &:hover, &:focus {
                color: @colHighlight; }}
        > .active {
            > a, > a:hover, > a:focus, {
                color: @colHighlight;
                background-color: @bgHighlight; }}}
}
 */
