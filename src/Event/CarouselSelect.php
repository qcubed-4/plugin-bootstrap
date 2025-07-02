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
 * Class CarouselSelect
 * Item in carousel is selected.
 *
 * @package QCubed\Bootstrap\Event
 */
class CarouselSelect extends EventBase
{
    /** Event Name */
    const EVENT_NAME = 'bscarousselect';
    const JS_RETURN_PARAM = 'ui';
}
