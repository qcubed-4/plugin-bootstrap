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
use QCubed\Control\ControlBase;
use QCubed\Control\FormBase;
use QCubed\Control\HList;
use QCubed\Exception\Caller;
use QCubed\Html;
use QCubed\Project\Application;
use QCubed\Js;

/**
 * Class Carousel
 * A control that implements a Bootstrap Carousel
 *
 * Use the BsCarousel_SelectEvent to detect a click on an item in the carousel.
 *
 * Note: Keeping track of which carousel item is showing is not currently implemented, mainly because it creates
 * unnecessary traffic between the browser and server, and not sure there is any compelling reason. Also, a redrawing of
 * the control will reset the carousel to the first item as active.
 */
class Carousel extends HList
{
    protected string $strCssClass = 'carousel slide';

    /**
     * Constructor method for initializing the object.
     *
     * @param ControlBase|FormBase $objParent The parent object to which this control belongs.
     * @param string|null $strControlId Optional control ID for this object. If not provided, a unique ID will be
     *     generated.
     *
     * @return void
     * @throws Caller
     */
    public function __construct (ControlBase|FormBase $objParent, ?string $strControlId = null) {
        parent::__construct ($objParent, $strControlId);

        //$this->addCssFile(__BOOTSTRAP_CSS__);
    }

    /**
     * Validates the current state or input of the object.
     *
     * @return bool Returns true if validation is successful, otherwise false.
     */
    public function validate(): bool
    {
        return true;
    }

    /**
     * Parses post data and processes the necessary operations related to it.
     *
     * @return void
     */
    public function parsePostData(): void {}

    /**
     * Generates the HTML content for all carousel items.
     *
     * Iterates through all child items of the carousel, ensuring they are of type CarouselItem.
     * Renders each item with specific attributes such as images, anchors, and captions
     * in the correct format and sets the first item as active.
     * Throws an exception if a child item is not a valid CarouselItem instance.
     *
     * @return string The HTML string representing all carousel items.
     *
     * @throws Caller If any child control is not an instance of CarouselItem.
     */
    protected function getItemsHtml(): string
    {
        $strHtml = '';
        $active = 'active'; // make the first one active

        foreach ($this->getAllItems() as $objItem) {
            if (!($objItem instanceof CarouselItem)) {
                throw new Caller('Carousel child controls must be CarouselItems');
            }
            else {
                $strImg = _nl(_indent(Html::renderTag('img', ['class'=>'img-responsive center-block', 'src'=>$objItem->ImageUrl, 'alt'=>$objItem->AltText], null, true), 1));
                if ($objItem->Anchor) {
                    $strImg = Html::renderTag('a', ['href'=>$objItem->Anchor], $strImg);
                }
                $strImg .= Html::renderTag('div', ['class'=>'carousel-caption'], $objItem->Text);

                $strHtml .= _indent(Html::renderTag('div', ['class'=>'item ' . $active, 'id'=>$objItem->Id], $strImg), 1);
                $active = ''; // subsequent ones are inactive on initial drawing
            }
        }
        return $strHtml;
    }

    /**
     * Generates the HTML for indicators based on the item count.
     *
     * Iterates through the items and creates HTML list elements (`<li>`) for each indicator.
     * The first indicator is marked as active, and subsequent ones are generated without the active class.
     *
     * @return string The HTML string representing the indicators.
     */
    protected function getIndicatorsHtml(): string
    {
        $strToReturn = '';
        for ($intIndex = 0; $intIndex < $this->getItemCount(); $intIndex++) {
            if ($intIndex == 0) {
                $strToReturn .= _nl(Html::renderTag('li', ['data-target'=>'#' . $this->strControlId, 'data-slide-to'=>$intIndex, 'class'=>"active"]));
            } else {
                $strToReturn .= "  " . _nl(Html::renderTag('li', ['data-target'=>'#' . $this->strControlId, 'data-slide-to'=>$intIndex]));
            }
        }
        return $strToReturn;
    }

    /**
     * Generates and returns the HTML string for the control.
     *
     * @return string The complete HTML representation of the control, including indicators, items, and navigation
     *     elements.
     * @throws Caller
     */
    public function getControlHtml(): string
    {
        $strIndicators = $this->getIndicatorsHtml();
        $strItems = $this->getItemsHtml();

        $strHtml = <<<TMPL
        <ol class="carousel-indicators">
      $strIndicators</ol>
    <div class="carousel-inner" role="listbox">
    $strItems     <a class="left carousel-control" href="#$this->strControlId" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#$this->strControlId" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a> 
    </div> 
    TMPL;

        return $this->renderTag('div', ['data-ride'=>'carousel'], null, $strHtml);
    }

    /**
     * Creates a jQuery widget for the control and binds a click event to elements with the class 'item'.
     * Executes a JavaScript closure that triggers a custom 'bscarousselect' event with the clicked item's ID.
     *
     * @return void
     */
    public function makeJqWidget(): void
    {
        Application::executeControlCommand($this->ControlId, 'on', 'click', '.item',
            new Js\Closure("jQuery(this).trigger('bscarousselect', this.id)"), ApplicationBase::PRIORITY_HIGH);
    }

}


