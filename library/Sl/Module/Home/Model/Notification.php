<?php

namespace Sl\Module\Home\Model;

class Notification extends \Sl_Model_Abstract {

    protected $_repeated;
    protected $_repeat_data;
    protected $_name;
    protected $_data;
    protected $_status;
    protected $_date_deadline;
    protected $_date_start;
    protected $_loged = false;
    
    protected $_lists = array(
        'status' => 'home_notification_status',
    );

    public function setRepeated($repeated) {
        $this->_repeated = $repeated;
        return $this;
    }

    public function setRepeatData($repeat_data) {
        $this->_repeat_data = $repeat_data;
        return $this;
    }

    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    public function setData($data) {
        $this->_data = $data;
        return $this;
    }

    public function setStatus($status) {
        $this->_status = $status;
        return $this;
    }

    public function setDateDeadline($date_deadline) {
        if ($date_deadline instanceof \DateTime) {
            $date_deadline = $date_deadline->format(self::FORMAT_TIMESTAMP);
        }
        $this->_date_deadline = $date_deadline;
        return $this;
    }

    public function setDateStart($date_start) {
        if ($date_start instanceof \DateTime) {
            $date_start = $date_start->format(self::FORMAT_TIMESTAMP);
        }
        $this->_date_start = $date_start;
        return $this;
    }

    public function getRepeated() {
        return $this->_repeated;
    }

    public function getRepeatData() {
        return $this->_repeat_data;
    }

    public function getName() {
        return $this->_name;
    }

    public function getData() {
        return $this->_data;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function getDateDeadline($as_object = false) {
        if ($as_object) {
            return \DateTime::createFromFormat(self::FORMAT_TIMESTAMP, $this->getDateDeadline());
        }
        return $this->_date_deadline;
    }

    public function getDateStart($as_object = false) {
        if ($as_object) {
            return \DateTime::createFromFormat(self::FORMAT_TIMESTAMP, $this->getDateStart());
        }
        return $this->_date_start;
    }

}
