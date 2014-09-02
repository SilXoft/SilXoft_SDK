<?php
namespace Sl\Module\Home\Model;

class Transaction extends \Sl_Model_Abstract {

	protected $_doc_id;
	protected $_reason_id;
	protected $_qty;
	protected $_master_relation;
	protected $_date;
    protected $_status;
    protected $_ballance;
    protected $_account_id;
    
	public function setDocId ($doc_id) {
		$this->_doc_id = $doc_id;
		return $this;
	}
	public function setReasonId ($reason_id) {
		$this->_reason_id = $reason_id;
		return $this;
	}
	public function setQty ($qty) {
		$this->_qty = $qty;
		return $this;
	}
	public function setMasterRelation ($master_relation) {
		$this->_master_relation = $master_relation;
		return $this;
	}
	public function setDate ($date) {
		$this->_date = $date;
		return $this;
	}
    public function setStatus ($status) {
        $this->_status = $status;
        return $this;
    }
    public function setBallance ($ballance) {
        $this->_ballance = $ballance;
        return $this;
    }
    public function setAccountId ($account_id) {
        $this->_account_id = $account_id;
        return $this;
    }
	public function getDocId () {
		return $this->_doc_id;
	}
	public function getReasonId () {
		return $this->_reason_id;
	}
	public function getQty () {
		return $this->_qty;
	}
	public function getMasterRelation () {
		return $this->_master_relation;
	}
	public function getDate () {
		return $this->_date;
	}
    public function getAccountId () {
        return $this->_account_id;
    }
    public function getStatus () {
        return $this->_status;
    }
    public function getBallance () {
        return $this->_ballance;
    }

}