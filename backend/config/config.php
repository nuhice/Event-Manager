<?php
class Database {
   private static $host = 'localhost';
   private static $dbName = 'event_manager';
   private static $username = 'root';
   private static $password = '';
   private static $port = '3306';
   private static $driver = 'mysql';
   private static $connection = null;
   public const JWT_SECRET = 'some_secret_key';

   private static function loadFromEnv() {
       $databaseUrl = getenv('DATABASE_URL');
       if ($databaseUrl) {
           self::parseDatabaseUrl($databaseUrl);
       } else {
           self::$host = getenv('DB_HOST') ?: self::$host;
           self::$dbName = getenv('DB_NAME') ?: self::$dbName;
           self::$username = getenv('DB_USER') ?: self::$username;
           self::$password = getenv('DB_PASS') ?: self::$password;
           self::$port = getenv('DB_PORT') ?: self::$port;
           self::$driver = getenv('DB_DRIVER') ?: self::$driver;
       }
   }

   private static function parseDatabaseUrl($url) {
       $parsed = parse_url($url);
       
       if (isset($parsed['scheme'])) {
           self::$driver = ($parsed['scheme'] === 'postgresql' || $parsed['scheme'] === 'postgres') ? 'pgsql' : 'mysql';
       }
       
       if (isset($parsed['host'])) {
           self::$host = $parsed['host'];
       }
       
       if (isset($parsed['port'])) {
           self::$port = $parsed['port'];
       }
       
       if (isset($parsed['user'])) {
           self::$username = $parsed['user'];
       }
       
       if (isset($parsed['pass'])) {
           self::$password = $parsed['pass'];
       }
       
       if (isset($parsed['path'])) {
           self::$dbName = ltrim($parsed['path'], '/');
       }
   }

   public static function connect() {
       if (self::$connection === null) {
           self::loadFromEnv();
           try {
               $dsn = self::$driver . ":host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName;
               
               self::$connection = new PDO(
                   $dsn,
                   self::$username,
                   self::$password,
                   [
                       PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                       PDO::ATTR_EMULATE_PREPARES => false
                   ]
               );
           } catch (PDOException $e) {
               error_log("Database connection failed: " . $e->getMessage());
               die("Connection failed: " . $e->getMessage());
           }
       }
       return self::$connection;
   }

   public static function getJwtSecret() {
       return getenv('JWT_SECRET') ?: self::JWT_SECRET;
   }
   
   public static function getDriver() {
       if (self::$connection === null) {
           self::loadFromEnv();
       }
       return self::$driver;
   }
}
?>