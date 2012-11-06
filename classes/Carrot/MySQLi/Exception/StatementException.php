<?php

namespace Carrot\MySQLi\Exception;

use RuntimeException;

/**
 * Exception generik yang dilempar oleh kelas Statement.
 * 
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class StatementException extends RuntimeException
{
    /**
     * @var string Pesan error dari MySQLi.
     */
    protected $mysqlErrorMessage;
    
    /**
     * @var string Kode SQLSTATE dari MySQLi. 
     */
    protected $sqlstate;
    
    /**
     * @var string Query yang tidak bisa dijalankan.
     */
    protected $query;
    
    /**
     * @var array Parameter yang digunakan untuk mengeksekusi
     *      statement.
     */
    protected $params;
    
    /**
     * Konstruktor.
     * 
     * Karena ini merupakan kelas Exception untuk kesalahan dalam
     * eksekusi statement, maka dalam konstruksi objek informasi-
     * informasi penting seperti query yang dijalankan, kode error,
     * pesan error, SQLSTATE harus di-inject.
     * 
     * @param string $message
     * @param string $mysqlErrorCode
     * @param string $mysqlErrorMessage
     * @param string $sqlstate
     * @param string $query
     * @param array $params
     *
     */
    public function __construct(
        $message,
        $mysqlErrorCode,
        $mysqlErrorMessage,
        $sqlstate,
        $query,
        array $params
    )
    {
        parent::__construct($message, $mysqlErrorCode);
        $this->mysqlErrorMessage = $mysqlErrorMessage;
        $this->sqlstate = $sqlstate;
        $this->query = $query;
        $this->params = $params;
        $this->initialize();
    }
    
    /**
     * Mengambil pesan error dari MySQLi saat terjadi kesalahan.
     * 
     * @return string
     *
     */
    public function getMysqlErrorMessage()
    {
        return $this->mysqlErrorMessage;
    }
    
    /**
     * Mengambil status SQLSTATE dari MySQLi saat terjadi kesalahan.
     * 
     * @return string
     *
     */
    public function getSqlstate()
    {
        return $this->sqlstate;
    }
    
    /**
     * Mengambil string query yang menyebabkan kesalahan.
     * 
     * @return string
     *
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * Mengambil nilai-nilai yang digunakan untuk mengeksekusi query.
     * 
     * @return array
     *
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Metode ini akan dipanggil oleh {@see __construct()}, untuk
     * dioverride oleh kelas anak.
     *
     */
    protected function initialize()
    {
        
    }
}