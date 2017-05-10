<?php
/**
 * SessionAccessTrait
 *
 * @package     erdiko/authenticate/traits
 * @copyright   Copyright (c) 2016, Arroyo Labs, http://www.arroyolabs.com
 * @author      Leo Daidone, leo@arroyolabs.com
 */

namespace erdiko\authorize\traits;


trait SessionAccessTrait
{
    public static function startSession()
    {
        if (version_compare(phpversion(), '5.4.0', '<')) {
            if(session_id() == '') {
                @session_start();
            }
        }
        else
        {
            if (session_status() == PHP_SESSION_NONE) {
                @session_start();
            }
        }
    }
}