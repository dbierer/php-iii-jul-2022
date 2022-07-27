<?php
declare(strict_types=1);
namespace Middleware;
use Throwable;
use PDO;
class DbService
{
    const FIELDS   = ['date','status','amount','description','customer'];
    const STATUS   = ['all','open','cancelled','held','invoiced','complete'];
    const OK_INS   = 'SUCCESS: data inserted OK';
    const OK_DEL   = 'SUCCESS: data removed OK';
    const ERR_CUST = 'ERROR: missing or invalid customer ID';
    const ERR_EXEC = 'ERROR: unable to execute database statement';
    const ERR_STATUS = 'ERROR: status must be one of "all|open|cancelled|held|invoiced|complete"';
    const ERR_AMOUNT = 'ERROR: amount must be a non-zero value';
    const ERR_INS    = 'ERROR: unable to insert data';
    public static $pdo = NULL;
    public static function getPDO()
    {
        if (empty(self::$pdo)) {
            $dsn = 'mysql:host=localhost;dbname=' . DB_CONFIG['dbname'];
            self::$pdo = new PDO($dsn,  DB_CONFIG['dbuser'], DB_CONFIG['dbpwd']);
        }
        return self::$pdo;
    }
    /**
     * Returns list of orders
     *
     * @param int $id : if 0, return entire list
     * @return array $orders
     */
    public static function getList(int $id = 0)
    {
        $pdo = self::getPDO();
        $sql = 'SELECT * FROM orders';
        if ($id !== 0) {
            $sql .= ' WHERE id=' . $id;
        } else {
            $sql .= ' ORDER BY `date` DESC';
        }
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Removes an order
     *
     * @param int $id : ID of order to be removed
     * @return int $rowCount : 0 if operation failed; 1 otherwise
     */
    public static function remove(int $id) : int
    {
        $pdo = self::getPDO();
        $sql = 'DELETE * FROM orders WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute([$id])) {
            $msg[] = self::ERR_EXEC;
            return FALSE;
        }
        $msg[] = self::OK_DEL;
        return $stmt->rowCount();
    }
    /**
     * Inserts an order
     *
     * @param array $data : data to be inserted
     * @param array $msg  : any error or success messages (passed by ref)
     * @return int $rowCount : 0 if insert failed; > 0 otherwise
     */
    public static function insert(array $data, array &$msg = []) : int
    {
        // sanitize data
        foreach ($data as $key => $val)
            $data[$key] = strip_tags(trim($val));
        // validate data
        $pdo = self::getPDO();
        $err = 0;
        // validate customer
        if (empty($data['customer'])) {
            $msg[] = self::ERR_CUST;
            $err++;
        } else {
            // lookup customer
            $sql = 'SELECT id FROM customers WHERE id=' . (int) $data['customer'];
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            if (empty($result)) {
                $msg[] = self::ERR_CUST;
                $err++;
            }
        }
        // validate status
        if (empty($data['status']) || !in_array($data['status'], self::STATUS, TRUE)) {
            $msg[] = self::ERR_STATUS;
            $err++;
        }
        // validate status
        if (empty($data['amount'])) {
            $msg[] = self::ERR_AMOUNT;
            $err++;
        }
        if ($err) return 0;
        // additional sanitization
        $data['date'] = date('Y-m-d');
        $data['amount'] = (float) $data['amount'];
        $data['customer'] = (int) $data['customer'];
        // prepare/execute SQL
        $sql = 'INSERT INTO orders (`' . implode('`,`', self::FIELDS) . '`) ';
        $sql .= 'VALUES (:' . implode(',:', self::FIELDS) . ')';
        error_log(__METHOD__ . ':' . $sql);
        $ins = [];
        // strip out fields not in database
        foreach (self::FIELDS as $name)
            $ins[':' . $name] = $data[$name] ?? '';
        $result = 0;
        try {
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute($ins)) {
                $msg[] = self::ERR_EXEC;
            } else {
                $msg[] = ['insert_id' => $pdo->lastInsertId()];
                $result = $stmt->rowCount();
            }
        } catch (Throwable $t) {
            error_log(__METHOD__ . ':' . $t->getMessage());
            $msg[] = self::ERR_INS;
        }
        return $result;
    }
}
