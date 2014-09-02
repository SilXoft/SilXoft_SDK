<?php
namespace Sl\Module\Customers\Model;

class Customer extends \Sl_Model_Abstract {
    
    protected $_name;
	protected $_first_name;
	protected $_last_name;
	protected $_middle_name;
    protected $_address;
    protected $_passport;
	protected $_passport_date;
	//protected $_attracts;
	protected $_skype;
	protected $_qq;
	protected $_post_code;
	protected $_is_dealer;
	protected $_notify_email = 1;
	protected $_notify_sms;
	protected $_notify_dealer;
	protected $_sender_phone;
	protected $_company_name;
	protected $_web_site;
	protected $_description;
    protected $_contact;
    protected $_status;
    protected $_lists = array('status' => 'customer_status');
    
	/*				
	protected $_lists = array(
		'attracts' => 'CustomerAttracts'
	);
	*/
	
	
	/*
    public function __toString() {
        $code_arr = $this->fetchRelated('customeridentifiercustomer');
        if (count($code_arr)){
            return current($code_arr).' '.$this->name;
        } 
        return $this->name; 
    }
    
	*/
	public function setStatus($status) {
        $this->_status = $status;
        return $this;
    }	
	public function setCompanyName($company_name) {
        $this->_company_name = $company_name;
        return $this;
    }
	
	
	public function setWebSite($web_site) {
        $this->_web_site = $web_site;
        return $this;
    }
	
	
	
	public function setPostCode($post_code) {
        $this->_post_code = $post_code;
        return $this;
    }
	
	public function setSenderPhone($sender_phone) {
        $this->_sender_phone = $sender_phone;
        return $this;
    }
	
	public function setIsDealer($is_dealer) {
        $this->_is_dealer = $is_dealer;
        return $this;
    }
	
	
	public function setNotifyDealer($notify_dealer) {
        $this->_notify_dealer = $notify_dealer;
        return $this;
    }
	

	
	public function setDescription($description) {
        $this->_description = $description;
        return $this;
    }
	
	public function setFirstName($first_name) {
        $this->_first_name = $first_name;
        return $this;
    }
	
	public function setLastName($last_name) {
        $this->_last_name = $last_name;
        return $this;
    }
	
	public function setMiddleName($middle_name) {
        $this->_middle_name = $middle_name;
        return $this;
    }
	
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }
    
	public function setSkype($skype) {
        $this->_skype = $skype;
        return $this;
    }
    
	public function setNotifyEmail($notify) {
        $this->_notify_email = $notify;
        return $this;
    }
    
	public function setNotifySms($notify) {
        $this->_notify_sms = $notify;
        return $this;
    }
	
	public function setQq($qq) {
        $this->_qq = $qq;
        return $this;
    }
    
	public function setAddress($address) {
        $this->_address = $address;
        return $this;
    }
	/*
	public function setAttracts($attracts) {
        $this->_attracts = $attracts;
        return $this;
    }
	*/
	public function setPassport($passport) {
        $this->_passport = $passport;
        return $this;
    }
	public function setPassportDate($passport_date) {
        $this->_passport_date = $passport_date;
        return $this;
    }
	
   
    public function setContact($contact) {
        $this->_contact = $contact;
        return $this;
    }
   
    public function getName() {
        return $this->_name;
    }
    public function getStatus() {
        return $this->_status;
    }    
	public function getAddress() {
        return $this->_address;
    }
	
    public function getPassport() {
        return $this->_passport;
    }
    
    public function getPassportDate() {
        return $this->_passport_date;
    }
    /*
	public function getAttracts() {
        return  $this->_attracts;
    }
  	*/
	public function getSkype() {
        return  $this->_skype;
    }
	public function getQq() {
        return  $this->_qq;
    }
	
	public function getNotifyEmail() {
        return  $this->_notify_email;
    }
	public function getNotifySms() {
        return  $this->_notify_sms;
    }
	
	public function getPostCode() {
        return  $this->_post_code;
    }
	
	public function getFirstName() {
        return  $this->_first_name;
    }
	
	public function getLastName() {
        return  $this->_last_name;
    }
	
	public function getMiddleName() {
        return  $this->_middle_name;
    }
	
	public function getNotifyDealer() {
        return $this->_notify_dealer;
    }
	
	public function getIsDealer() {
        return $this->_is_dealer;
        
    }
	
	public function getDescription() {
        return $this->_description;
    }
	
	
	public function getSenderPhone() {
        return $this->_sender_phone;
        
    }	
	
	
	public function getCompanyName() {
        return $this->_company_name;
    }
	
	
	public function getWebSite() {
        return $this->_web_site;
    }
    
    public function getContact() {
        return $this->_contact;
    }
    
    
}

