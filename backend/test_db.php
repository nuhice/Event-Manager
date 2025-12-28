<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/plain');

echo "--- Render Connectivity & Setup Diagnostic ---\n";

// 1. Check Environment
$dbUrl = getenv('DATABASE_URL');
if (!$dbUrl) {
    echo "ERROR: DATABASE_URL environment variable is NOT set.\n";
    exit(1);
} else {
    echo "OK: DATABASE_URL is set (length: " . strlen($dbUrl) . ")\n";
}

// 2. Parse URL
$parsed = parse_url($dbUrl);
$host = $parsed['host'] ?? 'unknown';
$port = $parsed['port'] ?? '5432';
$db   = ltrim($parsed['path'] ?? '', '/');
$user = $parsed['user'] ?? '';
$pass = $parsed['pass'] ?? '';

echo "Info: Host=$host, Port=$port, DB=$db, User=$user\n";

// 3. Attempt Connection
$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
echo "Attempting connection with DSN: $dsn\n";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "SUCCESS: Connected to PostgreSQL database!\n";
} catch (PDOException $e) {
    echo "ERROR: Connection failed: " . $e->getMessage() . "\n";
    
    // Try without SSL requirement as fallback check
    echo "Retrying without sslmode=require...\n";
    try {
        $dsnNoSsl = "pgsql:host=$host;port=$port;dbname=$db";
        $pdo = new PDO($dsnNoSsl, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "SUCCESS: Connected without SSL!\n";
    } catch (PDOException $e2) {
        echo "ERROR: Connection failed again: " . $e2->getMessage() . "\n";
        exit(1);
    }
}

// 4. Check Tables
echo "\nChecking tables...\n";
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($tables)) {
    echo "WARNING: No tables found. Database is empty.\n";
    echo "Running Initialization Script...\n";
    
    // Run SQL
    $sqlFile = __DIR__ . '/event-manager-postgres.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        try {
            $pdo->exec($sql);
            echo "SUCCESS: Database initialized with schema.\n";
        } catch (PDOException $e) {
            echo "ERROR: Failed to run SQL initialization: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ERROR: SQL file not found at $sqlFile\n";
    }
} else {
    echo "OK: Tables found: " . implode(", ", $tables) . "\n";
}

echo "\n--- Diagnostic Complete ---\n";
?>
