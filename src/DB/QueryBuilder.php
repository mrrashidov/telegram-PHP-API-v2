<?php


namespace Bot\TelegramBot\DB;


class QueryBuilder
{
    protected $table;
    protected $columns = "*";
    protected $wheres;
    protected $limit;
    protected $order_by;
    protected $save_filed;


    protected $sql = "";


    /**
     * Build sql
     */
    protected function build_select(){

        $this->sql = "SELECT :column FROM :table :where :order_by :limit";

        $this->prepare_table();
        $this->prepare_column();
        $this->prepare_where();
        $this->prepare_order_by();
        $this->prepare_limit();

    }

    /**
     * Build insert query string
     */
    protected function build_insert () {

        $this->sql = "INSERT INTO :table ( :column ) VALUES ( :values );";

        $this->prepare_table();

        $this->sql = str_replace(
            [':column', ':values'],
            [
                implode(", ", array_keys($this->save_filed)),
                "'".implode("' , '", array_values($this->save_filed))."'"
            ], $this->sql);
    }

    /**
     * @return bool|mixed
     * Build update query string
     */
    protected function build_update () {
        $this->sql = "UPDATE :table SET :col_val :where ;";
        $this->prepare_table();

        $col_val = "";
        $i = 0;
        foreach ($this->save_filed as $column => $value) {
            $col_val .= "{$column} = '{$value}'";
            $col_val .= ($i == count($this->save_filed) -1) ? "" : ", ";
            $i++;
        }

        $this->sql = str_replace(":col_val", $col_val, $this->sql);

        if ($this->wheres === null) {
            return false;
        }else {
            $this->prepare_where();
            return DB::inst()->setSql($this->sql)->execute();
        }

    }

    /**
     * @return bool|mixed
     * Build delete query string
     */
    public function build_delete () {

        $this->sql = "DELETE FROM :table :where";

        $this->prepare_table();

        if ($this->wheres === null) {
            return false;
        }else {
            $this->prepare_where();
            return DB::inst()->setSql($this->sql)->execute();
        }

    }


    /**
     * Prepare table name
     */
    protected  function prepare_table() {
        $this->sql = str_replace(":table", $this->table, $this->sql);

        $this->table;
    }

    /**
     * Prepare column
     */
    protected  function prepare_column() {
        $column =  (is_string($this->columns)) ? $this->columns : implode(', ', $this->columns);
        $this->sql = str_replace(":column", $column, $this->sql);
        $this->columns = null;
    }

    /**
     * Prepare WHERE
     */
    protected function prepare_where () {
        $where = "";
        if ($this->wheres != null) {
            $where = (is_string($this->wheres)) ? $this->wheres : implode(" ", $this->wheres);
            $where = " WHERE ".$where;
        }
        $this->sql = str_replace(":where", $where, $this->sql);

        $this->wheres = null;
    }

    /**
     * Prepare ORDER BY
     */
    protected function prepare_order_by() {
        $order_by = "";
        if ($this->order_by !== null) {
            $order_by = (is_string($this->order_by)) ? $this->order_by : implode(", ", $this->order_by);
            $order_by = " ORDER BY ".$order_by;
        }
        $this->sql = str_replace(":order_by", $order_by, $this->sql);

        $this->order_by = null;
    }

    /**
     * @return $this
     * Prepare LMIT
     */
    protected function prepare_limit() {
        $limit = "";
        if ($this->limit !== null and is_int($this->limit)) {
            $limit = " LIMIT ".$this->limit;
        }
        $this->sql = str_replace(":limit", $limit, $this->sql);

        $this->limit = null;
    }
}