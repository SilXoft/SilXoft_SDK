<?php
namespace Sl\Service\Message;

abstract class Message {
    
    protected $_data;
    protected $_errors = array();
    
    /**
     * Установка данных
     * 
     * @param mixed $data
     * @return \Sl\Service\Message\Message
     */
    public function setData($data) {
        $this->_data = $data;
        return $this;
    }
    
    /**
     * Возвращает данные
     * 
     * @return mixed
     */
    public function getData() {
        return $this->_data;
    }
    
    /**
     * Возвращает ошибки
     * 
     * @return string[]
     */
    public function getErrors() {
        return $this->_errors;
    }
    
    /**
     * Добавляет ошибку
     * 
     * @param string $error
     * @return \Sl\Service\Message\Message
     */
    public function addError($error) {
        $this->_errors[] = $error;
        return $this;
    }
    
    /**
     * Добавляет ошибки
     * 
     * @param array $errors
     * @return \Sl\Service\Message\Message
     */
    public function addErrors(array $errors) {
        foreach($errors as $error) {
            $this->addError($error);
        }
        return $this;
    }
    
    /**
     * Есть ли ошибки
     * 
     * @return boolean
     */
    public function hasErrors() {
        return (count($this->getErrors()) > 0);
    }
    
    /**
     * Очитсить ошибки
     */
    public function cleanErrors() {
        $this->_errors = array();
    }
    
    /**
     * Вернуть тип
     * 
     * @return type
     */
    public function getType() {
        return $this->_type;
    }
}