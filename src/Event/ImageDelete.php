<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

/**
 * Class ImageDelete
 *
 * Captures the delete event that occurs after an image is deleted.
 *
 */

class ImageDelete extends EventBase {

    const string EVENT_NAME = 'imagedelete';
    const string JS_RETURN_PARAM = 'ui';
}
