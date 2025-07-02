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
use QCubed\Control\PaginatedControl;
use QCubed\Control\PaginatorBase;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Control\Paginator;
use QCubed\Html;
use QCubed\Type;

/**
 * Class Pager
 *
 * A simple bootstrap paginator that works more like a pager than a paginator. Shows next and previous arrows, and
 * a page number.
 *
 * We use the pagination class rather than the pager class, because the bootstrap pager has some issues with vertical alignment when
 * using the previous and next classes. The pagination class gives a more pleasing presentation.
 *
 * @property bool $AddArrow add the arrow to the previous and next buttons
 * @property bool $Spread Spread the buttons out rather than bunch them
 * @property-write int $Size One of SMALL, MEDIUM or LARGE to specify how to draw the paginator buttons
 * @package QCubed\Bootstrap
 */
class Pager extends Paginator
{
    /** @var bool Add an arrow to the previous and next buttons */
    protected ?bool $blnAddArrow = false;
    /** @var bool Set the buttons to the left and right side of the parent object, vs. bunched in the middle */
    protected bool $blnSpread = true;

    /** @var int  */
    protected int $intSize = self::MEDIUM;

    protected string $strLabelForPrevious;
    protected string $strLabelForNext;

    // BEHAVIOR
    /** @var int Default number of items per page */
    protected int $intItemsPerPage = 5;

    const SMALL = 1;
    const MEDIUM = 2;
    const LARGE = 3;

    /**
     * Constructor for the control.
     *
     * @param ControlBase|FormBase $objParent The parent control or form to which this control belongs.
     * @param string|null $strControlId The optional control ID for this control.
     *
     * @return void
     */
    public function __construct(ControlBase|FormBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);

        // Default to a very compat format.
        $this->strLabelForPrevious = '&laquo;';
        $this->strLabelForNext = '&raquo;';
    }

    /**
     * Generates the HTML for the "previous" pagination buttons.
     *
     * This method creates the HTML structure for the "previous" button in a pagination control.
     * It adjusts the button class and label based on the current page number, rendering a disabled button if
     * the current page is the first page. Arrow icons can be optionally added to the label.
     *
     * @return string The HTML string representing the "previous" button wrapped in a list item.
     */
    protected function getPreviousButtonsHtml(): string
    {
        $strClasses = "";
        if ($this->blnSpread) {
            $strClasses = "previous";
        }
        $strLabel = $this->strLabelForPrevious;
        if ($this->blnAddArrow) {
            $strLabel = '<span aria-hidden="true">&larr;</span> ' . $strLabel;
        }
        if ($this->intPageNumber <= 1) {
            $strButton = Html::renderTag("a", ["href"=>"#"], $strLabel);
            $strClasses .= " disabled";
        } else {
            $this->mixActionParameter = $this->intPageNumber - 1;
            $strButton = $this->prxPagination->renderAsLink($strLabel, $this->mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
        }

        return Html::renderTag("li", ["class"=>$strClasses], $strButton);
    }

    /**
     * Generates the HTML for "Next" pagination buttons.
     *
     * This function constructs the HTML for the "Next" buttons in a pagination control,
     * applying appropriate classes and labels based on the current page and other settings.
     *
     * @return string The HTML content for the "Next" pagination button as a string.
     */
    protected function getNextButtonsHtml(): string
    {
        $strClasses = "";
        if ($this->blnSpread) {
            $strClasses = "next";
        }
        $strLabel = $this->strLabelForNext;
        if ($this->blnAddArrow) {
            $strLabel = $strLabel . ' <span aria-hidden="true">&rarr;</span>' ;
        }
        if ($this->intPageNumber >= $this->PageCount) {
            $strButton = Html::renderTag("a", ["href"=>"#"], $strLabel);
            $strClasses .= " disabled";
        } else {
            $this->mixActionParameter = $this->intPageNumber + 1;
            $strButton = $this->prxPagination->renderAsLink($strLabel, $this->mixActionParameter, ['id'=>$this->ControlId . "_arrow_" . $this->mixActionParameter], "a", false);
        }

        return Html::renderTag("li", ["class"=>$strClasses], $strButton);
    }

    /**
     * Generates and returns the HTML for the control, including pagination elements.
     *
     * @return string The generated HTML string for the control.
     */
    public function getControlHtml(): string
    {
        $this->objPaginatedControl->dataBind();

        $strPager = $this->getPreviousButtonsHtml();
        $strLabel = Html::renderTag("a", ["href"=>"#"], $this->intPageNumber . ' ' .  t("of") . ' ' . $this->PageCount);
        $strPager .= Html::renderTag("li", ["class"=>"disabled"], $strLabel);
        $strPager .= $this->getNextButtonsHtml();
        $strClass = "pagination";
        if ($this->intSize == self::SMALL) {
            $strClass .= " pagination-sm";
        } elseif ($this->intSize == self::LARGE) {
            $strClass .= " pagination-lg";
        }
        $strPager = Html::renderTag("ul", ["class"=>$strClass], $strPager);

        return Html::renderTag("nav", $this->renderHtmlAttributes(), $strPager);
    }

    /**
     * Magic method to retrieve the value of a property.
     *
     * @param string $strName The name of the property to retrieve.
     *
     * @return mixed The value of the property.
     * @throws Caller If the property is not found.
     */
    public function __get(string $strName): mixed
    {
        switch ($strName) {
            case 'AddArrow': return $this->blnAddArrow;
            case 'Spread': return $this->blnSpread;
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
     * Magic method to set the value of a property.
     *
     * @param string $strName The name of the property to set.
     * @param mixed $mixValue The value to assign to the specified property.
     *
     * @return void
     * @throws Caller If an invalid value is provided or the property does not exist.
     */
    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'AddArrow':
                try {
                    $this->blnAddArrow = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Spread':
                try {
                    $this->blnSpread = Type::cast($mixValue, Type::BOOLEAN);
                    break;
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
            case 'Size':
                try {
                    $this->intSize = Type::cast($mixValue, Type::INTEGER);
                    break;
                } catch (Caller $objExc) {
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
