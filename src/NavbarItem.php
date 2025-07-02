<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\HListItem;
use QCubed\Html;
use QCubed\QString;

/**
 * Class NavbarItem
 * An item to add to the navbar list.
 * @package QCubed\Bootstrap
 */
class NavbarItem extends HListItem
{
    /**
     * Constructor method for initializing the object with specified parameters.
     *
     * @param string $strText Optional text parameter, default is an empty string.
     * @param string|null $strValue Optional value parameter, default is null.
     * @param mixed $strAnchor Optional anchor parameter, default is set to '#' if not provided.
     *
     * @return void
     */
    public function __construct(string $strText = '', ?string $strValue = null, mixed $strAnchor = null)
    {
        parent::__construct($strText, $strValue);
        if ($strAnchor) {
            $this->strAnchor = $strAnchor;
        } else {
            $this->strAnchor = '#'; // need a default for attaching clicks and correct styling.
        }
    }

    /**
     * Retrieves the formatted text representation of the item.
     *
     * @return string The item's text, optionally wrapped in an anchor tag if an anchor value is set.
     */
    public function getItemText(): string
    {
        $strHtml = QString::htmlEntities($this->strName);

        if ($strAnchor = $this->strAnchor) {
            $strHtml = Html::renderTag('a', ['href' => $strAnchor], $strHtml, false, true);
        }
        return $strHtml;
    }

    /**
     * Retrieves the attributes of a subtag.
     *
     * @return array|string|null Returns an array of attributes, a string, or null if no attributes are available.
     */
    public function getSubTagAttributes(): array|string|null
    {
        return null;
    }
}
