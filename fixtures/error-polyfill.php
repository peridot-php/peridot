<?php

if (!interface_exists('Throwable')) {
    interface Throwable {}
}
if (!class_exists('Error')) {
    class Error extends Exception implements Throwable {}
}
