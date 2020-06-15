<?php


namespace Bot\TelegramBot\DB;


/*use Monolog\Handler\StreamHandler;
use Monolog\Logger;*/
use PDO;

class DB
{
    protected static $instance;
    protected static $query;

    /**
     * @var PDO
     */
    private     $link;

//    private     $log;

    /**
     * @var string|null
     */
    protected   $sql;

    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $db_debug = true;

    /**
     * DB constructor.
     */
    public function __construct(){

//        $this->log = new Logger("db");
//        $this->log->pushHandler(new StreamHandler('storage/log/db.log'));

        $this->config = TELEGRAM_BOT_DB_CONFIG;

        if (!is_a($this->link, "PDO"))
        {
            $this->connection();
        }
    }

    /**
     * @return DB
     */
    public static function inst () : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @return PDO
     */
    private function connection () {

        try{
            $this->link = new PDO("mysql:host=". $this->config['db_host'] .";dbname=".
                $this->config['db_name'], $this->config['db_user'], $this->config['db_password']);

            if ($this->db_debug) {
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            return $this->link;
        }
        catch(PDOException $e)
        {
//          $this->log->error("Підключення до бази `$this->DBNAME` не вдалось.");
            print("Error: ".$e->getMessage());
        }
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function fetch_assoc ()
    {
        $sth =  $this->link->prepare($this->sql);
        $sth->execute();
        $result =  $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param bool $return_id
     * @return bool
     */
    public function execute ($return_id = false)
    {
        $sth = $this->link->prepare($this->sql);
        $result = $sth->execute();
        return ($return_id) ? $this->link->lastInsertId() : $result ;
    }

    /**
     * @param $sql
     * @return $this
     */
    public function setSql ($sql) {
        $this->sql = $sql;
        return $this;
    }


    private function __clone(){}
    private function __wakeup(){}
}