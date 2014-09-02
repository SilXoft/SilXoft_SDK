<?php
namespace Sl\Model\DbTable\Profiler;

class Log extends \Zend_Db_Profiler {

    protected $_log;

    protected $_totalElapsedTime;

    public function __construct($enabled = false) {
        parent::__construct($enabled);

        $this->_log = new \Zend_Log();
        $writer = new \Zend_Log_Writer_Stream('/tmp/db.log');
        $this->_log->addWriter($writer);
    }

    public function queryEnd($queryId) {
        $state = parent::queryEnd($queryId);

        if (!$this->getEnabled() || $state == self::IGNORED) {
            return;
        }

        $profile = $this->getQueryProfile($queryId);

        $this->_totalElapsedTime += $profile->getElapsedSecs();

        $message = "\r\nElapsed Secs: ".round($profile->getElapsedSecs(), 5)."\r\n";
        $message .= "Query: ".$profile->getQuery()."\r\n";

        $this->_log->info($message);
    }

}