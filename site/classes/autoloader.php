<?php
function classLoader($class)
{
    require_once($_SERVER['DOCUMENT_ROOT'] . '/classes/' . str_replace('\\', '/', $class . '.php'));
}

spl_autoload_register('classLoader');
?>