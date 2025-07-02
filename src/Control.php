<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed as Q;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;

/**
 * Class Control
 *
 * Base bootstrap control. Set your Control to inherit from this control if you want bootstrap functionality
 * across all your controls.
 *
 * The implementation passes off most of its functionality to a trait. 2 reasons: You can make a single control
 * into a bootstrap control this way without having to make all your controls have the bootstrap functionality.
 *
 * The following properties come from the trait
 *
 * @property-write string $LabelCssClass Css class for the label
 * @property-write string $HorizontalClass Css class to use for horizontal display
 *
 *
 *
 * @package QCubed\Bootstrap
 */
abstract class Control extends ControlBase
{
    use ControlTrait;    // Pass off most functionality to the trait.

    /**
     * Constructor for initializing the control.
     *
     * @param ControlBase|FormBase $objParent The parent control or form to which this control belongs.
     * @param string|null $strControlId Optional control ID. If null, a unique ID is automatically generated.
     *
     * @return void
     */
    public function __construct(ControlBase|FormBase $objParent, ?string $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);

        Bootstrap::loadJS($this);
        /*

        if ($this instanceof \QCubed\Control\TextBoxBase ||
            $this instanceof \QCubed\Project\Control\ListBox) {
            $this->addCssClass(Bootstrap::FormControl);
        }
        */
    }
}
