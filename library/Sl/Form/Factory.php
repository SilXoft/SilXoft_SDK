<?php

class Sl_Form_Factory {
		
	const  ITEM_REQUIRED_VALIDATOR = '\Sl\Validate\Required\Item';	
	const  REQUIRED_SUFFIX	= ' <span class="reuired_field">*</span>';
	const  AFTER_SAVE_URL_INPUT = 'form_after_save_url';

	protected static $default_sorter = array();
	protected static $_translator;
	protected static $_form_options;
	public static $default_subform_field_decorators = array(
		'ViewHelper',
		array(
			'Label',
			array('placement' => 'prepend')
		),
	);
	public static $delete_item_decorators = array(
		'ViewHelper',
		array(
			'Label',
			array('class' => 'del_button icon-remove')
		),
	);
	public static $delete_new_item_decorators = array(
		'ViewHelper',
		array(
			'Label',
			array(
				'class' => 'del_button icon-remove',
				'style' => 'display:none;'
			)
		),
	);
    public static $default_subform_text_field_decorators = array('ViewHelper',);
	public static $default_subform_decorators = array(
		'FormElements',
		array(
			array('div' => 'HtmlTag'),
			array('tag' => 'div')
		),
		array(
			array('div' => 'HtmlTag'),
			array(
				'tag' => 'div',
				'class' => 'item control-group'
			)
		)
	);
	public static $default_subform_group_decorators = array(
		'FormElements',
		array(
			array('div' => 'HtmlTag'),
			array(
				'tag' => 'div',
				'class' => 'form_list controls'
			)
		),
		array(
			'Label',
            array('placement' => 'prepend', 'class' => 'control-label', 'escape' => false)
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group subform')
		)
	);
	public static $default_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'controls')
		),
		array(
			'Label',
            array('placement' => 'prepend', 'class' => 'control-label')
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group')
		)
	);
    public static $default_date_decorators = array(
		'ViewHelper',
        array(
            array('spanOpen' => 'HtmlTag'),
            array('tag' => 'span', 'placement' => 'append', 'class' => 'add-on', 'openOnly' => true),
        ),
        array(
            array('i' => 'HtmlTag'),
            array('tag' => 'i', 'placement' => 'append', 'class' => 'icon-calendar'),
        ),
        array(
            array('spanClose' => 'HtmlTag'),
            array('tag' => 'span', 'placement' => 'append', 'closeOnly' => true),
        ),
        array(
            array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'input-append date')
        ),
        array(
            array('div2' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'controls')
        ),
        array(
            'Label',
            array('placement' => 'prepend', 'class' => 'control-label')
        ),
        array(
            array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group')
        )
    );
	public static $default_open_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'input-append', 'placement' => 'prepend',
				'openOnly' => true)
		),
		array(
			array('div2' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'controls', 'placement' => 'prepend',
				'openOnly' => true)
		),
		array(
			'Label',
            array('placement' => 'prepend', 'class' => 'control-label')
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group', 'placement' => 'prepend',
				'openOnly' => true)
		)
	);
	public static $default_close_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
			array('tag' => 'div',  'placement' => 'append',
				'closeOnly' => true)
		),
		array(
			array('div2' => 'HtmlTag'),
			array('tag' => 'div', 'placement' => 'append',
				'closeOnly' => true)
		),
		array(
			array('section' => 'HtmlTag'),
			array('tag' => 'div', 'placement' => 'append',
				'closeOnly' => true),
		)
	);
	public static $button_open_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'controls', 'openOnly' => true, 'placement' => 'prepend',)
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group', 'openOnly' => true, 'placement' => 'prepend',)
		) 
	);
	public static $button_close_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
            array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append',)
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')
		) 
	);
	public static $button_decorators = array(
		'ViewHelper',
		array(
			array('div' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'controls')
		),
		array(
			array('section' => 'HtmlTag'),
            array('tag' => 'div', 'class' => 'control-group')
		) 
	);
	public static $hidden_decorators = array('ViewHelper');
	
	const RELATION_NAMES_SUFFIX = 'names';
	
	public static $sort_order_extend = 0;
	
	protected static function eraseSorter() {
		self::$default_sorter = array();
	}
	
	protected static function IncrementSorterExtend() {
       self::$sort_order_extend +=100000;
	}
	
	protected static function getSorterValue($sort_order = false, $is_subform = false) {
        if ($is_subform) {
        	$sort_order+=self::$sort_order_extend;
            //return $sort_order?$sort_order:null;
        }
		
		
		
		if ($sort_order) {
			$sort_order = round($sort_order / 10) * 10;
		}

		if (!$sort_order) {
			$sort_order = count(self::$default_sorter) ? max(self::$default_sorter) + 10 : 10;
		} else {
			while (in_array($sort_order, self::$default_sorter))
				$sort_order += 10;
		}

		self::$default_sorter[] = $sort_order;
        //echo $sort_order."\r\n";
		return $sort_order;
	}

	public static function setTranslator(Zend_Translate $translate) {
		$old_tr = self::$_translator;
		self::$_translator = $translate;
		return $old_tr;
	}

	public static function getTranslator() {
		if (!isset(self::$_translator)) {
			if (Zend_Registry::isRegistered('Zend_Translate')) {
				self::setTranslator(Zend_Registry::get('Zend_Translate'));
			}
		}
		return self::$_translator;
	}

	/*
	 * Побудова форми із конфіга або моделі
	 * @param $data - Sl_Model_Abstract або array(Sl_Module_Abstract, form_name)
	 *
	 * */
    
public static function build($data, $try_fill = false, $subform = false, $ajaxcreation = false, $exclude_fields = array(), $class_prefix = array(), $readonly = false, $subFormObj, $sub_form_name=null) {
		if (is_array($data) && $data[0] instanceof \Sl_Module_Abstract) {
            return self::_fromConfig($data, $try_fill);
		} elseif ($data instanceof Sl_Model_Abstract) {
			return self::_fromModel($data, $try_fill, $subform, $ajaxcreation, $exclude_fields, $class_prefix, $readonly, $subFormObj, $sub_form_name);
		} else {
			throw new Sl_Exception_Form('Not implemented.');
		}
	}

	protected static function _fromConfig(array $data, $try_fill = false) {

		$module = $data[0];
        $module_name = $module->getName();

		$form_name = $data[1];
        $config = $module->section('forms')->$form_name;

        if (!$config instanceof Zend_Config || !$config->fields instanceof Zend_Config)
			throw new \Sl_Exception_Form('Unset config for ' . $form_name);

        $class = 'form-horizontal form-config';
        if (isset($config->class) && $config->class) {
            $class = $config->class;
        }
        
        $form = new \Sl\Form\Form(array(
			'method' => 'POST',
			'decorators' => array(
				//	new Zend_Form_Decorator_FormElements(),
				//	new Zend_Form_Decorator_Fieldset(),
				//	new Zend_Form_Decorator_Form(),
			),
            'class' => $class,
			)
		);

        $form->setTranslator(self::getTranslator());
        
        \Sl_Service_Acl::setContext($form, 'form');
        
		foreach ($config->fields as $name => $field) {
			$field_resource = \Sl_Service_Acl::joinResourceName(array(
				'type' => \Sl_Service_Acl::RES_TYPE_FIELD,
				'module' => $module_name,
				'name' => $form_name,
				'field' => $name
			));

			$priv_read = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_READ);

			$priv_edit = \Sl_Service_Acl::isAllowed($field_resource, \Sl_Service_Acl::PRIVELEGE_UPDATE);
            
            
                
			if ($priv_edit || $priv_read) {
                $form->addElement(self::_mapMemberTypeFieldType($field->type), $name, array(
					'disableLoadDefaultDecorators' => true,
                    /* 'decorators' => array(
						'viewHelper',
						array(
							array('div' => 'HtmlTag'),
							array('tag' => 'div')
						),
						array(
							'Label',
							array('placement' => 'prepend')
						),
						array(
							array('section' => 'HtmlTag'),
							array('tag' => 'section')
						),
						new Sl_Form_Decorator_Acl( array('acl' => Sl_Service_Acl::acl(), )),
						
                      ), */
                    'decorators' => self::_getDefaultDecorators($field->type), //self::$default_decorators,
                    'translator' => $form->getTranslator(),
                    'label' => $field->label,
                    'title' => $field->title ? $field->title : $field->label,
                    'class' => 'fieldtype-' . $field->type,
                    'placeholder' => isset($field->placeholder) ? $field->placeholder : '',
				));
                if (isset($field->data)) {
                    foreach ($field->data as $k => $v) {
                        $form->getElement($name)->setAttrib('data-' . $k, $v);
                                    }
                                }

                if (in_array($field->type, array('date', 'datetime'))) {
                 
                    $form->getElement($name)->setDecorators(self::$default_date_decorators);
                } elseif ($field->type == 'select' && $field->options) {
                  
                    $options = is_object($field->options) ? $field->options->toArray() : $field->options;
                    $form->getElement($name)->setMultiOptions($options);
                }
                
				if (!$priv_edit || $field->readonly)
                    $form->getElement($name)->setOptions(array('readonly' => 'readonly'));
                
                if ($field->required)
                    $form->getElement($name)->setOptions(array('required' => 'required'));
                
                if ($field->disabled) {
                    $form->getElement($name)->setOptions(array('readonly' => 'readonly'));
                }
                
                if ($field->class) {
                    $form->getElement($name)->setOptions(array('class' => $field->class));
				}

                if ($field->validators) {
					foreach ($field->validators as $val) {
                        $form->getElement($name)->addValidator($val->name, null, ($val->options) ? $val->options : array());
					}
				}
                
                if ($field->hidden || $field->withoutLabel || $field->type == 'submit') {
                    $form->getElement($name)->removeDecorator('Label');
				}
				
                
				
                if ($try_fill && $field->value) {
                    $form = self::setElementValue($form, $name, $field->value);
					//$form -> getElement($name) -> setValue($field -> value);
				}
            }
        }
        

        return $form;
    }
    protected static function _addFeildToTab(Sl_Model_Abstract $model, $try_fill = false, $subform = false, $ajaxcreation = false, $exclude_fields = array(), $class_prefix = array(), $readonly = false, $form, $key, $value){
             
        
        //print_r($form->getAttribs());

        //echo get_class($form); die;
        $form_options = \Sl_Module_Manager::getInstance()->getCustomConfig($model->findModuleName(), 'forms', $form_name);
        
                
        if (!count($class_prefix)) {
            $class_prefix[] = \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX;
        }

        $model_described_fields = $model->describeFields();

        $default_decorators = $subform ? self::$default_subform_field_decorators : self::$default_decorators;
        //виведення полів
        //підготовка id елемента
        $id_prefix = $class_prefix;
        unset($id_prefix[0]);
        if (count($id_prefix)) {
            $id_prefix[] = $model->getId() ? $model->getId() : 'new';
			}

        \Sl_Service_Acl::setContext($model, 'form');
        $group_tab_name = $value['group_tab'] . '_gr';

        if( $form->getSubForm($value['element_tab']) && $form->getSubForm($value['element_tab'])->getDisplayGroup($group_tab_name) ){
             $dg = $form->getSubForm($value['element_tab'])->getDisplayGroup($group_tab_name);
        
        } else{
        $dg = new \Sl\Form\DisplayGroup($group_tab_name, $form->getPluginLoader('decorator'), array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => array(
                array('FormElements'),

        )));
        
        if($value['group_tab_custom']){
        $dg->setDecorators(array(array(
                'ViewScript',
                array(
                    'viewScript' => strtolower($model->findModelName()).'/tab_'.$value['group_tab'].'.phtml',
                ),
               ), ));
		}

        $dg->setLabel($value['group_tab_name']);
        }
        
            $priv_read = \Sl_Service_Acl::isAllowed(array(
                        $model,
                        $key
                            ), \Sl_Service_Acl::PRIVELEGE_READ);


            $priv_edit = \Sl_Service_Acl::isAllowed(array(
                        $model,
                        $key
                            ), \Sl_Service_Acl::PRIVELEGE_UPDATE);


            //echo $key; var_dump($priv_read); var_dump($priv_edit); 
            if (((($priv_read && !$ajaxcreation) || $priv_edit) && (!count($exclude_fields) || !in_array($key, $exclude_fields))) ||
                    $key == 'id') {

                $value['type'] = ($form_options->$key->type) ? $form_options->$key->type : $value['type'];
                $value['readonly'] = isset($form_options->$key->readonly) ? $form_options->$key->readonly : $value['readonly'];


                if ($list = $model->ListsAssociations($key)) {

                    $options = \Sl\Service\Lists::getList($list);
                    if (count($options))
                        $value['type'] = 'select';
                }

                $decorator = self::$default_decorators;
                
                if (self::_mapMemberTypeFieldType($value['type']) == 'hidden') {
                    $decorator = self::$hidden_decorators;
                } elseif ($value['type'] == 'checkbox' && $value['switch']) {
                    //http://www.bootstrap-switch.org/
                    $decorator =
                            array('ViewHelper',
                                array(
                                    //array('div' => 'HtmlTag'),
                                    array('tag' => 'div', 
                                        'class' => 'control-group make-switch '.$value['class_switch'].'',
                                        'data-text-label' => $value['label'],
                                        'data-on' => $value['data-on'],
                                        'data-off' => $value['data-off'],
                                        'data-on-label' => $value['data-on-label'],
                                        'data-off-label' => $value['data-off-label'])
                                ),
                    );
                } elseif (in_array($value['type'], array('date', 'datetime')) && !($readonly || !$priv_edit || !$model->isEditable() || $value['disabled'])) {
                    $decorator = self::$default_date_decorators;
                    //print_r($decorator);
                    //die;
                } elseif ($value['type'] == 'file') {
                    $decorator = array(
                        
                        array('Text', array('tag' => 'span', 'placement' => 'append', 'content' => 'Выбирите файл')),
                        'File',
                        array(array('spanO' => 'HtmlTag'), array('tag' => 'span', 'class' => 'btn btn-success fileinput-button')),
                        
                        array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                        
                        array(array('l' => 'Label'), array('placement' => 'prepend', 'class' => 'control-label')),
                        array(array('sect' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
                            
                    );
                } elseif ($subform && in_array(self::_mapMemberTypeFieldType($value['type']), array('textarea', 'seelct', 'text'))) {
                    $decorator = self::$default_subform_text_field_decorators;
                } elseif ($subform) {
                    $decorator = self::$default_subform_field_decorators;
                }

                $id_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array_merge($id_prefix, array($key)));
                
                
                $dg->addElement($form->createElement(self::_mapMemberTypeFieldType($value['type']), $key, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => $decorator,
                    'label' => $value['label'],
                    'title' => $value['label'],
                    'placeholder' => $value['label'],
                    'id' => $id_name . ($ajaxcreation ? \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'ajax' : ''),
                        
                )));

                $dg->getElement($key)->setValidators($model->validators($key));

                if ($key != 'id' && ($readonly || !$priv_edit || !$model->isEditable() || $value['disabled'])) {
                    $dg->getElement($key)->setOptions(array('readonly' => 'readonly'));
                }

                $dg->getElement($key)->addDecorator(new \Sl_Form_Decorator_Error(array('class' => 'alert alert-error')));

                $sort_order = false;
                if (isset($form_options->$key) && isset($form_options->$key->sort_order) || isset($value['sort_order'])) {
                    $sort_order = $form_options->$key->sort_order ? $form_options->$key->sort_order : $value['sort_order'];
                }

                $dg->getElement($key)->setOrder(self::getSorterValue($sort_order, $subform));

                if (!$options) {
                    $options = array();
                }
                if ($key == 'id' ||
                        (isset($form_options->$key) && isset($form_options->$key->readonly) && $form_options->$key->readonly) ||
                        (isset($value['readonly']) && $value['readonly']) ||
                        in_array($value['type'], array('date', 'datetime'))
                ) {


                    $dg->getElement($key)->setOptions(array('readonly' => 'readonly'));
                    if ($value['type'] == 'select') {

                        if ($try_fill) {
                            $method = $model->buildMethodName($key, 'get');
                            if (method_exists($model, $method)) {
                                $cur_val = $model->$method();
                                if (!is_null($cur_val)) {
                                    if (isset($options[$cur_val])) {
                                        $options = array(
                                            $cur_val => $model->Lists($key, $cur_val)
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
                if ((isset($form_options->$key) && isset($form_options->$key->required) && $form_options->$key->required) || (isset($value['required']) && $value['required'])) {
                    $dg->getElement($key)->setRequired(true)->getDecorator('label')->setOption('requiredSuffix', self::REQUIRED_SUFFIX);
                    $dg->getElement($key)->setRequired(true)->getDecorator('label')->setOption('escape', false);
                }



                $class_name = implode(' ', array(implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array_merge($class_prefix, array($key))),
                    implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array('fieldtype', $value['type']))));
                $custom_class = array();
                if ((isset($form_options->$key) && isset($form_options->$key->class) && $form_options->$key->class) || (isset($value['class']) && $value['class'])) {
                    $custom_class = (isset($value['class']) && $value['class']) ? $value['class'] : $form_options->$key->class;
                    $custom_class = is_array($custom_class) ? $custom_class : array($custom_class);
                }
                $dg->getElement($key)->setOptions(array('class' => array_merge(array($class_name), $custom_class)));

                if ((isset($value['disabled']) && $value['disabled']) || (isset($form_options->$key) && isset($form_options->$key->disabled) && $form_options->$key->disabled)) {
                    $dg->getElement($key)->setOptions(array('readonly' => 'readonly'));
                }

                if ($value['type'] == 'hidden') {
                    $dg->getElement($key)->removeDecorator('Zend_Form_Decorator_Label');
                }
                if (isset($value['options'])) {
                    $dg->getElement($key)->setOptions($value['options']);
                }
                if ($value['type'] == 'select') {
                    $dg->getElement($key)->setMultiOptions($options);
                }
                if ($value['type'] == 'select' && $value['options']) {
                    $dg->getElement($key)->setMultiOptions($value['options']);
                }

                if (isset($value['default'])) {
                    $dg->getElement($key)->setOptions(array('data-default' => $value['default']));
                } elseif (isset($form_options->$key) && isset($form_options->$key->default)) {
                    $dg->getElement($key)->setOptions(array('data-default' => $form_options->$key->default));
                }

                if ($try_fill) {
                    $method = $model->buildMethodName($key, 'get');

                    if ($value['type'] == 'checkbox' && $model->$method()) {

                        $dg->getElement($key)->setValue(1)->setOptions(array('checked' => 'checked'));
                    } else {
                        $field_value = $model->$method();
                        if (!strlen($field_value)) {
                            if ($value['type'] == 'date') {
                            } elseif ($value['type'] == 'datetime' || $value['type'] == 'timestamp') {
                            }
                        }

                        if ($form->getElement($key)) {
                            $form= self::setElementValue($form, $key, $field_value);
                        } else {
                            $dg -> getElement($key) -> setValue($field_value);
                        }
                    }
                }
            }  
        if(!$form->getSubForm($value['element_tab'])){

        if($form instanceof \Sl_Form_SubForm){
            $sub_Form = new \Sl_Form_SubForm();          
          //  $sub_Form->addDecorator($value['include_decorator_elemets'].'subform');           
        }
        else {
                $sub_Form = new \Sl_Form_SubForm(
                        array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => array($value['include_decorator_elemets']),)
                );                   
            }
        
        }
        if ($form->getSubForm($value['element_tab'])) {
            $sub_Form = $form->getSubForm($value['element_tab']);
            $sub_Form->addDisplayGroups(array($dg));

        } else {

            $sub_Form->addDisplayGroups(array($dg));
            $form->addSubForm($sub_Form, $value['element_tab']);
        }

        
  	return $form;
	}
        
        

	protected static function _fromModel(Sl_Model_Abstract $model, $try_fill = false, $subform = false, $ajaxcreation = false, $exclude_fields = array(), $class_prefix = array(), $readonly = false, $subFormObj = null, $sub_form_name=null) {
		//Дістаємо налаштування виведення полів форми із конфіга модуля
        $form_name = strtolower('model_' . $model->findModelName() . '_form');
        
		//$form_options = \Sl_Module_Manager::getInstance() -> getModule($model -> findModuleName()) -> section('forms') -> $form_name;
        $form_options = \Sl_Module_Manager::getInstance()->getCustomConfig($model->findModuleName(), 'forms', $form_name);
		$form_relations = \Sl_Modulerelation_Manager::getRelations($model);

		if (!count($class_prefix)) {
			$class_prefix[] = \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX;
		}

        if ($try_fill && $model->getId() && $subform) {
            $model = \Sl_Model_Factory::mapper($model)->findAllowExtended($model->getId());
		}
                if($subFormObj) {
                    $form = $subFormObj;
                } else {
                    if (!$subform) {
                        self::eraseSorter();
                        $form = new \Sl\Form\Form(array(
                            'method' => 'POST',
                            'decorators' => array(
                                new Zend_Form_Decorator_FormElements(),
                                new Zend_Form_Decorator_Fieldset(),
                                new Zend_Form_Decorator_Form(),
                            ),
                            'editable' => $model->isEditable(),
                            'class' => $model->findModelName() . ' form-horizontal form-model ' . ($ajaxcreation ? ' ajaxcreate' : '') . ' ' . ($model->isEditable() ? ' editable ' : ''),
                        ));
                    } else {
                        $form = $subform;
                    }
                }
                /*
                if($form instanceof \Sl_Form_SubForm && $sub_form_name){
                    $form->setName($sub_form_name);
                }
                 * 
                 */
                
		//наповнення конфіга TODO: прибрати
		//$model -> fillEmptyFieldInfo();

                $model_described_fields = $model->describeFields();
		
		$default_decorators = $subform ? self::$default_subform_field_decorators : self::$default_decorators;
		//виведення полів
		//підготовка id елемента
		$id_prefix = $class_prefix;
		unset($id_prefix[0]);
		if (count($id_prefix)) {
            $id_prefix[] = $model->getId() ? $model->getId() : 'new';
		}
        
        
        \Sl_Service_Acl::setContext($model, 'form');
        
		foreach ($model_described_fields as $key => $value) {
			
if($value['element_tab']){
    self::_addFeildToTab($model, $try_fill, $subform, $ajaxcreation, $exclude_fields, $class_prefix, $readonly, $form,$key, $value);
}
else{
			$priv_read = \Sl_Service_Acl::isAllowed(array(
				$model,
				$key
			), \Sl_Service_Acl::PRIVELEGE_READ);
			
			  
			$priv_edit = \Sl_Service_Acl::isAllowed(array(
				$model,
				$key
			), \Sl_Service_Acl::PRIVELEGE_UPDATE);
            
            
			//echo $key; var_dump($priv_read); var_dump($priv_edit); 
			if (((($priv_read && !$ajaxcreation) || $priv_edit) && (!count($exclude_fields) || !in_array($key, $exclude_fields))) || 
                    $key == 'id') {
					
                $value['type'] = ($form_options->$key->type) ? $form_options->$key->type : $value['type'];
                $value['readonly'] = isset($form_options->$key->readonly) ? $form_options->$key->readonly : $value['readonly'];

                if ($list = $model->ListsAssociations($key)) {

					$options = \Sl\Service\Lists::getList($list);
					if (count($options))
						$value['type'] = 'select';
				}

				$decorator = self::$default_decorators;
				//$readonly = false;
				if (self::_mapMemberTypeFieldType($value['type']) == 'hidden') {
					$decorator = self::$hidden_decorators;
                } elseif ($value['type'] == 'checkbox' && $value['switch']) {
                    //http://www.bootstrap-switch.org/
                    $decorator =
                            array('ViewHelper',
                                array(
                                    array('div' => 'HtmlTag'),
                                    array('tag' => 'div', 'class' => 'controls',
                                        'class' => 'control-group make-switch '.$value['class_switch'].'',
                                        'data-text-label' => $value['label'],
                                        'data-on' => $value['data-on'],
                                        'data-off' => $value['data-off'],
                                        'data-on-label' => $value['data-on-label'],
                                        'data-off-label' => $value['data-off-label'])
                                ),
                    );
                } elseif (!$value['readonly'] && !$value['readonly'] && in_array($value['type'], array('date', 'datetime')) && !($readonly || !$priv_edit || !$model->isEditable() || $value['disabled'])) {

					$decorator = self::$default_date_decorators;
                } elseif ($value['type'] == 'file') {
					$decorator = array(
                        //array(array('i'=>'HtmlTag'), array('tag' => 'i', 'placement' => 'prepend', 'class' => 'icon-plus')),
                        array('Text', array('tag' => 'span', 'placement' => 'append', 'content' => 'Выбирите файл')),
                        'File',
                        array(array('spanO' => 'HtmlTag'), array('tag' => 'span', 'class' => 'btn btn-success fileinput-button')),
                        //array(array('spanC' => 'HtmlTag'), array('tag' => 'span', 'placement' => 'append', 'closeOnly' => 'true')),
                        array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                        //array(array('div2' => 'HtmlTag'),array('tag' => 'div', 'placement' => 'append', 'closeOnly' => true)),
                        array(array('l' => 'Label'), array('placement' => 'prepend', 'class' => 'control-label')),
                        array(array('sect' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
                        //array(array('sect2' => 'HtmlTag'),array('tag' => 'div',  'placement' => 'append','closeOnly' => true)),
                    );
                } elseif ($subform && in_array(self::_mapMemberTypeFieldType($value['type']), array('textarea', 'seelct', 'text'))) {
					$decorator = self::$default_subform_text_field_decorators;
				} elseif ($subform) {
					$decorator = self::$default_subform_field_decorators;
				}
				
				$id_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array_merge($id_prefix, array($key)));
                                
                $form->addElement(self::_mapMemberTypeFieldType($value['type']), $key, array(
                    'disableLoadDefaultDecorators' => true,                    
                    'decorators' => $decorator,
                    'label' => $value['label'],
                    'title' => $value['label'],
                    'placeholder' => $value['label'],
                //    'id' => $id_name . ($ajaxcreation ? \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'ajax' : ''),
                    //'validators' => $model -> validators($key),
                ));
               
                if (isset($value['withoutLabel'])) {
                    $form->getElement($key)->removeDecorator('Label');
				}                
                $form->getElement($key)->setValidators($model->validators($key));
                 
                if ($key != 'id' && ($readonly || !$priv_edit || !$model->isEditable() || $value['disabled'])) {
                    $form->getElement($key)->setOptions(array('readonly' => 'readonly'));
				}
				
                $form->getElement($key)->addDecorator(new \Sl_Form_Decorator_Error(array('class' => 'alert alert-error')));
				
				$sort_order = false;
                if (isset($form_options->$key) && isset($form_options->$key->sort_order) || isset($value['sort_order'])) {
                    $sort_order = $form_options->$key->sort_order ? $form_options->$key->sort_order : $value['sort_order'];
				}
                
                $form->getElement($key)->setOrder(self::getSorterValue($sort_order, $subform));
                
                if (!$options) {
                    $options = array();
                }
                if ($key == 'id' ||
                        (isset($form_options->$key) && isset($form_options->$key->readonly) && $form_options->$key->readonly) ||
				    (isset($value['readonly']) && $value['readonly']) || 
				    in_array($value['type'], array('date', 'datetime'))
                   ) {
						
						
                    $form->getElement($key)->setOptions(array('readonly' => 'readonly'));
                    if ($value['type'] == 'select') {
                        
                        if ($try_fill) {
                            $method = $model->buildMethodName($key, 'get');
                            if (method_exists($model, $method)) {
                                $cur_val = $model->$method();
                                if (!is_null($cur_val)) {
                                    if (isset($options[$cur_val])) {
                                        $options = array(
                                            $cur_val => $model->Lists($key, $cur_val)
                                        );
                                    }
                                }
                            }
                        }
                    }
				}
                if ((isset($form_options->$key) && isset($form_options->$key->required) && $form_options->$key->required) || (isset($value['required']) && $value['required'])) {
                    $form->getElement($key)->setRequired(true)->getDecorator('label')->setOption('requiredSuffix', self::REQUIRED_SUFFIX);
                    $form->getElement($key)->setRequired(true)->getDecorator('label')->setOption('escape', false);
				}
					
					
					
                $class_name = implode(' ', array(implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array_merge($class_prefix, array($key))),
                    implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array('fieldtype', $value['type']))));
				$custom_class = array();
                if ((isset($form_options->$key) && isset($form_options->$key->class) && $form_options->$key->class) || (isset($value['class']) && $value['class'])) {
                    $custom_class = (isset($value['class']) && $value['class']) ? $value['class'] : $form_options->$key->class;
					$custom_class = is_array($custom_class) ? $custom_class : array($custom_class);
				}
                $form->getElement($key)->setOptions(array('class' => array_merge(array($class_name), $custom_class)));

                if ((isset($value['disabled']) && $value['disabled']) || (isset($form_options->$key) && isset($form_options->$key->disabled) && $form_options->$key->disabled)) {
                    $form->getElement($key)->setOptions(array('readonly' => 'readonly'));
                }
                
				if ($value['type'] == 'hidden') {
                    $form->getElement($key)->removeDecorator('Zend_Form_Decorator_Label');
				}
				if (isset($value['options'])) {
                    $form->getElement($key)->setOptions($value['options']);
				}
				if ($value['type'] == 'select') {
                    $form->getElement($key)->setMultiOptions($options);
				}
				if ($value['type'] == 'select' && $value['options']) {
                    $form->getElement($key)->setMultiOptions($value['options']);
				}

                if (isset($value['default'])) {
                    $form->getElement($key)->setOptions(array('data-default' => $value['default']));
                } elseif (isset($form_options->$key) && isset($form_options->$key->default)) {
                    $form->getElement($key)->setOptions(array('data-default' => $form_options->$key->default));
                }
                
				if ($try_fill) {
                    $method = $model->buildMethodName($key, 'get');
					
                    if ($value['type'] == 'checkbox' && $model->$method()) {

                        $form->getElement($key)->setValue(1)->setOptions(array('checked' => 'checked'));
					} else {
                        $field_value = $model->$method();
                        if (!strlen($field_value)) {
                            if ($value['type'] == 'date') {
								//$field_value = date('Y-m-d');
                            } elseif ($value['type'] == 'datetime' || $value['type'] == 'timestamp') {
								//$field_value = date('Y-m-d H:i:s');
							}
						}
						$form = self::setElementValue($form, $key, $field_value);	
						//$form -> getElement($key) -> setValue($field_value);
					}
				}
			}
        }
		}

		//Виведення зв'язків
		//$relations = $model -> fetchRelated();

		foreach ($form_relations as $relation) {

            $relation_name = strtolower($relation->getName());
            
            
			$priv_read = \Sl_Service_Acl::isAllowed(array(
				$model,
				$relation_name
			), \Sl_Service_Acl::PRIVELEGE_READ);

			$priv_edit = \Sl_Service_Acl::isAllowed(array(
				$model,
				$relation_name
			), \Sl_Service_Acl::PRIVELEGE_UPDATE);
			$relation_form_name = self::setElementRelationName($relation_name);
			
            if (($priv_read || $priv_edit) && (!count($exclude_fields) || !in_array($relation_form_name, $exclude_fields))) {
				$relation_readonly = false;	
                if ((isset($form_options->{$relation_form_name}) && isset($form_options->$relation_form_name->readonly) && $form_options->$relation_form_name->readonly)) {
					$relation_readonly = true;
				}
                $iframe_edit = false;
                if ((isset($form_options->{$relation_form_name}) && isset($form_options->$relation_form_name->iframe_edit) && $form_options->$relation_form_name->iframe_edit)) {
				    $iframe_edit = true;
				}
                
                
                switch ($relation->getType()) {
                    case (\Sl_Modulerelation_Manager::RELATION_MODEL_ITEM) :
                        break;
                    case (\Sl_Modulerelation_Manager::RELATION_ITEM_OWNER) :

                        $form = self::addItemElements($form, $model, $relation, $form_options, $try_fill, $relation_readonly || $readonly || !$priv_edit || !$model->isEditable(), array_merge($class_prefix, array(self::setElementRelationName($relation_name))), self::_getRelationValidators($form_name, $relation_form_name, $model, get_class($relation->getRelatedObject($model))), $subFormObj, $sub_form_name);
                        break;
                    case \Sl_Modulerelation_Manager::RELATION_FILE_ONE:
                    case \Sl_Modulerelation_Manager::RELATION_FILE_MANY:
                        $form = self::addFileElements($form, $model, $relation, $relation_readonly || $readonly || !$priv_edit || !$model->isEditable(), $try_fill);
                        break;
                    default :
                        if ($ajaxcreation) {

                            $prefix_array = array_merge($class_prefix, array(self::setElementRelationName($relation_name)));
                            if ($relation_readonly || $readonly || !$priv_edit || !$model->isEditable()) {
                                self::addRelationElement($form, $model, $relation, $form_options, $try_fill, $relation_readonly || $readonly || !$priv_edit || !$model->isEditable(), false, false, false, $prefix_array);
                            } else {
                                $form = self::addRelationSelectElement($form, $model, $relation, $form_options, $try_fill, $relation_readonly || $readonly || !$priv_edit || !$model->isEditable(), true, $prefix_array);
                            }
                        } else {
                            $relation_model = $relation->getRelatedObject($model);

                            //кнопка швидкого створення зв'язаної моделі
                            $relation_resource = \Sl_Service_Acl::joinResourceName(array(
                                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                        'module' => $relation_model->findModuleName(),
                                        'controller' => $relation_model->findModelName(),
                                        'action' => ($iframe_edit ? \Sl\Service\Helper::CREATE_ACTION : \Sl\Service\Helper::POPUP_CREATE_ACTION)
                            ));

                            $priv_ajax_create = \Sl_Service_Acl::isAllowed($relation_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS);


                            //кнопка швидкого редагування зв'язаної моделі
                            $priv_ajax_edit = false;
                            if ($relation->getType() == \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE) {

                                $relation_resource = \Sl_Service_Acl::joinResourceName(array(
                                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                            'module' => $relation_model->findModuleName(),
                                            'controller' => $relation_model->findModelName(),
                                            'action' => ($iframe_edit ? \Sl\Service\Helper::TO_EDIT_ACTION : \Sl\Service\Helper::POPUP_EDIT_ACTION)
                                ));

                                $priv_ajax_edit = \Sl_Service_Acl::isAllowed($relation_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS);
                            }

                            //кнопка вибору зі списку зв'язаної моделі
                            $priv_popup_list = false;



                            $relation_resource = \Sl_Service_Acl::joinResourceName(array(
                                        'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                                        'module' => $relation_model->findModuleName(),
                                        'controller' => $relation_model->findModelName(),
                                        'action' => \Sl\Service\Helper::POPUP_LIST_ACTION
                            ));

                            $priv_popup_list = \Sl_Service_Acl::isAllowed($relation_resource, \Sl_Service_Acl::PRIVELEGE_ACCESS);


                            $form = self::addRelationElement($form, $model, $relation, $form_options, $try_fill, $relation_readonly || $readonly || !$priv_edit || !$model->isEditable(), $priv_edit && $priv_ajax_create, $priv_edit && $priv_ajax_edit, $priv_popup_list, array_merge($class_prefix, array(self::setElementRelationName($relation_name))), $iframe_edit);
                        }
                }
            }

            // контекст міг помінятися в виводі modulerelations
            \Sl_Service_Acl::setContext($model, 'form');
		}

		if (!$subform) {

			if ($ajaxcreation) {
                $form->addElement('hidden', 'ajax_action', array(
					'disableLoadDefaultDecorators' => true,
					'decorators' => self::$hidden_decorators,
					'value' => '1'
				));
			}
				
			
			
            if ($model->isEditable() && !$readonly) {
                if (!$ajaxcreation) {
					
                    $form->addElement('hidden', self::AFTER_SAVE_URL_INPUT, array(
							'disableLoadDefaultDecorators' => true,
							'decorators' => self::$hidden_decorators,
							'name' => self::AFTER_SAVE_URL_INPUT,
							'order' => self::getSorterValue(false, $subform),
						)); 	
				} else {
                    $form->addElement('button', 'save', array(
                        'disableLoadDefaultDecorators' => true,
                        'decorators' => $ajaxcreation ? self::$button_decorators : self::$button_close_decorators,
                        'name' => 'Save',
                        //'style' => 'clear:both;',
                        'class' => 'btn btn-success save_btn',
                        'validate_action' => $ajaxcreation ? '' : \Sl\Service\Helper::ajaxValidateUrl($model),
                        'order' => self::getSorterValue(false, $subform),
                        'id' => 'Save' . ($ajaxcreation ? \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'ajax' : '')
                    ) + 
                            ($model->getId() > 0 ?
                                    array('locker_resource' => implode(':', array(get_class($model), $model_id))) : array()
                         )
                    );
                }
                
			} elseif ($model -> isFinal()) {

			}
		} else {

		}
                
		return $form;
	}

	public static function addSubform(\Sl_Form_SubForm $subform, \Sl_Model_Abstract $model, $try_fill = false, $class_prefix = array(), $readonly = false) {
		self::IncrementSorterExtend();	
		$subform = self::build($model, $try_fill, $subform, false, array(), $class_prefix, $readonly);
		self::IncrementSorterExtend();
		return $subform;
	}

    public static function addFileElements(\Zend_Form $form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $readonly = false, $try_fill = false) {
        $options = self::_getModelFormConfig($model);
        
        $rel_options = array();
        if (isset($options->{'modulerelation_' . strtolower($relation->getName())})) {
            $rel_options = $options->{'modulerelation_' . strtolower($relation->getName())};
        }
        
        if (isset($rel_options->field_filters)) {
            $field_filters = $rel_options->field_filters->toArray();
        }
        
        $order = 0;
        if ($rel_options && isset($rel_options->sort_order)) {
            $order = $rel_options->sort_order;
        }
        
        $label = ($relation->getType() == \Sl_Modulerelation_Manager::RELATION_FILE_ONE) ? 'Загрузить файл' : 'Загрузить файлы';
        if ($options && isset($options->label)) {
            $label = $options->label;
        }
        if ($rel_options && isset($rel_options->label)) {
            $label = $rel_options->label;
        }
        
        $accept = null;
        if ($options && isset($options->accept)) {
            $accept = $options->accept;
        }
        if ($rel_options && isset($rel_options->accept)) {
            $accept = $rel_options->accept;
        }
        
        $order = self::getSorterValue($order ? $order : false);
        
        $items = array();
        
        if ($try_fill) {
            $files = $model->fetchRelated($relation->getName());
            if ($files) {
                foreach ($files as $file) {
                    $items[] = array(
                        'href' => \Sl\Service\Helper::modelEditViewUrl($file),
                        'text' => $file->getName(),
                        'id' => $file->getId(),
                    );
                }
            }
        }
        
        if ($readonly) {
			$decorators = array(
                array(array('i' => 'HtmlTag'), array('tag' => 'i', 'placement' => 'prepend', 'class' => 'icon-plus')),
                //array(array('lLoad'=>'Text'), array('tag' => 'span', 'placement' => 'append', 'content' => 'Загрузить файл')),
                array('File', array('style' => 'display:none;')),
                //array(array('span'  => 'HtmlTag'),array('tag' => 'span', 'class'=>'input-append', 'openOnly' => true, 'placement' => 'prepend')),
                array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array(array('l'     => 'Label'), array('placement' => 'prepend', 'class' => 'control-label')),
                array(array('divLS' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls ', 'openOnly' => true, 'placement' => 'append')),
                array('FilesList', array(
                        'id' => self::setElementRelationName($relation->getName()) . '_list',
                    'items' => $items,
                    'class' => 'unstyled blockquote'
                    //'style' => 'position: absolute',
                )),
                array(array('divLF' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
                array(array('sect' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
            );
		} else {
			$decorators = array(
                array(array('i' => 'HtmlTag'), array('tag' => 'i', 'placement' => 'prepend', 'class' => 'icon-plus')),
                //array(array('lLoad'=>'Text'), array('tag' => 'span', 'placement' => 'append', 'content' => 'Загрузить файл')),
                'File',
                array(array('spanO' => 'HtmlTag'), array('tag' => 'span', 'class' => 'btn fileinput-button', 'data-url' => \Sl\Service\Helper::ajaxFileCreate(),)),
                array(array('spanLabel' => 'Text'), array('tag' => 'span', 'class' => 'btn disabled', 'placement' => 'prepend', 'content' => $label)),
                array(array('butOpen' => 'HtmlTag'), array(
                    'tag' => 'button',
                    'class' => 'btn set_modulerelation',
                    'type' => 'button',
                        'id' => self::setElementRelationName($relation->getName()) . '_names',
                        'name' => self::setElementRelationName($relation->getName()) . '_names',
                    'data-type' => $relation->getType(),
                    'data-handling' => $relation->getHandling(),
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model))),
                    'data-isfile' => '1',
                        'value' => implode(';', array_map(function($el) {
                                            return $el['id'];
                                        }, $items)),
                    'placement' => 'append',
                    'openOnly' => true,
                )),
                //array(array('butLabel' => 'HtmlTag'), array('tag' => 'span', 'class' => 'icon-list icon-white', 'placement' => 'append')),
                array(array('butClose' => 'HtmlTag'), array('tag' => 'button', 'closeOnly' => true, 'placement' => 'append')),
                array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'input-append input-prepend')),
                array(array('spanC' => 'HtmlTag'), array('tag' => 'span', 'placement' => 'append', 'closeOnly' => 'true')),
                //array(array('span'  => 'HtmlTag'),array('tag' => 'span', 'class'=>'input-append', 'openOnly' => true, 'placement' => 'prepend')),
                array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array(array('l'     => 'Label'), array('placement' => 'prepend', 'class' => 'control-label')),
                array(array('divLS' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls ', 'openOnly' => true, 'placement' => 'append')),
                array('FilesList', array(
                        'id' => self::setElementRelationName($relation->getName()) . '_list',
                    'items' => $items,
                    'class' => 'unstyled blockquote'
                    //'style' => 'position: absolute',
                )),
                array(array('divLF' => 'HtmlTag'), array('tag' => 'div', 'closeOnly' => true, 'placement' => 'append')),
                array(array('sect' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
            );
		}
		
        $el = new \Zend_Form_Element_File(self::setElementRelationName($relation->getName()) . '_m', array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => $decorators,
            'order' => $order,
            'data-url' => \Sl\Service\Helper::ajaxFileCreate(),
        ));
        
        if ($accept) {
            $el->setOptions(array('accept' => $accept));
        }
        
        $el->getPluginLoader('decorator')->addPrefixPath('\\Sl\\Form\\Decorator\\', LIBRARY_PATH . '/Sl/Form/Decorator/');
        
        if (count($field_filters)) {
            $iterator = 0;
            foreach ($field_filters as $filter) {
                $i = $iterator++;
                if (is_object($el->getDecorator('butOpen'))) {
                $el->getDecorator('butOpen')->setOption('data-filter' . $i, implode('-', array(
                        $filter['field'],
                        $filter['matching'],
                        \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                        $filter['value']
                    )));
                }
            }
        }
        
        if (!$readonly && $relation->getType() == \Sl_Modulerelation_Manager::RELATION_FILE_MANY) {
            $el->setOptions(array('multiple' => 'multiple'));
            //$el->getDecorator('spanLabel')->setOption('content', 'Загрузить файлы');
        }
        
        $el_chose_hid = new \Zend_Form_Element_Hidden(self::setElementRelationName($relation->getName()), array(
            'disableLoadDefaultDecorators' => true,
            'value' => implode(';', array_map(function($el) {
                                return $el['id'];
                            }, $items)),
            'decorators' => self::$hidden_decorators,
            'order' => $order - 1,
        ));
        
		
		
		
        /* $el_chose = new \Zend_Form_Element_Button(self::setElementRelationName($relation->getName()).'_names', array(
            'label' => 'Выбрать из загруженных',
            'disableLoadDefaultDecorators' => true,
            'order' => $order+1,
            'class' => 'btn btn-success set_modulerelation',
            'decorators' => self::$button_decorators,
            'data-type' => $relation->getType(),
            'data-handling' => $relation->getHandling(),
            'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model))),
            'data-isfile' => '1',
            'value' => implode(';', array_map(function($el) { return $el['id']; }, $items)),
        ));
        
          $el_chose->getDecorator('div')->setOption('class', 'controls span3'); */
        
        $el->setValidators(array());
        
        $form->addElement($el);
        //$form->addElement($el_chose);
        $form->addElement($el_chose_hid);
        return $form;
    }
    
	/** Вставка пов'заних піделементів
	 * @param \Zend_Form $form - форма
	 * @param \Sl_Model_Abstract $model - об'єкт
	 * @param \Sl\Modulerelation\Modulerelation $relation - зв'язок
	 * @param $config - налаштування з ini файлу
	 * @param $try_fill - прапорець спроби заповнення
	 * @param $readonly - прапорець можливості редагування
	 * @return  \Zend_Form $form
	 * */
	public static function addItemElements($form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $config, $try_fill, $readonly = false, $class_prefix = array(), $validators, $subFormObj, $sub_form_name) {
        $relation_name = strtolower($relation->getName());

        $field_name = self::setElementRelationName($relation_name);
        $field_names_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX);
        $field_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'btn');

        $items_title = isset($config->$field_name->label) ? $config->$field_name->label : $field_name;
        $required = (isset($config->$field_name->required) && $config->$field_name->required) ? self::ITEM_REQUIRED_VALIDATOR : false;

        if ($required && !$readonly) {
            $validators[] = self::_buildValidatorObject(self::ITEM_REQUIRED_VALIDATOR, array('options' => null, 'field' => $field_name, 'class_name' => $relation->getDbTable()->findRelatedModelsKeys(get_class($model)), 'escape' => false));

            $items_title.=self::REQUIRED_SUFFIX;
        }

        //$subforms_group = new Sl_Form_SubForm( array('decorators' => self::$default_subform_group_decorators, 'label'=>$items_title));
        //print_r($validators);
        $subForm = new Sl_Form_SubForm(array(
            'decorators' => self::$default_subform_group_decorators,
            'label' => $items_title,
            'validators' => $validators,
                //'requiredSuffix' => self::REQUIRED_SUFFIX,
                //'escape'=> false,
        ));
        //$subForm
        //$subForm->setOptions(array('requiredSuffix'=> ' <span class="required">*</span> ', 'escape'=> false));
        //print_r(($subForm));
        //die;
        //$form -> getElement($key) -> setRequired(true)->getDecorator('label')->setOption('escape', false);
        $subForm->addDecorator(new \Sl_Form_Decorator_Error(array('class' => 'alert alert-error')));

        $sort_order = false;
        if (isset($config->$field_name)) {
            if (isset($config->$field_name->sort_order)) {
                $sort_order = $config->$field_name->sort_order;
            }
        }

        if (!$readonly) {

            $related_objs = $model->fetchRelated($relation_name);

            foreach ($related_objs as $related_model_id => $related_model) {

                $subForm2 = new Sl_Form_SubForm(array('decorators' => self::$default_subform_decorators, 'validators'));

                $subForm2 = self::addSubform($subForm2, $related_model, $try_fill = true, $class_prefix);

                $subForm2->addElement('hidden', 'delete', array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => self::$delete_item_decorators,
                    'label' => 'x',
                ));
                $subForm2->getElement('delete')->setOrder(1000000000/* self::getSorterValue() */);

                $subForm->addSubForm($subForm2, $related_model_id, self::getSorterValue($sort_order, true));
            }

            $subForm2 = new Sl_Form_SubForm(array('decorators' => self::$default_subform_decorators,));

            // не правильное определение модели
        //    $clear_model = \Sl_Model_Factory::object($relation->getDbTable()->findRelatedModelsKeys(get_class($model)));
            $clear_model = $relation->getRelatedObject($model);
            
            $subForm2 = self::addSubform($subForm2, $clear_model, $try_fill = false, $class_prefix);

            $subForm2->addElement('hidden', 'delete', array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => self::$delete_new_item_decorators,
                'label' => 'x',
            ));

            $subForm2->getElement('delete')->setOrder(1000000000/* self::getSorterValue() */);
            //error_reporting(E_ALL);
            $class = $subForm2->getDecorator('div')->getOption('class');
            $class = is_array($class) ? $class : array($class);
            $class[] = 'new_item';
            $subForm2->getDecorator('div')->setOption('class', $class);
            $subForm->addSubForm($subForm2, 'new', self::getSorterValue($sort_order, true));



            $class = $subForm->getDecorator('div')->getOption('class');
            $class = is_array($class) ? $class : array($class);
            $class[] = array_pop($class_prefix);

            $subForm->getDecorator('div')->setOption('class', $class);
            //$subforms_group -> addSubForm($subForm, $field_name, self::getSorterValue());
        } else {

            $related_objs = $model->fetchRelated($relation_name);

            foreach ($related_objs as $related_model_id => $related_model) {

                $subForm2 = new Sl_Form_SubForm(array('decorators' => self::$default_subform_decorators,));

                $subForm2 = self::addSubform($subForm2, $related_model, $try_fill = true, $class_prefix, true);

                $subForm->addSubForm($subForm2, $related_model_id, self::getSorterValue($sort_order, true));
            }

            $class = $subForm->getDecorator('div')->getOption('class');
            $class = is_array($class) ? $class : array($class);
            $class[] = array_pop($class_prefix);

            $subForm->getDecorator('div')->setOption('class', $class);
        }


      $form->addSubForm($subForm, self::setElementRelationName($relation_name), self::getSorterValue($sort_order));


        return $form;
    }

    /** Вставка пов'заного елемента
	 * @param \Zend_Form $form - форма
	 * @param \Sl_Model_Abstract $model - об'єкт
	 * @param \Sl\Modulerelation\Modulerelation $relation - зв'язок
	 * @param $config - налаштування з ini файлу
	 * @param $try_fill - прапорець спроби заповнення
	 * @param $readonly - прапорець можливості редагування
	 * @return  \Zend_Form $form
	 * */
    public static function addRelationElement(\Zend_Form $form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $config, $try_fill, $readonly = false, $ajaxcreate = false, $ajaxedit = false, $popup_list = false, $class_prefix = array(), $iframe_edit = false) {
        if (in_array($relation->getType(), array(
            \Sl_Modulerelation_Manager::RELATION_MANY_TO_MANY,
            \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY
        ))) {
            $relation_name = strtolower($relation->getName());
            $field_name = self::setElementRelationName($relation_name);

            if ($config->$field_name->element_tab) {
                return self::_addToElementTab($form, $model, $relation, $config, $try_fill, $readonly, $ajaxcreate, $ajaxedit, $class_prefix, $iframe_edit);
            }
            return self::_addToManyRelationElement($form, $model, $relation, $config, $try_fill, $readonly, $ajaxcreate, $ajaxedit, $class_prefix, $iframe_edit);
        }
        //Можливість швидкого редагувати лише тоді, коли є пов'язаний об'єкт    
        $relation_name = strtolower($relation->getName());
        /*
        if ($ajaxedit && $try_fill) {
            
            $relation_data = $model -> fetchRelated($relation_name);
            
            $ajaxedit = (bool) count($relation_data);
            
        } else {
            $ajaxedit = false;
        }
          */  
            
        
        
        $field_name = self::setElementRelationName($relation_name);
        $field_names_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX);
        $field_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'btn');
        $field_btn_clear_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'clear');

        $prefixes = $class_prefix;
        $class_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);

        $prefixes = array_merge($class_prefix, array(self::RELATION_NAMES_SUFFIX));
        $class_names_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);
        
        $field_name_decorators = self::$hidden_decorators;
        
        $target_model = $relation->getRelatedObject($model);
        $target_model_name = $target_model->findModelName();
        
        $required = (isset($config->$field_name->required) && $config->$field_name->required) ? self::ITEM_REQUIRED_VALIDATOR : false;
        
        if (!$readonly) {
            $form->addElement('hidden', $field_name, array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => self::$hidden_decorators,
                'class' => $class_name,
                'data-modelname' => $target_model_name,
            ));
            $form->addElement('text', $field_names_name, array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => self::$default_open_decorators,
            ));
            
            if ($required) {
                $form->getElement($field_name)->setRequired(true);
                $form->getElement($field_name)->addDecorator(new \Sl_Form_Decorator_Error());
                //$form->getElement($field_name)->addValidator(self::_buildValidatorObject(self::ITEM_REQUIRED_VALIDATOR,array('options'=>null, 'field'=>$field_name, 'class_name'=>$relation -> getDbTable() -> findRelatedModelsKeys(get_class($model)))));
            }
            
            if (in_array($relation->getType(), array(
                \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE
            ))) {
                $form->getElement($field_names_name)->setOptions(array(
                    'data-rel' => \Sl\Service\Helper::ajaxSearchUrl($relation->getDependedTable(get_class($model)), true),
                    'data-handling' => $relation->getHandling() ? 1 : 0,
                ));
                
                if(\Sl_Service_Settings::value('USE_NEW_LISTVIEW', false)) {
                    $form->getElement($field_names_name)->setOptions(array(
                        'data-rel' => \Sl\Service\Helper::buildModelUrl($relation->getRelatedObject($model), 'ajaxautocomplete'),
                    ));
                }

                $form->getElement($field_name)->setOptions(array(
                    'data-name' => '',
                ));
                
                $class_names_name .= ' autocomplete';
            } else {
                $form->getElement($field_names_name)->setOptions(array('readonly' => 'readonly'));
            }
            $class_name_names = 'input_set_modulerelation';
            if ($ajaxcreate)
                $class_name_names .= '_ajaxcreate';
            if ($ajaxedit)
                $class_name_names .= '_ajaxedit';
            //if($ajaxcreate) $class_name_names .= '_ajaxcreate';
            $class_names_name .=' ' . $class_name_names . ' ';
            $form->getElement($field_names_name)->setOptions(array('class' => $class_names_name));
            
            /*
            $form -> addElement('button', $field_btn_clear_name, array(

                'disableLoadDefaultDecorators' => true,
                'decorators' => self::$hidden_decorators,
                'class' => 'clear_modulerelation btn',
                'label' => '',
                'title' => self::getTranslator()->translate('Clear'),
               
              
            )  
              
            );
            */
            
            $form->addElement('button', $field_btn_name, array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => ($ajaxedit || $ajaxcreate) ? self::$hidden_decorators : self::$default_close_decorators,
                'class' => 'set_modulerelation btn',
                'label' => '',
                'title' => self::getTranslator()->translate('Select'),
                'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model))),
                'data-type' => $relation->getType(),
                'data-handling' => $relation->getHandling() ? 1 : 0,
            ) + 
                    (!$popup_list ? array('disabled' => 'disabled') : array())
            );
            
            
            
            if ($ajaxedit) {
                
                $field_edit_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'edit');
                
                $form->addElement('button', $field_edit_btn_name, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => $ajaxcreate ? self::$hidden_decorators : self::$default_close_decorators,
                    'class' => 'ajax_edit_modulerelation btn',
                    'label' => '',
                    'title' => self::getTranslator()->translate('Edit'),
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model)), ($iframe_edit ? \Sl\Service\Helper::TO_EDIT_ACTION : \Sl\Service\Helper::POPUP_EDIT_ACTION)),
                    'data-type' => $relation->getType(),
                    'data-relation' => self::setElementRelationName($relation->getName())
                        ) +
                        ($iframe_edit ? array('data-iframe' => 1) : array())
                );
            }
            
            if ($ajaxcreate) {

                $field_create_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'create');
                $form->addElement('button', $field_create_btn_name, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => self::$default_close_decorators,
                    //'decorators' => array('ViewHelper'),
                    'class' => 'ajax_create_modulerelation btn',
                    'label' => '',
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model)), ($iframe_edit ? \Sl\Service\Helper::CREATE_ACTION : \Sl\Service\Helper::POPUP_CREATE_ACTION)),
                    'data-type' => $relation->getType(),
                    'data-relation' => self::setElementRelationName($relation->getName())
                ) + 
                        ($iframe_edit ? array('data-iframe' => 1) : array())
                );
              //  $form -> getElement($field_create_btn_name) -> setLabel('');
            }

            $sort_order = false;
            $request_fields = $field_filters = array();
            if (isset($config->$field_name)) {
                if (isset($config->$field_name->sort_order)) {
                    $sort_order = $config->$field_name->sort_order;
                }
                if (isset($config->$field_name->request_fields)) {
                    $request_fields = $config->$field_name->request_fields->toArray();
                }

                if (isset($config->$field_name->field_filters)) {
                    $field_filters = $config->$field_name->field_filters->toArray();
                }    
            
              
                

                if (isset($config->$field_name->label)) {
                    $label = $config->$field_name->label;
                    //$form -> getElement($field_names_name) -> setLabel($label.($required?self::REQUIRED_SUFFIX.'43':''))-> setOptions(array('placeholder'=>$label, 'title'=>$label));
                    //
                    if ($required) {
                        $form->getElement($field_names_name)->setLabel($label . self::REQUIRED_SUFFIX)->setOptions(array('placeholder' => $label, 'title' => $label));
                        $form->getElement($field_names_name)->setRequired(true)->getDecorator('label')->setOption('escape', false);
                    } else {
                        $form->getElement($field_names_name)->setLabel($label)->setOptions(array('placeholder' => $label, 'title' => $label));
                    }
                }   
            }
            
            if (in_array($relation->getType(), array(
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY,
                    ))) {
                         $vals = array('null');
                if ($model->getId()) {
                              $vals[] = $model->getId();
                         }    
                $field_filters[] = array(
                    'field' => $relation->getName() . ':id',
                                'matching' => 'in',
                    'value' => 'val:' . implode(',', $vals),
                              );  
                    }
            
            
            
            $sort_order = self::getSorterValue($sort_order, ($form instanceof \Sl_Form_SubForm));
            $form->getElement($field_name)->setOrder($sort_order - 5);
            $form->getElement($field_names_name)->setOrder($sort_order - 4);
            if ($form->getElement($field_btn_clear_name))
                $form->getElement($field_btn_clear_name)->setOrder($sort_order - 3);
            
            $form->getElement($field_btn_name)->setOrder($sort_order - 2);
            
            if ($ajaxedit) {
                $form->getElement($field_edit_btn_name)->setOrder($sort_order - 1);
            }
            
            if ($ajaxcreate) {
                $form->getElement($field_create_btn_name)->setOrder($sort_order);
            }
            
            
            
            if (count($request_fields)) {
                $form->getElement($field_btn_name)->setOptions(array('data-request_fields' => $request_fields));
            }
            
            
            $iterator = 0;
            if (count($field_filters)) {
                
                foreach ($field_filters as $filter) {
                    $i = $iterator++;
                    $form->getElement($field_btn_name)->setOptions(array('data-filter' . $i => implode('-', array(
                            $filter['field'],
                            $filter['matching'],
                            \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                            $filter['value'],
                            $filter['strong'],
                        ))));
                    if (in_array($relation->getType(), array(
                        \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE
                    ))) {
                        $form->getElement($field_names_name)->setOptions(array('data-filter' . $i => implode('-', array(
                                $filter['field'],
                                $filter['matching'],
                                \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                                $filter['value'],
                                $filter['strong'],
                            ))));
                    }
                }
            }
        // Restrictions  -  додаткові фільтри
            //$parent_model = $relation->getRelatedObject($model);
            $restrictions = \Sl\Module\Auth\Service\Restrictions::restrictions($model, $relation);
            //print_r(array($model->findModelName(),$restrictions, $relation->getName()));
            if (count($restrictions)) {
                
                $i = $iterator++;
                $form->getElement($field_btn_name);
                $form->getElement($field_btn_name)->setOptions(array('data-filter' . $i => implode('-', array(
                            'id',
                            'in',
                            \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                        'val:' . implode(',', $restrictions),
                        ))));
                if (in_array($relation->getType(), array(
                        \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE
                    ))) {
                    $form->getElement($field_names_name)->setOptions(array('data-filter' . $i => implode('-', array(
                            'id',
                            'in',
                            \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                            'val:' . implode(',', $restrictions),
                        ))));
                    }
            }
        } else {
            // тільки для читання
            // TODO: винести в окремий метод
            
            $form->addElement('hidden', $field_name, array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => self::$hidden_decorators,
                // readonly - ставиться для розуміння, чи редагується поле з форми; використовується в js- обробниках
                'class' => $class_name . ' readonly',
                'data-modelname' => $target_model_name,
            ));
            
            $form->addElement('text', $field_names_name, array(
                'disableLoadDefaultDecorators' => true,
              //  'decorators' => $ajaxedit?self::$default_open_decorators: self::$default_decorators,
                'decorators' => /* $ajaxedit?self::$default_open_decorators: */self::$default_decorators,
                'label' => $field_name,
                'readonly' => 'readonly',
                'class' => $class_names_name,
            ));

            /* if ($ajaxedit) {
                $field_edit_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR .'edit');
                $form -> addElement('button', $field_edit_btn_name, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' =>self::$default_close_decorators,
                    'class' => 'ajax_edit_modulerelation btn',
                    'label' => '',
                    'title'=> self::getTranslator()->translate('Edit'),
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation -> getDependedTable(get_class($model)), ($iframe_edit?\Sl\Service\Helper::TO_EDIT_ACTION:\Sl\Service\Helper::POPUP_EDIT_ACTION)),
                    'data-type' => $relation -> getType(),
                    'data-relation' => self::setElementRelationName($relation->getName())
                )+
                
                ($iframe_edit?array('data-iframe'=>1):array())
                
                
                ); 
                
             
            } */

            $sort_order = false;

            if (isset($config->$field_name)) {
                if (isset($config->$field_name->sort_order)) {
                    $sort_order = $config->$field_name->sort_order;
                }
                if (isset($config->$field_name->label))
                    $form->getElement($field_names_name)->setLabel($config->$field_name->label)->setOptions(array('placeholder' => $config->$field_name->label, 'title' => $config->$field_name->label));
            }
            $sort_order = self::getSorterValue($sort_order, ($form instanceof \Sl_Form_SubForm));
            
            //$form -> getElement($field_name) -> setOrder(self::getSorterValue($sort_order));
            $form->getElement($field_names_name)->setOrder(self::getSorterValue($sort_order, false));
        }
        
        if ($try_fill) {

            $relation_data = $model->fetchRelated($relation_name);
            
            
            
            $form = self::setElementValue($form, $field_name, $relation_data);
            /*
            $form -> getElement($field_names_name) -> setValue(implode('; ', array_map(function($el) {
                return $el -> __toString();
            }, $relation_data)));

            if (!$readonly) {
                $form -> getElement($field_name) -> setValue(implode(';', array_map(function($el) {
                    return $el -> getId();
                }, $relation_data)));

                $form -> getElement($field_name) -> setOptions(array('data-name' => implode('; ', array_map(function($el) {
                        return $el -> __toString();
                    }, $relation_data))));
            }
            */
        }
        return $form;
    }

	/** Вставка пов'заного елемента як списку
	 * @param \Zend_Form $form - форма
	 * @param \Sl_Model_Abstract $model - об'єкт
	 * @param \Sl\Modulerelation\Modulerelation $relation - зв'язок
	 * @param $config - налаштування з ini файлу
	 * @param $try_fill - прапорець спроби заповнення
	 * @param $readonly - прапорець можливості редагування
	 * @return  \Zend_Form $form
	 * */
	public static function addRelationSelectElement(\Zend_Form $form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $config, $try_fill, $readonly = false, $ajaxcreate = false, array $class_prefix) {
        $relation_name = strtolower($relation->getName());

		$field_name = self::setElementRelationName($relation_name);
        $field_names_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX);

		$class_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, array_merge($class_prefix));

		if (!$readonly) {
            $element_type = in_array($relation->getType(), array(
				\Sl_Modulerelation_Manager::RELATION_MANY_TO_MANY,
				\Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY
			)) ? 'multiselect' : 'select';

            $form->addElement($element_type, $field_name, array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => self::$default_decorators,
				'id' => $field_name . ($ajaxcreate ? \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'ajax' : ''),
				'class' => $class_name,
			));

			$options = array();
			$options[null] = '-';

            $relation_model = $relation->getRelatedObject($model);

			/**
			 * @TODO Заменить fetchAll на что-то, что может отфильтровать по управляющим связям
			 */
        	if (!method_exists($relation_model, 'getName'))
                $objects = \Sl_Model_Factory::mapper($relation_model)->fetchAll(null, 'id');
                else {
                $objects = \Sl_Model_Factory::mapper($relation_model)->fetchAll(null, 'name');
            }
        
			
	
            
            
            $restrictions = \Sl\Module\Auth\Service\Restrictions::restrictions($model, $relation);
            
			foreach ($objects as $object) {
			    //TODO: переробити фільтрування на фільтр в запиті
                if (!count($restrictions) || in_array($object->getId(), $restrictions)) {
                    $options[$object->getId()] = '' . $object;
			    } 
			}

			$sort_order = false;

            if (isset($config->$field_name)) {
                if (isset($config->$field_name->sort_order)) {
                    $sort_order = $config->$field_name->sort_order;
				}

                if (isset($config->$field_name->label))
                    $form->getElement($field_name)->setLabel($config->$field_name->label);
			}
			$sort_order = self::getSorterValue($sort_order, false);
            $form->getElement($field_name)->setOrder($sort_order)->setMultiOptions($options);
            
            $form_name = strtolower('model_' . $model->findModelName() . '_form');
            
            $form_options = \Sl_Module_Manager::getInstance()->getCustomConfig($model->findModuleName(), 'forms', $form_name);
            
            if ((isset($form_options->$field_name) && isset($form_options->$field_name->required) && $form_options->$field_name->required)) {
                $form->getElement($field_name)->setRequired(true)->getDecorator('label')->setOption('requiredSuffix', self::REQUIRED_SUFFIX);
                $form->getElement($field_name)->setRequired(true)->getDecorator('label')->setOption('escape', false);
            }
		} else { //die('hello');
			// тільки для читання
			// TODO: відкрити за потреби
			/*
			 $form -> addElement('text', $field_names_name, array(
			 'disableLoadDefaultDecorators' => true,
			 'decorators' => self::$default_decorators,
			 'label' => $field_name,
			 'readonly' => 'readonly'
			 ));

			 $sort_order = false;

			 if (isset($config -> $field_name)) {
			 if (isset($config -> $field_name -> sort_order)) {
			 $sort_order = $config -> $field_name -> sort_order;

			 }
			 if (isset($config -> $field_name -> label))
			 $form -> getElement($field_names_name) -> setLabel($config -> $field_name -> label);

			 }

			 //$form -> getElement($field_name) -> setOrder(self::getSorterValue($sort_order));
			 $form -> getElement($field_names_name) -> setOrder(self::getSorterValue($sort_order));
			 */
		}

		if ($try_fill) {

            $relation_data = $model->fetchRelated($relation_name);

			if ($readonly) {/*
				 $form -> getElement($field_names_name) -> setValue( array_map(function($el) {
				 return $el -> getName();
				 }, $relation_data));
				 */
			}

			if (!$readonly) {
				$form = self::setElementValue($form, $field_name, $relation_data);
				/*
				$form -> getElement($field_name) -> setValue(implode(';', array_map(function($el) {
					return $el -> getId();
				}, $relation_data)));
				 */ 
			}
		}
		/*
		 foreach ($form->getElements() as $element){
		 $element->setOptions(array('data-sort'=>$element->getOrder()));
		 }
		 */
		return $form;
	}
	
    public static function setElementValue(\Zend_Form $form, $element_name, $value = null) {
        if ($value !== null) {
            if (strpos($element_name, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX) === 0 && is_array($value)) {
                $field_names_name = $element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX;
                if ($form->getElement($field_names_name)) {
                    $form->getElement($field_names_name)->setValue(implode('; ', array_map(function($el) {
                                                return $el->__toString();
					}, $value)));
				}
                if ($form->getElement($element_name)) {
				
                    $form->getElement($element_name)->setValue(implode(';', array_map(function($el) {
                                                return $el->getId();
						}, $value)));
		
                    $form->getElement($element_name)->setOptions(array('data-name' => implode('; ', array_map(function($el) {
                                            return $el->__toString();
						}, $value))));
				}
            } elseif (is_array($value)) {
                if (method_exists($form->getElement($element_name), 'setMultiOptions')) {
                    $form->getElement($element_name)->setMultiOptions($value);
                }
            } elseif ($form->getElement($element_name)) {
                $form->getElement($element_name)->setValue($value);
            }
                    
	}
        
	return $form;
    }
	
	public static function setElementRelationName($name) {
		return \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . strtolower($name);
	}

	protected static function sortFormElements(array $a, array $b) {
		return strnatcmp($a['sort_order'] . $a['label'], $b['sort_order'] . $b['label']);
	}

	protected static function newRelationElement($form, $element_name) {

	}
    
    protected static function _mapMemberTypeFieldType($member_type) {
        switch ($member_type) {
            case 'text':
            case 'date':
			case 'datetime':
            case 'timestamp':
                return 'text';
                break;
            default:
                return $member_type;
                break;
        }
    }

    public static function elementToString( $form, $element_name) {
		$string = '';
        if (strpos($element_name, \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX) === 0) {
            if ($element = $form->getDisplayGroup($element_name)) {
                $string .= '' . $element;
            } elseif ($element = $form->getDisplayGroup($element_name . '_gr')) {
                $string .= '' . $element;
            } else {   

                
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX)) {
                    $string .= '' . $element;
                }
                 
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'clear')) {
                    $string .= '' . $element;
                } 
                 
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'btn')) {
                    $string .= '' . $element;
                }
                
                 
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'edit')) {
                    $string .= '' . $element;
                }
				
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'm')) {
                    $string .= '' . $element;
                }
				
                if ($element = $form->getElement($element_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'create')) {
                    $string .= '' . $element;
                }
				
               
                
                if ($element = $form->getElement($element_name)) {
                    $string .= '' . $element;
                }
            }
		} else {
            if ($element = $form->getElement($element_name)) {
				$string = '' . $element;
			} else {
			    
			}
		}

		return $string;
	}

	public static function subformToString(\Zend_Form $form, $subform_name) {
            $string = '';

        if ($element = $form->getSubform($subform_name)) {
			$string .= '' . $element;
		}

		return $string;
	}

    public static function subformTabs(\Zend_Form $form, $subform) {
 //       echo "el".$form->getSubForm('tab_gr_1')->additional_data_gr->getElement('date_birthday')->getValue();
        $string = '';
        $tab = $form->getSubForm($subform);
        if ($tab) $string = '' . $tab;
        return $string;
    }

	public static function displaygroupToString(\Zend_Form $form, $displaygroup_name) {
		$string = '';

        if ($element = $form->getDisplayGroup($displaygroup_name)) {
			$string .= '' . $element;
        } elseif ($element = $form->getDisplayGroup($displaygroup_name . '_gr')) {
			$string .= '' . $element;
		} else {
			//print_r(array_keys($form -> getDisplayGroups()));
			//print_r(array_keys($form -> getSubForms()));
		}

		return $string;
	}
	
    protected static function _getRelationValidators($form_name, $field, $model, $relation_classname) {
            	
        $form_options = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName())->section('forms')->$form_name;
			
            $validators = array();
        if (isset($form_options->$field) && isset($form_options->$field->validators)) {
            	
            $val_data = $form_options->$field->validators->toArray();
				
            foreach ($val_data as $key => $validator_option) {
	                $val_name = '';
	                $val_options = array();
                if (is_string($validator_option)) {
	                    
	                    $val_name = $validator_option;
                } elseif (is_array($validator_option)) {
	                    $val_name = $key;
	                    $val_options = $validator_option;
	                }
					
                $validator = self::_buildValidatorObject($val_name, array('options' => $val_options, 'field' => $field, 'class_name' => $relation_classname));
                if ($validator) {
	                    $validators[] = $validator;
	                }
	            }
            }
			
			//print_r($validators);
            return $validators;
        }
	  
	 protected static function _buildValidatorObject($validator_name, $options = array()) {
        try {
            return \Sl\Validate\Validate::factory($validator_name, $options);
        } catch (Exception $e) {
            echo $e->getMessage() . "\r\n";
            return null;
        }
    }
    
    protected static function _getModelFormConfig(\Sl_Model_Abstract $model) {
        if (!isset(self::$_form_options[get_class($model)])) {
            $form_name = strtolower('model_' . $model->findModelName() . '_form');
            self::$_form_options[get_class($model)] = \Sl_Module_Manager::getInstance()
                                                            ->getModule($model->findModuleName())
                                                            ->section('forms')
                                                            ->$form_name;
        }
        return self::$_form_options[get_class($model)];
    }
    
    /**
     * 
     * @param \Sl\Form\Form $form Форма, куд буде добавлен элемент
     * @param \Sl_Model_Abstract $model Объект, для которого строится форма
     * @param \Sl\Modulerelation\Modulerelation $relation Связь, объекта с главным объектом
     * @param \Zend_Config $config Конфигурация основной формы
     * @param boolean $try_fill Попытаться заполнить форму
     * @param boolean $readonly Является ли элемент "только для чтения"
     * @param type $ajaxcreate Флаг формы быстрого создания объекта
     * @param type $ajaxedit Флаг формы быстрого редактирования объекта
     * @param type $class_prefix Префикс класса для калькуляторов
     * @return \Sl\Form\Form
     */
    protected static function _addToElementTab( $form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $config, $try_fill, $readonly = false, $ajaxcreate = false, $ajaxedit = false, $class_prefix = array(), $iframe_edit = false
    ) {

        // Название связи
        $relation_name = strtolower($relation->getName());

        \Sl_Service_Acl::setContext($model, 'form');

        $priv_edit = \Sl_Service_Acl::isAllowed(array($model, $relation_name), \Sl_Service_Acl::PRIVELEGE_UPDATE);
        /** @TODO Переделать на DisplayGroups */
        // Формируем имена для элементов
        // Основной (хранит Id)
        $field_name = self::setElementRelationName($relation_name);
        // Название списка
        $field_names_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX);
        // Кнопка списка
        $field_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'btn');
        // Кнопка быстрого создания
        $field_create_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'create');

        $prefixes = $class_prefix;
        $class_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);

        $prefixes = array_merge($class_prefix, array(self::RELATION_NAMES_SUFFIX));
        $class_names_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);

        $label = strtoupper($relation_name);
        $rel_conf_name = \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . $relation_name;
        if (isset($config->$rel_conf_name) && isset($config->$rel_conf_name->label)) {
            $label = $config->$rel_conf_name->label;
        }



//Постройка контента табов
        $dg = new \Sl\Form\DisplayGroup($field_name . '_gr', $form->getPluginLoader('decorator'), array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => array(
                array('FormElements'),
                array(array('rels' => ($config->$rel_conf_name->include_decorator ? $config->$rel_conf_name->include_decorator : 'FilesList')), array(
                        'id' => $field_name . '_list',
                        'placement' => 'append',
                        'data-name' => $relation->getName(),
                        'data-returnfields' => implode(',', $config->$rel_conf_name->show_field),
                    )),
                array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array('Label', array('placement' => 'prepend', 'class' => 'control-label optional')),
                array(array('mainWrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
            ),
            'label' => $label,
        ));



        $dg->addElement($form->createElement('hidden', $field_name, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => array(
                        'ViewHelper',
                    ),
                    'order' => 1,
                    'class' => $class_name,
        )));

        $dg->addElement($form->createElement('button', $field_btn_name, array(
                    'disableLoadDefaultDecorators' => true,
                    'decorators' => array('ViewHelper'),
                    'class' => 'set_modulerelation btn_tab tab_create',
                    'label' => '',
                    'title' => self::getTranslator()->translate('Select'),
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model))),
                    'data-type' => $relation->getType(),
                    'data-returnfields' => $config->$rel_conf_name->show_field ? implode(',', $config->$rel_conf_name->show_field->toArray()) : '',
                    'data-returnfields_label' => $config->$rel_conf_name->show_field_label ? implode(',', $config->$rel_conf_name->show_field_label->toArray()) : '',
                    'data-handling' => $relation->getHandling() ? 1 : 0,
                    'order' => 5,
        )));

        if ($ajaxcreate) {
            $dg->addElement($form->createElement('button', $field_create_btn_name, array(
                        'disableLoadDefaultDecorators' => true,
                        'decorators' => array(
                            'ViewHelper',
                        ),
                        'label' => '',
                        'order' => 10,
                        'class' => 'ajax_create_modulerelation btn_tab icon-plus',
                        'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model)), $iframe_edit ? \Sl\Service\Helper::CREATE_ACTION : \Sl\Service\Helper::POPUP_CREATE_ACTION),
                        'data-type' => $relation->getType(),
                        'data-returnfields' => $config->$rel_conf_name->show_field ? implode(',', $config->$rel_conf_name->show_field->toArray()) : '',
                        'data-returnfields_label' => $config->$rel_conf_name->show_field_label ? implode(',', $config->$rel_conf_name->show_field_label->toArray()) : '',
                        'data-iframe' => (int) $iframe_edit,
                        'data-relation' => $relation->getName()
            )));
        }

        $sort_order = false;
        $request_fields = $field_filters = array();
        if (isset($config->$field_name)) {
            if (isset($config->$field_name->sort_order)) {
                $sort_order = $config->$field_name->sort_order;
            }
            if (isset($config->$field_name->request_fields)) {
                $request_fields = $config->$field_name->request_fields->toArray();
            }
            if (isset($config->$field_name->field_filters)) {
                $field_filters = $config->$field_name->field_filters->toArray();
            }



            if (isset($config->$field_name->label)) {
                $dg->setLabel($config->$field_name->label);
            }
        }

        if (in_array($relation->getType(), array(
                    \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                    \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY,
                ))) {
            $vals = array('null');
            if ($model->getId()) {
                $vals[] = $model->getId();
            }
            $field_filters[] = array(
                'field' => $relation->getName() . ':id',
                'matching' => 'in',
                'value' => 'val:' . implode(',', $vals),
            );
        }

        if ($relation->getType() == \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE) {
            $vals = array('null');
            if ($model->getId()) {
                $vals[] = $model->getId();
            }
            $field_filters[] = array(
                'field' => $relation->getName() . ':id',
                'matching' => 'in',
                'value' => 'val:' . implode(',', $vals),
            );
        }
        $sort_order = self::getSorterValue($sort_order, false);
        $dg->setOrder($sort_order);

        $dg->getElement($field_name)->setOrder(2);
        $dg->getElement($field_btn_name)->setOrder(1);
        if ($dg->getElement($field_create_btn_name)) {
            $dg->getElement($field_create_btn_name)->setOrder(3);
        }


        if (count($request_fields)) {
            $dg->getElement($field_btn_name)->setOptions(array('data-request_fields' => $request_fields));
        }

        $iterator = 0;
        if (count($field_filters)) {

            foreach ($field_filters as $filter) {
                $i = $iterator++;
                $dg->getElement($field_btn_name)
                        ->setOptions(array(
                            'data-filter' . $i => implode('-', array(
                                $filter['field'],
                                $filter['matching'],
                                \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                                $filter['value']
                            ))
                ));
            }
        }

        if ($config->$field_name->required) {
            $dg->getElement($field_name)->setRequired(true);
        }

        $restrictions = \Sl\Module\Auth\Service\Restrictions::restrictions($model, $relation);

        if (count($restrictions)) {

            $i = $iterator++;
            if ($form->getElement($field_btn_name))
                $form->getElement($field_btn_name)->setOptions(array('data-filter' . $i => implode('-', array(
                        'id',
                        'in',
                        \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                        'val:' . implode(',', $restrictions),
                ))));
        }


        if ($readonly) {
            if ($dg->getElement($field_btn_name)) {
                $dg->removeElement($field_btn_name);
            }
            if ($dg->getElement($field_create_btn_name)) {
                $dg->removeElement($field_create_btn_name);
            }
            // тільки для читання
        }

        if ($try_fill) {
            $relation_data = $model->fetchRelated($relation_name);
            $values = array();
            $items = array();
            if ($relation_data) {

                foreach ($relation_data as $oData) {
                    $values[] = $oData->getId();
                    $items[] = array(
                        'id' => $oData->getId(),
                        'href' => \Sl\Service\Helper::modelEditViewUrl($oData),
                        'target' => '_blank',
                        'text' => $oData . '',
                        'object' => $oData,
                        'fields' => $config->$rel_conf_name->show_field,
                    );
                }
            }
            if ($dg->getElement($field_name)) {
                $dg->getElement($field_name)->setValue(implode(';', $values));
            }
            if ($dg->getDecorator('rels')) {
                $dg->getDecorator('rels')->setOption('items', $items);
                if ($config->$rel_conf_name->show_field_label)
                    $dg->getDecorator('rels')->setOption('field_label', $config->$rel_conf_name->show_field_label->toArray());
            }
        }
      
        if(!$form->getSubForm($config->$rel_conf_name->element_tab)){

        if($form instanceof \Sl_Form_SubForm){
            $subForm = new \Sl_Form_SubForm();            
            $subForm->addDecorator($config->$rel_conf_name->include_decorator_elemets.'subform');           
        }
        else {              
 
 
        $subForm = new \Sl_Form_SubForm(array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => array($config->$rel_conf_name->include_decorator_elemets),)
        );
        }
        
        }
        

        if ($form->getSubForm($config->$rel_conf_name->element_tab)) {
            $subForm = $form->getSubForm($config->$rel_conf_name->element_tab);
            $subForm -> addDisplayGroups(array($dg));
            
        } else {
            $subForm->addDisplayGroups(array($dg) );
            $form->addSubForm($subForm, $config->$rel_conf_name->element_tab);
        }

        return $form;
    }

    /**
     * 
     * @param \Sl\Form\Form $form Форма, куд буде добавлен элемент
     * @param \Sl_Model_Abstract $model Объект, для которого строится форма
     * @param \Sl\Modulerelation\Modulerelation $relation Связь, объекта с главным объектом
     * @param \Zend_Config $config Конфигурация основной формы
     * @param boolean $try_fill Попытаться заполнить форму
     * @param boolean $readonly Является ли элемент "только для чтения"
     * @param type $ajaxcreate Флаг формы быстрого создания объекта
     * @param type $ajaxedit Флаг формы быстрого редактирования объекта
     * @param type $class_prefix Префикс класса для калькуляторов
     * @return \Sl\Form\Form
     */
    protected static function _addToManyRelationElement(
    \Sl\Form\Form $form, \Sl_Model_Abstract $model, \Sl\Modulerelation\Modulerelation $relation, $config, $try_fill, $readonly = false, $ajaxcreate = false, $ajaxedit = false, $class_prefix = array(), $iframe_edit = false
    ) {
        // Название связи
		$relation_name = strtolower($relation->getName());
        
        \Sl_Service_Acl::setContext($model, 'form');
        
        $priv_edit = \Sl_Service_Acl::isAllowed(array(
            $model,
            $relation_name
        ), \Sl_Service_Acl::PRIVELEGE_UPDATE);
        /** @TODO Переделать на DisplayGroups */
        // Формируем имена для элементов
        // Основной (хранит Id)
        $field_name = self::setElementRelationName($relation_name);
        // Название списка
        $field_names_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . self::RELATION_NAMES_SUFFIX);
        // Кнопка списка
        $field_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'btn');
        // Кнопка быстрого создания
        $field_create_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . 'create');

        $prefixes = $class_prefix;
        $class_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);

        $prefixes = array_merge($class_prefix, array(self::RELATION_NAMES_SUFFIX));
        $class_names_name = implode(\Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR, $prefixes);

        $label = strtoupper($relation_name);
        $rel_conf_name = \Sl_Modulerelation_Manager::RELATION_FIELD_PREFIX . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR . $relation_name;
        if (isset($config->$rel_conf_name) && isset($config->$rel_conf_name->label)) {
            $label = $config->$rel_conf_name->label;
        }
       // die($config)
        $dg = new \Sl\Form\DisplayGroup($field_name . '_gr', $form->getPluginLoader('decorator'), array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => array(
                array('FormElements'),
                array(array('wrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'input-append input-prepend')),
                array(array('rels' => ($config->$rel_conf_name->include_decorator ? $config->$rel_conf_name->include_decorator : 'FilesList')), array(
                        'id' => $field_name . '_list',
                        'placement' => 'append',
                    'data-name' => $relation->getName(),
                    //'data-returnfields' => implode(',', $config->$rel_conf_name->show_field),
                )),
                array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'controls')),
                array('Label', array('placement' => 'prepend', 'class' => 'control-label optional')),
                array(array('mainWrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'control-group')),
            ),
            'label' => $label,
        ));
        
        
        
            $dg->addElement($form->createElement('hidden', $field_name, array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => array(
                    'ViewHelper',
                ),
                'order' => 1,
				'class' => $class_name,
			)));
            
            $dg->addElement($form->createElement('button', $field_btn_name, array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => array('ViewHelper'),
				'class' => 'set_modulerelation btn',
				'label' => '',
                                'title' => self::getTranslator()->translate('Select'),
                    'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model))),
                    'data-type' => $relation->getType(),
                                'data-returnfields' => $config->$rel_conf_name->show_field ? implode(',', $config->$rel_conf_name->show_field->toArray()) : '',
                                'data-returnfields_label' => $config->$rel_conf_name->show_field_label ? implode(',', $config->$rel_conf_name->show_field_label->toArray()) : '',
                    'data-handling' => $relation->getHandling() ? 1 : 0,
                'order' => 5,
			)));
			
			if ($ajaxcreate) {
				$dg->addElement($form->createElement('button', $field_create_btn_name, array(
					'disableLoadDefaultDecorators' => true,
                    'decorators' => array(
                        'ViewHelper',
                    ),
                    'label' => '',
                    'order' => 10,
                    'class' => 'ajax_create_modulerelation btn',
                        'data-rel' => \Sl\Service\Helper::popupUrl($relation->getDependedTable(get_class($model)), $iframe_edit ? \Sl\Service\Helper::CREATE_ACTION : \Sl\Service\Helper::POPUP_CREATE_ACTION),
                        'data-type' => $relation->getType(),
                    'data-returnfields' => $config->$rel_conf_name->show_field ? implode(',', $config->$rel_conf_name->show_field->toArray()) : '',
                    'data-returnfields_label' => $config->$rel_conf_name->show_field_label ? implode(',', $config->$rel_conf_name->show_field_label->toArray()) : '',
                        'data-iframe' => (int) $iframe_edit,
                        'data-relation' => $relation->getName()
				)));
			}

			$sort_order = false;
			$request_fields = $field_filters = array();
        if (isset($config->$field_name)) {
            if (isset($config->$field_name->sort_order)) {
                $sort_order = $config->$field_name->sort_order;
				}
            if (isset($config->$field_name->request_fields)) {
                $request_fields = $config->$field_name->request_fields->toArray();
				}
            if (isset($config->$field_name->field_filters)) {
                $field_filters = $config->$field_name->field_filters->toArray();
				}
                
                
                
            if (isset($config->$field_name->label)) {
                $dg->setLabel($config->$field_name->label);
                }
			}
            
        if (in_array($relation->getType(), array(
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_ONE,
                        \Sl_Modulerelation_Manager::RELATION_ONE_TO_MANY,
                ))) {
                         $vals = array('null');
            if ($model->getId()) {
                              $vals[] = $model->getId();
                         }    
            $field_filters[] = array(
                'field' => $relation->getName() . ':id',
                                'matching' => 'in',
                'value' => 'val:' . implode(',', $vals),
                              );  
                    }
            
        if ($relation->getType() == \Sl_Modulerelation_Manager::RELATION_MANY_TO_ONE) {
                $vals = array('null');
            if ($model->getId()) {
                    $vals[] = $model->getId();
                }
                $field_filters[] = array(
                'field' => $relation->getName() . ':id',
                    'matching' => 'in',
                'value' => 'val:' . implode(',', $vals),
                );
            }
			$sort_order = self::getSorterValue($sort_order, false);
            $dg->setOrder($sort_order);
        /* $form -> getElement($field_name) -> setOrder($sort_order-4);
			//$form -> getElement($field_names_name) -> setOrder($sort_order-2);
          $form -> getElement($field_btn_name) -> setOrder($sort_order-1); */
			
            //$dg -> getElement($field_name) -> setOrder($sort_order-4);
			//$form -> getElement($field_names_name) -> setOrder($sort_order-2);
			//$dg -> getElement($field_btn_name) -> setOrder($sort_order-1);
			
			
			
            $dg->getElement($field_name)->setOrder(2);
            $dg->getElement($field_btn_name)->setOrder(1);
        if ($dg->getElement($field_create_btn_name)) {
                $dg->getElement($field_create_btn_name)->setOrder(3);
            }
            
        /* if (false && $ajaxcreate) {
                $form -> getElement($field_create_btn_name) -> setOrder($sort_order);
			}
			*/
        if (count($request_fields)) {
            $dg->getElement($field_btn_name)->setOptions(array('data-request_fields' => $request_fields));
			}
            
            $iterator = 0;
            if (count($field_filters)) {
				
				foreach ($field_filters as $filter) {
					$i = $iterator++;
                $dg->getElement($field_btn_name)
                            ->setOptions(array(
                            'data-filter' . $i => implode('-', array(
                                        $filter['field'],
                                        $filter['matching'],
                                        \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                                        $filter['value']
                                    ))
                                ));
				}
			}
            
        if ($config->$field_name->required) {
                $dg->getElement($field_name)->setRequired(true);
            }
            
            $restrictions = \Sl\Module\Auth\Service\Restrictions::restrictions($model, $relation);
            
        if (count($restrictions)) {
                
                $i = $iterator++;
            if ($form->getElement($field_btn_name))
                $form->getElement($field_btn_name)->setOptions(array('data-filter' . $i => implode('-', array(
                            'id',
                            'in',
                            \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX,
                        'val:' . implode(',', $restrictions),
                        ))));
            }
            
            
		if ($readonly)  {
            if ($dg->getElement($field_btn_name)) {
                $dg->removeElement($field_btn_name);
            }
            if ($dg->getElement($field_create_btn_name)) {
                $dg->removeElement($field_create_btn_name);
            }
            // тільки для читання
			// TODO: винести в окремий метод
			/*
			$form -> addElement('hidden', $field_name, array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => self::$hidden_decorators,
				'class' => $class_name,
			));
			
			$form -> addElement('text', $field_names_name, array(
				'disableLoadDefaultDecorators' => true,
				'decorators' => $ajaxedit?self::$default_open_decorators: self::$default_decorators,
				'label' => $field_name,
				'readonly' => 'readonly',
				'class' => $class_names_name,
			));

			if ($ajaxedit) {
				
				$field_edit_btn_name = self::setElementRelationName($relation_name . \Sl_Modulerelation_Manager::RELATION_FIELD_SEPARATOR .'edit');
				
				$form -> addElement('button', $field_edit_btn_name, array(
					'disableLoadDefaultDecorators' => true,
					'decorators' => self::$default_close_decorators,
					'class' => 'ajax_edit_modulerelation btn',
					
					'data-rel' => \Sl\Service\Helper::popupUrl($relation -> getDependedTable(get_class($model)),  \Sl\Service\Helper::POPUP_EDIT_ACTION),
					'data-type' => $relation -> getType()
				));
				$form -> getElement($field_edit_btn_name) -> setLabel(self::getTranslator()->translate('Edit'));
			}

			$sort_order = false;

			if (isset($config -> $field_name)) {
				if (isset($config -> $field_name -> sort_order)) {
					$sort_order = $config -> $field_name -> sort_order;

				}
				if (isset($config -> $field_name -> label))
					$form -> getElement($field_names_name) -> setLabel($config -> $field_name -> label);

			}

			//$form -> getElement($field_name) -> setOrder(self::getSorterValue($sort_order));
			$form -> getElement($field_names_name) -> setOrder(self::getSorterValue($sort_order));
            */
		}

        if ($try_fill) {
            $relation_data = $model->fetchRelated($relation_name);
			$values = array();
            $items = array();
            if ($relation_data) {
                
                foreach ($relation_data as $oData) {
                    $values[] = $oData->getId();
                    $items[] = array(
                        'id' => $oData->getId(),
                        'href' => \Sl\Service\Helper::modelEditViewUrl($oData),
                        'target' => '_blank',
                        'text' => $oData . '',
                        'object' => $oData,
                        'fields' => $config->$rel_conf_name->show_field,
                    );
                }
            }
            if ($dg->getElement($field_name)) {
                $dg->getElement($field_name)->setValue(implode(';', $values));
            }
            if ($dg->getDecorator('rels')) {
                $dg->getDecorator('rels')->setOption('items', $items);
                if ($config->$rel_conf_name->show_field_label)
                     $dg->getDecorator('rels')->setOption('field_label', $config->$rel_conf_name->show_field_label->toArray());              
            }
		}
         
        //echo "\r\n\r\n".$dg."\r\n\r\n";
        $form->addDisplayGroups(array($dg));
        //print_r($form->getDisplayGroups());die;
        return $form;
    }
    
    public static function removeFileelements(\Sl\Form\Form $form) {
        $elements = $form->getElements();    
        foreach ($elements as $element) {
            if ($element instanceof \Zend_Form_Element_File) {
                $form->removeElement($element->getName());
            }
        }
    }
    
    public static function addPrintElements(\Sl\Form\Form $form, \Sl_Model_Abstract $model, $order, $custom_decorators = null) {
        //error_reporting(E_ALL);
        $printforms = \Sl_Model_Factory::mapper('printform', \Sl_Module_Manager::getInstance()->getModule('home'))
                            ->fetchAllByNameType(\Sl\Printer\Manager::type($model));
        if (count($printforms) == 0) {
            return $form;
        }
        $dg = new \Sl\Form\DisplayGroup('print', $form->getPluginLoader('decorator'), array(
            'disableLoadDefaultDecorators' => true,
            'decorators' => array(
                array(array('t1' => 'Text'), array('tag' => 'span', 'class' => 'caret')),
                array(array('t2' => 'Text'), array('tag' => 'span', 'content' => 'Распечатать&nbsp;&nbsp;&nbsp;', 'placement' => 'prepend')),
                array(array('t3' => 'Text'), array('tag' => 'button', 'class' => 'btn dropdown-toggle btn-info', 'data-toggle' => 'dropdown')),
                array(array('t4' => 'HtmlTag'), array('tag' => 'ul', 'class' => 'dropdown-menu', 'placement' => 'append', 'openOnly' => true)),
                array('FormElements', array('placement' => 'append')),
                array(array('t5' => 'HtmlTag'), array('tag' => 'ul', 'placement' => 'append', 'closeOnly' => true)),
                array(array('t6' => 'HtmlTag'), array('tag' => 'div', 'class' => 'btn-group dropup')),
            ),
            'order' => $order,
        ));
        
        if ($custom_decorators) {
            foreach ($custom_decorators as $k => $dec) {
                if (is_string($dec) && ($dec == 'ViewHelper')) {
                    unset($custom_decorators[$k]);
                }
            }
            $dg->addDecorators($custom_decorators);
        }
        
        foreach ($printforms as $printform) {
            $name = $model->findModuleName() . '_' . $model->findModelName() . '_pf_' . $printform->getId();
            $dg->addElement($form->createElement('hidden', $name, array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array(
                    array('ViewHelper'),
                            array(array('a' => 'Text'), array('tag' => 'a', 'href' => \Sl\Service\Helper::returnPrintUrl($model, $printform), 'content' => $printform->getDescription())),
                            array(array('li' => 'Text'), array('tag' => 'li')),
                ),
            )));
        }
        $form->addDisplayGroups(array($dg));
        return $form;
    }
    
    protected static function _getDefaultDecorators($type) {
        switch ($type) {
            case 'file':
                return array(
                    'File',
                    array(
                        array('div' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'controls')
                    ),
                    array(
                        'Label',
                        array('placement' => 'prepend', 'class' => 'control-label')
                    ),
                    array(
                        array('section' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'control-group')
                    )
                );
                break;
            case 'button':
                return array(
                    'ViewHelper',
                    array(
                        array('div' => 'Text'),
                        array('tag' => 'div',)
                    ),
                    array(
                        array('div' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'controls')
                    ),
                    array(
                        'Label',
                        array('placement' => 'prepend', 'class' => 'control-label')
                    ),
                    array(
                        array('section' => 'HtmlTag'),
                        array('tag' => 'div', 'class' => 'control-group')
                    )
                );
            default:
                return self::$default_decorators;
                break;
        }
    }

}
