<?php

/**
 * Description of MySqlHelper
 *
 * @author Safaa AlNabulsi
 */
class Database
{

    private $host;
    private $user;
    private $pass;
    private $name;
    private $link;

    function __construct($host, $user, $pass, $name = "", $conn = 1)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        if (!empty($name)) {
            $this->name = $name;
        }
        if ($conn == 1) {
            $this->connect();
        }
    }

    function __destruct()
    {
        @mysql_close($this->link);
    }

    /**
     * connect to database
     */
    public function connect()
    {
        $this->link = mysql_connect($this->host, $this->user, $this->pass) or die("Could not create database connection!");
        if ($this->link) {
            if (!empty($this->name)) {
                if (!mysql_select_db($this->name)) {
                    $this->printMessage('Could not connect to the database: ' . $this->name);
                }
            }
        }
    }

    /**
     * Close the connection with database 
     */
    public function close()
    {
        @mysql_close($this->link);
    }

    /**
     * print Message 
     */
    private function printMessage($message)
    {
        echo "<pre>" . $message . "</pre>";
    }

    /**
     * Check if the table exists or not
     * @param string $table name of the table
     * @return boolean
     */
    private function tableExists($table)
    {
        $tablesInDb = @mysql_query('SHOW TABLES FROM ' . $this->name . ' LIKE "' . $table . '"');
        if ($tablesInDb) {
            if (mysql_num_rows($tablesInDb) == 1) {
                return true;
            } else {
                $this->printMessage('Table ' . $table . ' does not exist: ');
                return false;
            }
        }
    }

    /**
     * Execute the given query
     * @param string $sql
     * @return boolean|resource
     */
    private function query($sql)
    {
        if ($query = @mysql_query($sql)) {
            return $query;
        } else {
            $this->printMessage('something went wrong with the given query!');
            return false;
        }
    }

    /**
     * Fetches the data into an array
     * @param resource $query resource from mysql
     * @return array holds the result of the excuted query
     */
    private function fetchIntoArray($query)
    {
        $numResults = mysql_num_rows($query);
        for ($i = 0; $i < $numResults; $i++) {
            $r = mysql_fetch_array($query);
            $key = array_keys($r);
            for ($x = 0; $x < count($key); $x++) {
                // Sanitizes keys so only alphavalues are allowed
                if (!is_int($key[$x])) {
                    if (mysql_num_rows($query) > 1)
                        $result[$i][$key[$x]] = $r[$key[$x]];
                    else if (mysql_num_rows($query) < 1)
                        $result = null;
                    else
                        $result[$key[$x]] = $r[$key[$x]];
                }
            }
        }
        return $result;
    }

    /**
     * Get the Where condition as a string wether it was array or simple string
     * @param string|array $where
     * @return string|array
     */
    private function getWhereCondition($where)
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        return $where;
    }

    /**
     * Select from mysql database
     * @param string $table name of the table
     * @param string $columns names of the columns we want to query
     * @param string $where condition
     * @param string $order order by
     * @return boolean|array holds the result of the excuted query
     */
    public function select($table, $columns = '*', $where = null, $order = null)
    {
        $query = 'SELECT ' . $columns . ' FROM ' . $table;
        if ($where != null) {
            $query .= ' WHERE ' . $this->getWhereCondition($where);
        }
        if ($order != null) {
            $query .= ' ORDER BY ' . $order;
        }
        if ($this->tableExists($table)) {
            $query = $this->query($query);
            if (!$query) {
                return false;
            }
            $result = $this->fetchIntoArray($query);

            return !$result ? false : $result;
        }

        return false;
    }

    /**
     * insert given values to the given table
     * @param string $table name of the table
     * @param array $rows array holds the values 
     * @param string $columns names of the columns we want to insert the value to
     * @return boolean
     */
    public function insert($table, $rows, $columns = null)
    {
        if ($this->tableExists($table)) {
            $insert = 'INSERT INTO ' . $table;
            if ($columns != null) {
                $insert .= ' (' . $columns . ')';
            }
            foreach ($rows as $row) {
                $values[] = '(' . implode(',', $row) . ')';
            }
            $values = implode(',', $values);
            $insert .= ' VALUES ' . $values . ';';
            $ins = @mysql_query($insert);
            if ($ins) {
                return true;
            } else {
                $this->printMessage('Data Insertion Failed!');
                return false;
            }
        }
    }

    /**
     * Delete from a table in the database
     * @param string $table name of the table
     * @param string $where condition
     * @return boolean
     */
    public function delete($table, $where = null)
    {
        if ($this->tableExists($table)) {
            if ($where == null) {
                $delete = 'DELETE ' . $table;
            } else {
                $delete = 'DELETE FROM ' . $table . ' WHERE ' . $this->getWhereCondition($where);
            }
            $result = $this->query($delete);
            if ($result) {
                $this->printMessage('Data has been deleted successfully!');
                return true;
            }
        }
        return false;
    }

    /**
     * Update a row in the database
     * @param string $table name of the table
     * @param string $values names of the columns we want to update its value
     * @param string|array $where condition
     * @return boolean
     */
    public function update($table, $values, $where)
    {
        if ($this->tableExists($table)) {
            $update = 'UPDATE ' . $table . ' SET ';
            $keys = array_keys($values);
            for ($i = 0; $i < count($values); $i++) {
                if (is_string($values[$keys[$i]])) {
                    $update .= $keys[$i] . '="' . $values[$keys[$i]] . '"';
                } else {
                    $update .= $keys[$i] . '=' . $values[$keys[$i]];
                }

                // Parse to add commas
                if ($i != count($values) - 1) {
                    $update .= ',';
                }
            }
            $whereCondition = $this->getWhereCondition($where);
            $update .= ' WHERE ' . $whereCondition;
            $result = $this->query($update);
            if ($result) {
                $this->printMessage('Data has been updated successfully!');
                return true;
            }
        }
        return false;
    }

}
?>
