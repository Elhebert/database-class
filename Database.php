<?php

class Database {

    private $host, $user, $passwd, $name, $db, $is_set, $is_where, $query, $bind;
    private $bindType = [
        "integer" => PDO::PARAM_INT,
        "string" => PDO::PARAM_STR,
        "boolean" => PDO::PARAM_BOOL
    ];

    /**
     * @param $host
     * @param $user
     * @param $passwd
     * @param $name
     */
    public function __construct($host, $user, $passwd, $name) {
        $this->host = $host;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->name = $name;
    }

    /**
     * @param array $options array containing the options for PDO
     * @link http://php.net/manual/en/pdo.setattribute.php
     */
    public function connect($options = []) {
        try {
            $this->db = new PDO("mysql:dbname=$this->name;host=$this->host", $this->user, $this->passwd);
            if(!empty($options))
                foreach ($options as $k => $v) {
                    $this->db->setAttribute($k, $v);
                }
        } catch (\Exception $e) {
            echo "Error : \n$e->getMessage()";
            die();
        }
    }

    /**
     * @param string $table
     * @param array $fields
     */
    public function select($table, $fields = ['*']) {
        $this->is_where = 0;

        $tmp_fields = "";
        foreach ($fields as $field)
            $tmp_fields .= "$field, ";

        $this->query = "SELECT " . trim($tmp_fields, ', ') . " FROM $table";
    }

    /**
     * @param array $conditions
     */
    public function where($conditions = []) {
        if($this->is_where == 0) {
            $this->is_where = 1;
            $this->query .= " WHERE $conditions[0]";
            array_shift($conditions);
            if(!empty($conditions))
                foreach ($conditions as $condition)
                    $this->query .= " AND $condition";
        } else {
            foreach ($conditions as $condition)
                $this->query .= " AND $condition";
        }
    }

    /**
     * @param string $table
     * @param array $fields
     */
    public function insert($table, $fields = []) {
        $this->query = "INSERT INTO $table(";

        $tmp_fields = "";
        $tmp_values = "";
        for($i = 0; isset($fields[$i]) ; $i ++)
            foreach ($fields[$i] as $k => $v) {
                $tmp_fields .= "$k, ";
                $tmp_values .= ":$k, ";
                $this->bind[] = [":$k", $v, $this->bindType[gettype($v)]];
            }
        $this->query .= trim($tmp_fields, ', ') . ') VALUES (';
        $this->query .= trim($tmp_values, ', ') . ')';
    }

    /**
     * @param string $table
     */
    public function update($table) {
        $this->is_set = 0;
        $this->query = "UPDATE $table";
    }

    /**
     * @param array $fields
     */
    public function set($fields = []) {
        if($this->is_set == 0) {
            $this->is_set = 1;
            $first_field = $fields[0];
            foreach ($first_field as $k => $v) {
                $this->query .= " SET $k=:$k";
                $this->bind[] = [":$k", $v, $this->bindType[gettype($v)]];
            }
            array_shift($fields);
            if(!empty($fields))
                for($i = 0; isset($fields[$i]) ; $i ++)
                    foreach ($fields[$i] as $k => $v) {
                        $this->query .= ", $k=:$k";
                        $this->bind[] = [":$k", $v, $this->bindType[gettype($v)]];
                    }
        } else {
            for($i = 0; isset($fields[$i]) ; $i ++)
                foreach ($fields[$i] as $k => $v) {
                    $this->query .= ", $k=:$k";
                    $this->bind[] = [":$k", $v, $this->bindType[gettype($v)]];
                }
        }
    }

    public function limit() {
        if (func_num_args() == 1)
            $this->query .= " LIMIT " . func_get_arg(0);
        if (func_num_args() == 2)
            $this->query .= " LIMIT " . func_get_arg(0) . ", " . func_get_arg(1);
    }

    /**
     * @param string $field
     */
    public function group_by($field) {
        $this->query .= " GROUP BY $field";
    }

    /**
     * @param string $field
     */
    public function order_by($field) {
        $this->query .= " ORDER BY $field";
    }

    public function execute() {
        $query = $this->db->prepare($this->query);
        if(!empty($this->bind)) {
            foreach ($this->bind as $k => $v) {
                $query->bindParam($v[0], $v[1], $v[2]);
            }
        }
        try {
            $query->execute();
            $this->reset();
            if(empty($this->is_where))
                return $query;
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function reset() {
        $this->is_set = 0;
        $this->is_where = 0;
        $this->query = "";
        $this->bind = [];
    }
}