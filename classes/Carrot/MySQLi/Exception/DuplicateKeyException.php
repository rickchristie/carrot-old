<?php

namespace Carrot\MySQLi\Exception;

/**
 * Duplicate Key Exception.
 * 
 * Kelas exception yang akan dihasilkan oleh kelas Statement
 * apabila ada error MySQL nomor 1062.
 * 
 * @see http://dev.mysql.com/doc/refman/5.0/en/error-messages-server.html
 * @author  Ricky Christie <seven.rchristie@gmail.com>
 *
 */
class DuplicateKeyException extends StatementException
{
    /**
     * @var string Nama key di MySQL yang terduplikasi.
     */
    protected $duplicateKeyName = '';
    
    /**
     * @var mixed Nilai yang duplikat (sudah tercatat di database).
     */
    protected $duplicateValue;
    
    /**
     * Mengambil nama key yang duplikat.
     * 
     * @return string
     *
     */
    public function getDuplicateKeyName()
    {
        return $this->duplicateKeyName;
    }
    
    /**
     * Returns TRUE if the duplicate key name is the given name.
     * 
     * @param string $name
     * @return bool
     *
     */
    public function isKeyName($name)
    {
        return ($this->duplicateKeyName == $name);
    }
    
    /**
     * Mengambil nilai yang duplikat.
     * 
     * @return mixed
     *
     */
    public function getDuplicateValue()
    {
        return $this->duplicateValue;
    }
    
    /**
     * Metode ini akan melakukan parsing pesan error MySQL untuk
     * mencari {@see $duplicateKeyName} dan {@see $duplicateValue}.
     * 
     * @see parent::__construct()
     *
     */
    protected function initialize()
    {
        $result = preg_match_all(
            '/^Duplicate entry \'(.+)\' for key \'(.+)\'$/',
            $this->mysqlErrorMessage,
            $matches
        );
        
        if ($result >= 1)
        {
            $this->duplicateValue = $matches[1][0];
            $this->duplicateKeyName = $matches[2][0];
        }
    }
}