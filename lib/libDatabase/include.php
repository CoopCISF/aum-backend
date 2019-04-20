<?php

define("SQLITE3_MODE", 1);
define("MYSQL_MODE", 2);
define("PDO_MODE", 3);

/**
 * Base Abstract Database Wrapper
 * Contains only the mode of the database and the handler
 */
abstract class DatabaseWrapper {
    protected $mode;
    protected $handler;

    public function __construct(int $mode) {
        $this->mode = $mode;
    }

    public abstract function query(string $query);

    public function getMode() : string {
        switch ($this->mode) {
            case 1:
                return "SQLite3 Mode";
            case 2:
                return "MySQL Mode";
            default:
                return "You are not supposed to see this. Bug found";
        }
    }
    public function toString() : string {
        return "Database Wrapper : " . $this->getMode();
    }
}

class SQLite3DatabaseWrapper extends DatabaseWrapper{
    public function __construct(string $filename, array $config = []) {
        if(!class_exists('SQLite3'))
            throw new Exception("SQLite3 library not enabled.");
        
        $this->mode = 1;

        if(isset($config['read_only']) && $config['read_only']) 
            $mode = SQLITE3_OPEN_READONLY | SQLITE3_OPEN_CREATE;
        else
            $mode = (SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

        $this->handler = new SQLite3($filename, $mode);
        $this->handler->busyTimeout(5000);
    }

    public function query(string $query) {
        //When a query is not a SELECT, just do return exec
        if(stripos($query, 'SELECT') === false)
			return $this->handler->exec($query);
			
		$result = $this->handler->query($query);
			
		if($result == false)
            //return false;
            throw new Exception($this->handler->lastErrorMsg());
			
		$out = [];
			
		while($row = $result->fetchArray(SQLITE3_BOTH))
			$out[] = $row;
			
		return $out;
    }

    public function __destruct() {
        $this->handler->close();
    }
}


/**
 * Class for MySQL handling implementation
 */
class MySQLDatabaseWrapper extends DatabaseWrapper {
    public function __construct(array $config) {
        if(!function_exists('mysqli_connect'))
            throw new Exception("mysqli library not enabled.");

        $this->mode = 2;

        $this->handler = new mysqli($config['server'], $config['username'], $config['password'], $config['db_name']);
    
        if($this->handler->connect_error)
            throw new Exception("Connection failed. Follows error: " . $this->handler->connect_error);
    }

    public function query(string $query) {
        //When a query is not a SELECT, just do return exec
        if(stripos($query, 'SELECT') === false)
			return $this->handler->query($query);
			
		$result = $this->handler->query($query);
			
		if($result == false)
            return false;
		    //throw new Exception($this->handler->lastErrorMsg());

		$out = [];
			
		while($row = $result->fetch_assoc())
			$out[] = $row;
			
		return $out;
    }

    public function __destruct() {
        $this->handler->close();
    }
}

class PDODatabaseWrapper extends DatabaseWrapper {
    public function __construct(array $config) {
        try {
            $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];

            $this->handler = new PDO("{$config['db_type']}:host={$config['server']};dbname={$config['db_name']};charset=utf8", $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Error connecting to the database using PDO: " . $e->getMessage());
        }
    }

    public function query(string $query) {
        try {
            //$query = $this->handler->quote($query);
            if (strpos($query, "SELECT") !== false) {
                $result = $this->handler->query($query);
                $out = [];

                while($row = $result->fetch(PDO::FETCH_ASSOC))
                    $out[] = $row;

                return $out;
            }
            else
                $this->handler->exec($query);
        } catch (PDOException $e) {
            throw new Exception("Error in executing query '$query' : ". $e->getMessage());
        }
    }

    public function __destruct() {
        $this->handler = null;
    }
}