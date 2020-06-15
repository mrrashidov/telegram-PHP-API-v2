<?php


namespace Bot\TelegramBot\DB;


class Query extends QueryBuilder
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * Query constructor.
     */
    private function __construct(){}

    /**
     * @param string $table
     * @return Query
     */
    public static function table ($table = "") {

        if (self::$instance === null) {
            self::$instance = new self();
        }

        self::$instance->table = $table;

        return self::$instance;
    }

    /**
     * @param $column
     * @return $this
     */
    public function select ($column = "*") {
        $this->columns = (is_array($column) OR $column === "*") ? $column : func_get_args();
        return $this;
    }

    /**
     * @param array $save_filed
     * @return mixed
     */
    public function insert(Array $save_filed) {
        $this->save_filed = $save_filed;
        $this->build_insert();

        return DB::inst()->setSql($this->sql)->execute();
    }

    public function insert_id(Array $save_filed) {
        $this->save_filed = $save_filed;
        $this->build_insert();

        return DB::inst()->setSql($this->sql)->execute(true);
    }

    /**
     * @param array $update
     * @return bool|mixed
     */
    public function update (Array $update) {
        $this->save_filed = $update;
        return $this->build_update();
    }

    public function delete () {
        return $this->build_delete();
    }

    /**
     * @param $where
     * @return $this
     * Set where query. String or array.
     */
    public function where ($where) {

        #if (!isset($where[1])) $where = $where[0];

        $this->wheres = (is_array($where)) ? $where : func_get_args();
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit ($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param $order_by
     * @return $this
     * Set order by
     *
     * Like:
     * "name ASC", "email DESC"
     */
    public function order_by ($order_by) {
        $this->order_by = (is_array($order_by)) ? $order_by : func_get_args();
        return $this;
    }

    /**
     * @return mixed
     * All rows
     */
    public function all () {
        $this->build_select();

        return DB::inst()
            ->setSql($this->sql)
            ->fetch_assoc();
    }

    /**
     * @return mixed
     * One rows
     */
    public function one() {
        $this->limit(1);

        $this->build_select();

        $result = DB::inst()
            ->setSql($this->sql)
            ->fetch_assoc();

        return (isset($result[0])) ? $result[0] : [];
    }
}