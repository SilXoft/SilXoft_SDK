<?php

namespace Sl\Model\Identity;

use Sl\Model\Identity\Field\Context;

class Field {

    const EC_ALREADY_EXISTS = 1001;

    /**
     * Имя поля
     * 
     * @var string 
     */
    protected $_name;

    /**
     * Тип поля
     * 
     * @var type 
     */
    protected $_type = 'text';

    /**
     * Название поля
     * 
     * @var string
     */
    protected $_label;

    /**
     * Дополнительные опции
     * 
     * @var type 
     */
    protected $_options = array();

    /**
     * Контекст поля
     * 
     * @var \Sl\Model\Identity\Field\Context
     */
    protected $_context;

    /**
     * Набор, на который опирается поле
     * 
     * @var \Sl_Model_Abstract
     */
    protected $_fieldset;

    /**
     * Роли поля
     * 
     * @var array
     */
    protected $_roles = array();

    /**
     * Признак блокировки поля по правам
     * 
     * @var type 
     */
    protected $_blocked = false;

    /**
     * Роли, назначение которых блокируется, если нет прав на это поле
     * 
     * @var type 
     */
    protected $_blocked_roles = array(
        'render',
    );
    protected $_expandable_options = array(
        'roles',
    );

    /**
     * Поддерживаемые сравнения
     * 
     * @var array 
     */
    protected static $_supported_comparisons = array(
        'eq' => array(
            0 => 'eq',
            'n' => 'neq'
        ),
        'in' => array(
            0 => 'in',
            'n' => 'nin'
        ),
    );

    public function __construct($name, $context = null, array $options = array()) {
        $this->setName($name);
        if (!is_null($context)) {
            $this->setContext(Context\Factory::build($context));
        }
        $this->fill($options);
    }

    /**
     * Устанавливает имя поля
     * 
     * @param string $name
     * @return \Sl\Model\Identity\Field
     */
    public function setName($name) {
        $this->_name = $name;
        return $this;
    }

    /**
     * Установка названия
     * 
     * @param string $label
     * @return \Sl\Model\Identity\Field
     */
    public function setLabel($label) {
        $this->_label = $label;
        return $this;
    }

    /**
     * Устанавливает тип поля
     * 
     * @param string $type
     * @return \Sl\Model\Identity\Field
     */
    public function setType($type) {
        $this->_type = $type;
        return $this;
    }

    /**
     * Установка контекста
     * 
     * @param \Sl\Model\Identity\Field\Context $context
     * @return \Sl\Model\Identity\Field
     */
    public function setContext(Context $context) {
        $this->_context = $context;
        return $this;
    }

    /**
     * Установка текущего набора
     * 
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return \Sl\Model\Identity\Field
     */
    public function setFieldset(Fieldset $fieldset) {
        if ($this->getFieldset()) {
            throw new \Exception('Can\'t chage fieldset. ' . __METHOD__);
        }
        $this->_fieldset = $fieldset;
        return $this;
    }

    public function block() {
        $this->_blocked = true;
        return $this;
    }

    public function isBlocked() {
        return $this->_blocked;
    }

    /**
     * Возвращает имя поля
     * 
     * @return type
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Возвращает название поля
     * 
     * @return string
     */
    public function getLabel() {
        return $this->_label;
    }

    /**
     * Возвращает тип поля
     * 
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Возвращает контекст
     * 
     * @return type
     */
    public function getContext() {
        return $this->_context;
    }

    /**
     * Возвращает набор
     * 
     * @return type
     */
    public function getFieldset() {
        return $this->_fieldset;
    }

    /**
     * Проксирование запросов к контексту
     * 
     * @param string $name
     * @param mixed $arguments
     * @return \Sl\Model\Identity\Field
     */
    public function __call($name, $arguments) {

        if ($this->getContext()) {
            if (method_exists($this->getContext(), $name)) {
                $result = call_user_func_array(array($this->getContext(), $name), $arguments);
                if ($result && (is_object($result)) && ($this->getContext() instanceof $result)) {
                    return $this;
                }
                return $result;
            }
        }

        // Определяем ролевые действия
        try {
            $helper = Field\Helper\Factory::build($name, $this);
            return call_user_func_array(array($helper, $name), $arguments);
        } catch (\Exception $e) {
            if ($e->getCode() != Field\Helper\Factory::EC_BUILD_ERROR) {
                throw $e;
            }
        }
    }

    /**
     * Возвращает модель
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->getFieldset()->getModel();
    }

    public function getContextType() {
        return $this->getFieldset()->getContextType();
    }

    public function cleanName() {
        return preg_replace('/^(.+\.)?([^\.]+)$/', '$2', $this->getName());
    }

    /**
     * Наполняет поле данными
     * 
     * @param array $data
     * @return \Sl\Model\Identity\Field
     */
    public function fill(array $data = array(), $force = false) {
        foreach ($data as $k => $v) {
            if (in_array($k, $this->_expandable_options)) {
                $method_name = \Sl_Model_Abstract::buildMethodName($k, 'add');
            } else {
                $method_name = \Sl_Model_Abstract::buildMethodName($k, 'set');
            }
            try {
                $this->$method_name($v);
            } catch (\Exception $e) {
                
            }
            try {
                $this->addOption($k, $v, $force);
            } catch(\Exception $e) {
                
            }
        }
        return $this;
    }

    /**
     * Добавляет роль
     * 
     * @param string $role
     * @return \Sl\Model\Identity\Field
     * @throws \Exception
     */
    public function addRole($role) {
        $this->_roles[] = strval($role);
        return $this;
    }

    /**
     * Добавляет роли
     * @see addRole()
     * 
     * @param array $roles
     * @return \Sl\Model\Identity\Field
     */
    public function addRoles(array $roles = array()) {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }

    public function setRoles(array $roles = array()) {
        $this->_roles = array();
        return $this->addRoles($roles);
    }

    /**
     * Возвращает массив ролей поля
     * 
     * @return array
     */
    public function getRoles() {
        $this->_roles = array_unique($this->_roles);
        if ($this->isBlocked()) {
            return array_values(array_diff($this->_roles, $this->_blocked_roles));
        }
        return $this->_roles;
    }

    /**
     * Проверка поля на принадлежность роли(ям)
     * 
     * @param mixed $role Роль(и). <b>Возможные значения</b>:
     * <ul>
     * <li>'rolename' - одна из ролей поля равна 'rolename',</li>
     * <li>array('firstrolename', 'secrolename') - Поле принадлежит ко всем полям списка,</li>
     * </ul>
     * @return bool
     */
    public function hasRole($role) {
        if (is_string($role)) {
            return (false !== array_search($role, $this->getRoles()));
        } elseif (is_array($role)) {
            return count(array_diff($role, $this->getRoles())) > 0;
        } else {
            throw new \Exception('Wrong $role parametr. ' . __METHOD__);
        }
    }

    public function isRelated() {
        return ($this->getName() !== $this->cleanName());
    }

    public function relationAlias() {
        if (!$this->isRelated()) {
            return false;
        }
        return preg_replace('/^(.+)\.' . $this->cleanName() . '$/', '$1', $this->getName());
    }

    /**
     * Возвращает поле приведенное к string-у
     * 
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * Возвращает список поддерживаемых сравнений
     * 
     * @return array
     */
    public function getSuppotedComparisons() {
        return static::$_supported_comparisons;
    }

    /**
     * Возвращает список поддерживаемых сравнений
     * 
     * @return array
     */
    public static function getSupportedcomarisons() {
        return static::$_supported_comparisons;
    }

    public function cleanOptions() {
        $this->_options = array();
        return $this;
    }

    public function addOption($name, $value, $force = false) {
        if (isset($this->_options[$name]) && !$force) {
            throw new \Exception('Key already set. ' . __METHOD__, self::EC_ALREADY_EXISTS);
        }
        $this->_options[$name] = $value;
        return $this;
    }

    public function setOption($name, $value) {
        return $this->addOption($name, $value, true);
    }

    public function addOptopns(array $options) {
        foreach ($options as $name => $value) {
            try {
                $this->addOption($name, $value);
            } catch (\Exception $e) {
                if ($e->getCode() != self::EC_ALREADY_EXISTS) {
                    throw $e;
                }
            }
        }
        return $this;
    }

    public function setOptions(array $options, $clean = false) {
        if ($clean) {
            return $this->cleanOptions()->addOptions($options);
        } else {
            foreach ($options as $name => $value) {
                $this->addOption($name, $value, true);
            }
            return $this;
        }
    }

    public function getOption($name, $default = null) {
        return isset($this->_options[$name]) ? $this->_options[$name] : $default;
    }

    public function getOptions() {
        return $this->_options;
    }

}
