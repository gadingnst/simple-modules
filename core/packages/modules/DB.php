<?php

class DB {

  public static $dbh;
  private static $query = "", $params = [], $data = [];
  
  public function __construct() { }
  
  public function __clone(){ }
  
  public function __destruct(){ self::$dbh = null; }
  
  public static function connect() {
    try {
      if (!isset(self::$dbh)) {
        self::$dbh = new PDO(Database::DBH, Database::USER, Database::PASS);
        self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      return self::$dbh;
    } catch (PDOException $e) {
      self::fatal("CONNECTION FAILED: ".$e->getMessage());
    }
    return false;
  }
  
  public static function query($query, $params = []){
    self::$query = $query;
    self::$params = $params;
    return new self;
  }
  
  public static function select($table, $fields='*', $optionalClauses = ''){
    self::$query = "SELECT {$fields} FROM {$table} {$optionalClauses}";
    return new self;
  }

  public static function insert($tabel, $fields = []){
    self::clearParams();
    $columns = implode(", ", array_keys($fields));
    foreach ($fields as $column => $value) {
      $bindKey = self::setParam($column);
      self::$params[$bindKey] = $value;
    }
    $paramStr = implode(", ", array_keys(self::$params));
    self::$query = "INSERT INTO {$tabel} ({$columns}) VALUES ({$paramStr});";
    return self::execute();
  }
  
  public static function delete($table, $column, $value){
    self::clearParams();
    self::$params[self::setParam($column)] = $value;
    self::$query = "DELETE FROM {$table} WHERE {$column} = '{$value}';";
    return self::execute();
  }
  
  public static function update($table, $column, $value, $set = []){
    $setResult = "";
    self::clearParams();
    foreach($set as $setColumn => $setValue){
      $bindKey = self::setParam($setColumn);
      self::$params[$bindKey] = $setValue;
      $setString = "{$setColumn} = {$bindKey}, ";
      $setResult .= $setString;
    }
    $setResult = self::removeLastString($setResult, ', ');
    self::$query = "UPDATE {$table} SET {$setResult} WHERE {$column} = '{$value}';";
    return self::execute();
  }

  public function where($conditions = [], $optionalClauses = ""){
    $whereClauses = "";
    $this->clearParams();
    foreach ($conditions as $column => $clause) {
      $bindKey = $this->setParam($column);
      self::$params[$bindKey] = array_values($clause)[0];
      $operator = array_keys($clause)[0];
      $condition = "{$column} {$operator} {$bindKey} AND ";
      $whereClauses .= $condition;
    }
    $whereClauses = $this->removeLastString($whereClauses, ' AND ');
    self::$query .= " WHERE {$whereClauses} {$optionalClauses}";
    return $this;
  }

  public function toJSON() {
    self::$data = json_encode(self::$data);
    return $this;
  }

  private static function execute($statement = false){
    $dbh = self::connect();
    try {
      $stmt = $dbh->prepare(self::$query);
      $dbh->beginTransaction();
      if (!empty(self::$params)) 
        $stmt->execute(self::$params);
      else
        $stmt->execute();
      if ($statement) {
        $dbh->commit();
        return $stmt;
      }
      return $dbh;
    } catch (PDOException $e) {
      $dbh->rollBack();
      throw new PDOException($e->getMessage(), $e->getCode());
    }
  }
  
  public function fetch($all = false) {
    try {
      $stmt = self::execute(1);
      self::$data = $all ? $stmt->fetchAll(PDO::FETCH_OBJ) : $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), $e->getCode());
    }
    return $this;
  }

  public function get() {
    $data = self::$data;
    if ((is_object($data) || is_array($data)) && !empty($data)) {
      return $data;
    } else if (is_string($data)) {
      if ($data !== '[]')
        return $data;
      return 'false';
    }
    return false;
  }
  
  private static function setParam($column){
    return ':'.str_replace(str_split('\'"`[] '), '', $column);
  }
  
  private static function clearParams(){
    self::$params = [];
  }
  
  private function removeLastString($string, $stringToDelete){
    return substr($string, 0, strrpos($string, $stringToDelete));
  }
  
  private static function fatal($error){
    die("<h4>{$error}</h4>");
  }

}