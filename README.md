# How to Use

## Require initialize file
```php
require_once('yourpath/core/init.php');
```

## 1. DB Class

### Config your database connection credentials in `core/packages/config/Database.php`. Example:
```php
interface Database {
    public const SERVER = '127.0.0.1';
    public const USER = 'root';
    public const PASS = '';
    public const DB = 'your_db';
    public const DBH = 'mysql:hostname='.self::SERVER.';dbname='.self::DB;
}
```

#### `Select Example`
```php
$data = DB::select('users')->fetch(1)->get(); // select all fields from 'users' table (1 for fetch all, 0 or let it no parameter for normal fetch)
print_r($data);
```

#### `Select Example 2`
```php
$data = DB::select('users', 'name')->fetch(1)->get(); // select 'name' field from 'users' table
print_r($data);
```

#### `Select Example 3`
```php
$data = DB::select('users')->fetch(1)->toJSON()->get(); // return JSON data
print_r($data);
```

#### `Select with where clauses Example`
```php
$select = DB::select('users')
    ->where([
        'username' => ['=' => 'sutanlab']
    ]);

$data = $select->fetch()->get();
print_r($data);
```

#### `Select with where clauses Example 2`
```php
$select = DB::select('users')
    ->where([
        'name' => ['LIKE' => '%John%'],
        'job' => ['=' => 'Developer']
    ]);

$data = $select->fetch(1)->get();
print_r($data);
```

#### `Insert Example`
```php
try {
    $insert = DB::insert('users', [
        'name' => 'Sutan',
        'job' => 'Engineer'
    ]); // $insert will be return PDO object with transaction begins.

    $lastInsertId = $insert->lastInsertId();
    $insert->commit();
    var_dump($lastInsertId);
    var_dump($insert);
} catch (PDOException $ex) {
    // code for catching errors
    die($ex->getMessage());
}
```

#### `Update Example`
```php
try {
    $update = DB::update('users', 'userid', 2, [
        'name' => 'Pretty',
        'job' => 'Sales'
    ]); // $update will be return PDO object with transaction begins.

    $update->commit();
    var_dump($update);
} catch (PDOException $ex) {
    // code for catching errors
    die($ex->getMessage());
}
```

#### `Delete Example`
```php
try {
    $delete = DB::delete('users', 'userid', 2);

    $delete->commit();
    var_dump($delete);
} catch (PDOException $ex) {
    // code for catching errors
    die($ex->getMessage());
}
```

#### `Query builder fetch() data Example`
```php
$query = DB::query("SELECT * FROM users WHERE id = :id", [
  ':id' => 1
]);
$data = $query->fetch()->get(); // use fetch() to single fetch or fetch(1) to multi fetch
print_r($data); // return rows
```

#### `Query builder execute() Example`
```php
try {
    $query = $db->query("DELETE FROM users WHERE id = 1");
    $exec = $query->execute(); // use execute() to execute a query
    $exec->commit();
    var_dump($exec);
} catch (PDOException $ex) {
    // code for catching errors
    die($ex->getMessage());
}
```

## 2. Input Class
```php
$get = Input::get('key'); // return $_GET['key']
$post = Input::post('key'); // returns $_POST['key']
$gets = Input::get(); // returns $_GET
$posts = Input::post(); // returns $_POST
$gain = Input::gain('key'); // check if that 'key' is set in $_POST['key'] it will be return $_POST['key'] or if that 'key' is set in $_GET['key'] it will be return $_GET['key']
```

#### Other example with if statement
```php
if (Input::gain('id')) {
    $data = DB::select('users')
        ->where(['id' => ['=' => Input::gain('id')]])
        ->fetch()
        ->get();
    print_r($data);
} else {
    die('ID not setted');
}
```

* * *

Copyright © 2019 by Sutan Gading Fadhillah Nasution