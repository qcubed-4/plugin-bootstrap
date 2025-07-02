<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\Label as QLabel;

/**
 * Class Label
 *
 * Converts a \QCubed\Control\Label to be drawn as a bootstrap "Static Control".
 * @package QCubed\Bootstrap
 */
class Label extends QLabel
{
    protected string $strCssClass = "form-control-static";
    protected string $strTagName = "p";
}
