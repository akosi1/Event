<?php
/**
 * Redirect all traffic to Laravel's /public/index.php.
 * Safe workaround for servers that cannot change DocumentRoot.
 */

require __DIR__ . '/public/index.php';
