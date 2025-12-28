<?php
require_once __DIR__ . '/../../config/config.php';

class SetupRoutes {
    
    public function registerRoutes() {
        
        Flight::route('GET /setup-db', function() {
            try {
                $pdo = Database::connect();
                $driver = Database::getDriver();
                
                // Check if tables exist
                if ($driver === 'pgsql') {
                    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'users'");
                } else {
                    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                }
                
                $tableExists = $stmt->fetch();
                
                if ($tableExists) {
                    Flight::json([
                        'success' => true,
                        'message' => 'Database already initialized',
                        'driver' => $driver
                    ]);
                    return;
                }
                
                // Init Database
                $sqlFile = __DIR__ . '/../../event-manager-postgres.sql';
                if (!file_exists($sqlFile)) {
                     Flight::json(['error' => 'SQL file not found'], 500);
                     return;
                }
                
                $sql = file_get_contents($sqlFile);
                
                // Split by semicolon and run commands (basic splitter, safe enough for this schema)
                // Actually, PDO::exec can handle multiple statements in some drivers, but safer to run raw.
                // Postgres typically handles big blocks fine.
                $pdo->exec($sql);
                
                Flight::json([
                    'success' => true, 
                    'message' => 'Database initialized successfully',
                    'tables_created' => true
                ]);

            } catch (Exception $e) {
                Flight::json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });
        
        // Diagnostic route
        Flight::route('GET /diagnostic', function() {
            $status = [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'],
                'db_connected' => false,
                'db_error' => null
            ];
            
            try {
                $pdo = Database::connect();
                $status['db_connected'] = true;
                $status['db_driver'] = Database::getDriver();
            } catch (Exception $e) {
                $status['db_error'] = $e->getMessage();
            }
            
            Flight::json($status);
        });
    }
}
?>
