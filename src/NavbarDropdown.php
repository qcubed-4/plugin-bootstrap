<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap;

use QCubed\Control\ListItemStyle;
use QCubed\QString;

class NavbarDropdown extends NavbarItem
{
    /**
     * Constructor method for initializing the object with a name and setting default styles.
     *
     * @param string $strName The name to initialize the object with.
     *
     * @return void
     */
    public function __construct(string $strName)
    {
        parent::__construct($strName);
        $this->objItemStyle = new ListItemStyle();
        $this->objItemStyle->setCssClass('dropdown');
    }

    /**
     * Retrieves the formatted HTML text for the item with dropdown styling and attributes.
     *
     * @return string The generated HTML string representing the item text.
     */
    public function getItemText(): string
    {
        $strHtml = QString::htmlEntities($this->strName);
        $strHtml = sprintf('<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">%s <span class="caret"></span></a>', $strHtml)  . "\n";
        return $strHtml;
    }

    /**
     * Retrieves the attributes for sub-tags.
     *
     * @return array An associative array of sub-tag attributes where keys represent attribute names and values represent their respective settings.
     */
    public function getSubTagAttributes(): array
    {
        return ['class'=>'dropdown-menu', 'role'=>'menu'];
    }
}
