<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

/**
 * Class SidebarSelect
 *
 * @package QCubed\Plugin\Event
 */
class SidebarSelect extends EventBase
{
    /** Event Name */
    const string EVENT_NAME = 'sidebarselect';
    const string JS_RETURN_PARAM = 'ui';
}
