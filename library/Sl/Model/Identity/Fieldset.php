<?php
namespace Sl\Model\Identity;

use Sl\Model\Identity\Fieldset as Fs;

/**
 * Набор полей
 * 
 * @uses \Sl\Model\Identity\Field
 * @uses \Sl\Model\Identity\Fieldset\Comparison\Multi
 * @uses \Sl\Model\Identity\Field\Factory
 */
class Fieldset {
    
    /**
     * Exception Code (EC)
     * Код исключения, срабатываеющего, если поле уже установлено
     */
    const EC_ALREADY_SET = 1;
    
    /**
     * Код исключения, срабатываеющего, если поле не удалось добавить
     */
    const EC_CANT_ADD = 2;
    
    /**
     * Массив полей
     * 
     * @var array 
     */
    protected $_fields = array();
    
    /**
     * Проядок полей
     * 
     * @var array
     */
    protected $_fields_order = array();
    
    /**
     * Условие сравнения
     * 
     * @var Fs\Comparison\Multi
     */
    protected $_comp;
    
    /**
     * Модель, для которой строится набор полей
     * 
     * @var \Sl_Model_Abstract
     */
    protected $_model;
    
    /**
     * Контекст набора
     * 
     * @var Fieldset\Context
     */
    protected $_context_type;

    public function __construct($model, $context) {
        // Установка модели
        if($model instanceof \Sl_Model_Abstract) {
            $this->setModel($model);
        } elseif(is_array($model)) {
            if(isset($model['model']) && isset($model['module'])) {
                $model_name = $model['model'];
                $module_name = $model['module'];
            } else {
                $model_name = key($model);
                $module_name = current($model);
            }
            $this->setModel(\Sl\Service\Helper::getModelByAlias($model_name, $module_name));
        } elseif(is_string($model)) {
            $this->setModel(\Sl\Service\Helper::getModelByAlias($model));
        }
        if(!$this->getModel()) {
            throw new \Exception('Unknown model param type. '.__METHOD__);
        }
        // Установка контекста
        $this->setContextType((string) $context);
    }
    
    /**
     * Добавление поля в набор
     * 
     * @param Field $field Поле
     * @param mixed $position Позиция для вставки. Значения:
     * <ul>
     * <li>'first' - в начало набора,</li> 
     * <li>'last' - в конец набора (по-умолчанию),</li> 
     * <li>число - в конкретную позицию. Если позиция занята - раздвигает,</li> 
     * <li>array('after' => 'fieldname') - после указанного поля,</li> 
     * <li>array('before' => 'fieldname') - перед указанным полем,</li> 
     * <li>array('on' => число) - в конкретную позицию (аналог просто "число")</li> 
     * </ul>
     * Если передается массив, и нет нужного поля - будет исключение
     * 
     * @return Fieldset
     * @throws \Exception
     */
    public function addField(Field $field, $position = 'last') {
        if(isset($this->_fields[$field->getName()])) {
            throw new \Exception('Field "'.$field->getName().'" already added', self::EC_ALREADY_SET);
        }
        $field->setFieldset($this);
        $this->_fields[$field->getName()] = $field;
        $this->_reorder($field->getName(), $position);
        return $this->checkBlock($field);
    }
    
    /**
     * Создаем поле
     * 
     * @param type $name
     * @param array $options Опции
     * @param bool $add Добавить в набор
     * @return Field
     */
    public function createField($name, array $options = array(), $add = true, $position = 'last') {
        try {
            $field = Field\Factory::build($name, $this, $options);
            if($add) {
                $this->addField($field, $position);
            }
            return $field;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
	/**
         * Переставляет поле на конкретную позицию. 
         * @see addField()
         * 
         * @param type $name
         * @param type $position
         * 
         */
	public function moveField($name, $position) {
		if($name instanceof Field) {
			$name = $name->getName();
		}
		if(false !== ($key = array_search($name, $this->_fields_order))) {
			unset($this->_fields_order[$key]);
		}
		$this->_reorder($name, $position);
	}
    
    /**
     * Добавление поля с заменой, если такое уже добавлено
     * @see addField()
     * 
     * @param Field $field
     * @return Fieldset
     * @throws \Exception
     */
    public function setField(Field $field) {
        try {
            return $this->addField($field);
        } catch (\Exception $e) {
            if($e->getCode() == self::EC_ALREADY_SET) {
                unset($this->_fields[$field->getName()]);
                return $this->setField($field);
            }
            throw new \Exception('Can\'t add field "'.$field->getName().'". '.__METHOD__, self::EC_CANT_ADD);
        }
    }
    
    /**
     * Добавление нескольких полей
     * @see addField()
     * 
     * @param array $fields
     * @return Fieldset
     */
    public function addFields(array $fields = array()) {
        foreach($fields as $field) {
            try {
                $this->addField($field);
            } catch(\Exception $e) {
                // Просто игнорируем неверные данные
            }
        }
        return $this;
    }
    
    /**
     * Очистка списка полей
     * 
     * @return Fieldset
     */
    public function cleanFields() {
        $this->_fields = array();
        $this->_fields_order = array();
        return $this;
    }
    
    /**
     * Установка нескольких полей
     * @see addFields()
     * 
     * @param array $fields
     * @return Fieldset
     */
    public function setFields(array $fields = array()) {
        return $this->cleanFields()->addFields($fields);
    }
    
    /**
     * Возвращает поля c возможностью фильтрации по ролям
     * 
     * @param mixed $role Роль(и). <b>Возможные значения</b>:
     * <ul>
     * <li>'rolename' - одна из ролей поля равна 'rolename',</li>
     * <li>array('firstrolename', 'secrolename') - Поле принадлежит ко всем полям списка,</li>
     * <li>array(array('firstrolename'), array('secrolename', 'thirdrolename')) - Поле принадлежит к одному из перечисленных наборов</li>
     * </ul>
     * @return Field[]
     * @throws \Exception Если параметр $role задан неверно
     */
    public function getFields($role = false) {
        $data = array();
        if(false == $role) {
            foreach($this->_fields_order as $name) {
                $data[] = $this->getField($name);
            }
        } elseif(is_string($role)) {
            // Конкретная роль
            foreach($this->_fields_order as $name) {
                $field = $this->getField($name);
                if($field && $field->hasRole($role)) {
                    $data[] = $field;
                }
            }
        } elseif(is_array($role)) {
            if(is_array(current($role))) {
                // массив массивов - тогда ИЛИ -> И
                foreach($this->_fields_order as $name) {
                    $field = $this->getField($name);
                    $result = false;
                    foreach($role as $roles) {
                        $result |= $field->hasRole($roles);
                    }
                    if($result) {
                        $data[] = $field;
                    }
                }
            } else {
                // массив - тогда И
                foreach($this->_fields_order as $name) {
                    $field = $this->getField($name);
                    if($field && $field->hasRole($role)) {
                        $data[] = $field;
                    }
                }
            }
        } else {
            throw new \Exception('Unknown role type "'.gettype($role).'". '.__METHOD__);
        }
        return $data;
    }
    
    /**
     * Возвращает объект поля по имени
     * 
     * @param string $name
     * @return Field
     */
    public function getField($name) {
        return isset($this->_fields[$name])?$this->_fields[$name]:null;
    }
	
	public function hasField($name) {
		return isset($this->_fields[$name]);
	}
    
    public function getFieldByIndex($index) {
        if(isset($this->_fields_order[$index])) {
            return $this->getField($this->_fields_order[$index]);
        }
        return null;
    }
    
    /**
     * Добавляет сравнения к текущему сравнению (на самый низкий уровень)
     * 
     * @param array $comps Сравнения
     * @return Fieldset
     */
    public function addComps(array $comps = array()) {
        $this->getComp()->addComps($comps);
        return $this;
    }
    
    /**
     * Возвращает объект сравнения
     * 
     * @return Fs\Comparison\Multi
     */
    public function getComp() {
        if(!isset($this->_comp)) {
            $comp = new Fs\Comparison\Multi();
            $this->_comp = $comp;
        }
        return $this->_comp;
    }
    
    /**
     * Возвращает модель
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->_model;
    }
    
    /**
     * Устанавливает модель
     * 
     * @param \Sl_Model_Abstract $model
     * @return Fieldset
     */
    public function setModel(\Sl_Model_Abstract $model) {
        $this->_model = $model;
        return $this;
    }
    
    /**
     * Устанавливает контекст
     * 
     * @param string $context
     * @return string
     */
    public function setContextType($context_type) {
        $this->_context_type = $context_type;
        return $this;
    }
    
    /**
     * Возвращает контекст
     * 
     * @return string
     */
    public function getContextType() {
        return $this->_context_type;
    }
    
    /**
     * Пересортировка полей при добавлении
     * @see addField()
     * 
     * @param string $name
     * @param mixed $position
     * @return bool
     * @throws \Exception
     */
    protected function _reorder($name, $position) {
        if(is_array($position)) {
            $type = key($position); // before; after; on
            $fieldname = current($position);
            if(is_string($fieldname)) {
                $base_position = array_search($fieldname, $this->_fields_order);
                if($base_position === false) {
                    throw new \Exception('Field "'.$fieldname.'" doest exists. So you can\'t add something '.$type.'. '.__METHOD__);
                }
            } else {
                $base_position = intval($fieldname);
                $type = 'on';
            }
            switch($type) {
                case 'after':
                    return $this->_reorder($name, $base_position+1);
                case 'before':
                case 'on':
                default:
                    return $this->_reorder($name, $base_position);
            }
        } else {
            switch($position) {
                case 'first':
                    array_unshift($this->_fields_order, $name);
                    break;
                case 'last':
                    $this->_fields_order[] = $name;
                    break;
                default:
                    $position = intval($position);
                    if(array_key_exists($position, $this->_fields_order)) {
                        array_splice($this->_fields_order, $position, 0, array($name));
                    } else {
                        $this->_fields_order[$position] = $name;
                    }
                    break;
            }
            return ksort($this->_fields_order);
        }
    }
    
    public function checkBlock(Field $field) {
        if(!$field->isRelated()) {
            // Проверка не кастомное ли поле
            $method = \Sl_Model_Abstract::buildMethodName($field->getName(), 'get');
            if(!method_exists($field->getModel(), $method)) {
                return $this;
            }
        }
        
        $model = $this->getModel();
        $fname = $field->getName();
        if($field->isRelated()) {
            
         
           $dest_aliases = array_pop(\Sl\Service\Alias::describeAlias($field->relationAlias(), $this->getModel(), true));
           $model = \Sl\Service\Helper::getModelByAlias($dest_aliases['dest']);
            if(!$model || !($model instanceof \Sl_Model_Abstract)) {
                throw new \Exception('Can\'t determine destination object for field "'.$field->getName().'". '.__METHOD__);
            }
            $fname = $field->cleanName();
        }
        $res = \Sl_Service_Acl::joinResourceName(array(
            'type' => \Sl_Service_Acl::RES_TYPE_OBJ,
            'module' => $model->findModuleName(),
            'name' => $model->findModelName(),
            'field' => $fname
        ));
        if(!\Sl_Service_Acl::isAllowed($res, \Sl_Service_Acl::PRIVELEGE_READ) && ($field->cleanName() != 'id')) {
            $field->block();
        }
        return $this;
    }
}
