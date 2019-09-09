<?php

class Input {
    public static function gain($data){
        if (isset($_POST[$data]))
            return $_POST[$data];
        else if (isset($_GET[$data]))
            return $_GET[$data];
        return false;
    }

    public static function get($data) {
        if (isset($data)) {
            if (isset($_GET[$data]))
                return $_GET[$data];
            return false;
        }
        return $_GET;
    }

    public static function post($data) {
        if (isset($data)) {
            if (isset($_POST[$data]))
                return $_POST[$data];
            return false;
        }
        return $_POST;
    }
}