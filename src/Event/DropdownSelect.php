<?php
/**
 *
 * Part of the QCubed PHP framework.
 *
 * @license MIT
 *
 */

namespace QCubed\Bootstrap\Event;

use QCubed\Event\EventBase;

/**
 * Class DropdownSelect
 * Item in carousel is selected.
 *
 * @package QCubed\Bootstrap\Event
 */
class DropdownSelect extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'bsdropdownselect';
    const JS_RETURN_PARAM = 'ui';
}
