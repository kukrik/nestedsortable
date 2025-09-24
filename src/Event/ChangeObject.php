<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

/**
 * Class ChangeObject
 *
 * Detects a save event that occurs when a cropped image is sent to save, and can optionally trigger another event on other objects.
 *
 */

class ChangeObject extends EventBase {

    const EVENT_NAME = 'changeobject';
    const JS_RETURN_PARAM = 'ui';
}
