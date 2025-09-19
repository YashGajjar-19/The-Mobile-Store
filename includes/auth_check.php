<?php
// This script should be included at the top of pages you want to protect
// or on pages where you want to auto-login a user.

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

// If user is not logged in, check for a "Remember Me" cookie

