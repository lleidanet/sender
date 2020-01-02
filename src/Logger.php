<?php
/**
 * Logger.php
 * API PHP v4
 * @author David Tapia (c) 2018 - Lleida.net
 * @version 4.0
 * 
 */
namespace lnst;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    protected $isStream = 0;
    protected $filename;
    protected $stream;
    protected $pid;
    protected $user;
    protected $host;
    protected $objID;

    public function __construct($filename='vsms')
    {
        try {
            if (!preg_match('#\.log#', $filename)) {
                $this->filename = $filename.'.log';
            } else {
                $this->filename = $filename;
            }
            
            $this->stream = fopen($this->filename, 'a+');
            $this->isStream = 1;

            $this->host = gethostname();
            $this->pid = getmypid();
            $this->user = $this->getuser();

            $this->objID = $this->getObjectID(32);
        } catch (Exception $e) {
            $this->stream = STDERR;
        }
    }

    public function __destruct()
    {
        if ($this->isStream == 1) {
            if (!fclose($this->stream)) {
                // Unable to close log file
            }
        }
    }

    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    public function log($level, $message, array $context = [])
    {
        fwrite($this->stream, strtr(date('r').' '.$this->host.' sender['.$this->pid.']: ('.$this->user.') '.$this->objID.' '.$message, $context));
    }

    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, strtr($message, $context));
    }

    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, strtr($message, $context));
    }

    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, strtr($message, $context));
    }

    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, strtr($message, $context));
    }

    private function getObjectID($len)
    {
        if (!isset($this->objID) || $this->objID == '') {
            return substr(md5(rand(0, 999)), 0, $len);
        } else {
            return $this->objID;
        }
    }

    private function getuser(){
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $pwuid = posix_getpwuid(posix_geteuid());
            if(isset($pwuid['name'])){
                return $pwuid["name"];
            }
        }
        return getenv('USERNAME');
    }
}
