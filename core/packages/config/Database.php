<?php

interface Database {
    public const SERVER = '127.0.0.1';
    public const USER = 'root';
    public const PASS = 'mysql';
    public const DB = 'lkkpc';
    public const DBH = 'mysql:hostname='.self::SERVER.';dbname='.self::DB;
}