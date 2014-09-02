<?

namespace Sl\Serializer;

class Serializer {

    const SCRIPT_VIEW_BASE_PATH = '/Sl/View/Scripts';
    const SCRIPT_LIST_VIEW_BASE_PATH = '/Sl/View/Scripts/List';
    const SCRIPT_FIELD_BASE_PATH = '/Sl/View/Scripts/Field';
    const SCRIPT_DETAILED_VIEW_BASE_PATH = '/Sl/View/Scripts/Detailed';
    const SCRIPT_CHART_VIEW_BASE_PATH = '/Sl/View/Scripts/Chart';
    const SCRIPT_JS_VIEW_BASE_PATH = '/Sl/View/Scripts/JS';
    const VIEW_MAX_LIST_RELATIONS = 3;
    const SORT_ORDER_INCREMENT = 10;
    const DT_FIELD_CHECKBOX_CHECKED = '+';
    const DT_FIELD_CHECKBOX_UNCHECKED = '-';
    const LISTVIEW_TR_ATTRIBUTES_KEY = 'attributesTR';
    
    protected static $_view;
    protected static $_fields_templates = array();

    /**
     * 
     * @param \Sl\Model\Identity\Field $field
     * @param string $context
     * @param string $subcontext
     * @return string
     * Рендер поля в залежності від контексту
     */
     
    public static function render(\Sl\Model\Identity\Field $field, $context, $value = null, array $options = array()) {
        $tpl = self::_getTplPath($field, $field->getFieldset()->getContextType(), $context);
        if($tpl) {
            return self::getView()->assign(array(
                'field' => $field,
                'options' => $options,
                'value' => $value
            ))->render($tpl);
        } else {
            return $value;
        }
    }

    /**
     * 
     * @return string
     * Рендер чекбокса для ListView
     */
     
    public static function renderListviewCheckbox($checked = false){
           $view = self::getView();
           $view->checked = $checked;
           
           $partial_path_tpl = '/Html/ListviewCheckbox.phtml';
           
           return $view -> render($partial_path_tpl);    
           
    }
    
    public static function renderListviewSelector($type = 0){
        return self::getView()->render('/Html/Listview'.($type?'Radio':'Checkbox').'.phtml');
    }
    
    /**
     * Пошук темплейта до поля в залежності від контекстів
     * @return string
     */
     
    protected static function _getTplPath(\Sl\Model\Identity\Field $field, $context, $subcontext){
        $search_data = array_map('lcfirst', array_unique(array_merge((array) $context, (array) $subcontext)));
        return self::_searchTplFile($field, $search_data);
        
    }
    
    /**
     * Ищем шаблон отображения
     * Путь примерно такой
     * <ul>
     *  <li><b>context</b>/subcontext1/subcontext2/subcontext3/.../<i>type</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/subcontext2/subcontext3/.../<i>default</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/subcontext2/subcontext3/<i>type</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/subcontext2/subcontext3/<i>default</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/subcontext2/<i>type</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/subcontext2/<i>default</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/<i>type</i>.phtml</li>
     *  <li><b>context</b>/subcontext1/<i>default</i>.phtml</li>
     *  <li><b>context</b>/<i>type</i>.phtml</li>
     *  <li><b>context</b>/<i>default</i>.phtml</li>
     *  <li><i>type</i>.phtml</li>
     *  <li><i>default</i>.phtml</li>
     * </ul>
     * @param \Sl\Model\Identity\Field $field
     * @param type $context
     * @param type $subcontext
     */
    protected static function _searchTplFile(\Sl\Model\Identity\Field $field, $subcontext) {
        $tpl = null;
        $counter = 0;
        
        $builder = function($prefix, $parts, $last, $def = 'default') {
            $t = implode(DIRECTORY_SEPARATOR, $parts).DIRECTORY_SEPARATOR.$last.'.phtml';
            $p = $prefix.DIRECTORY_SEPARATOR.$t;
            if(file_exists($p)) {
                return $t;
            }
            $t = implode(DIRECTORY_SEPARATOR, $parts).DIRECTORY_SEPARATOR.$def.'.phtml';
            $p = $prefix.DIRECTORY_SEPARATOR.$t;
            if(file_exists($p)) {
                return $t;
            }
            return null;
        };
        
        $prefix = LIBRARY_PATH.self::SCRIPT_FIELD_BASE_PATH;
        
        while(is_null($tpl) && (count($subcontext) > 0)) {
            if(count($subcontext)) {
                $parts[] = implode(DIRECTORY_SEPARATOR, $subcontext);
            } else {
                $parts = array();
            }
            $tpl = $builder($prefix, $parts, $field->getType());
            array_pop($subcontext);
            $counter++;
        }
        if(!$tpl) {
            return $builder($prefix, array(), $field->getType());
        }
        return $tpl;
    }
    
    protected static function _findTplFileRecursive(array $params){
        
        if (isset(self::$_fields_templates[implode(':',$params)])){
            return self::$_fields_templates[implode(':',$params)];
        } else {
            $path = implode('/',array_merge(array(LIBRARY_PATH.self::SCRIPT_FIELD_BASE_PATH),$params)).'.phtml';
            if (file_exists($path)){
                self::$_fields_templates[implode(':',$params)] = implode('/',$params).'.phtml';
            } elseif(count($params) > 1) {
                array_pop($params);
                self::$_fields_templates[implode(':',$params)] = self::_findTplFileRecursive($params);
            } else {
                 self::$_fields_templates[implode(':',$params)] = false;
                                     
            }
            return self::$_fields_templates[implode(':',$params)];            
        }
    }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
    
    /**
     * 
     * @param array() $options
     * @return array()
     * На вході бере масив списку опцій для model_order_form, сортує його по ключу - значенню поля 'sort_order'.
     * Повертає впорядкований в порядку зростання ключа 'sort_order' масив опцій  для зручного послідовного опрацювання
     */
          
    public static function orderFormOptions($options = array()) {
        $max_order = 0;
        $copy_options = $options;
        $temp_order = array();
        $add_key = 0;
        foreach ($copy_options as $key => $value) {
            $add_key = $add_key + 1;
            if ($value['type'] == 'hidden') {
                unset($options[$key]);
            }
            if (isset($value['sort_order'])) {
                $temp_order[$value['sort_order']][$add_key] = array('name' => $key, 'label' => $value['label']);
                if ($value['type'] == 'checkbox') {
                    $temp_order[$value['sort_order']][$add_key]['checkbox'] = '1';
                }
                $max_order = max($value['sort_order'], $max_order);
            } else {
                $temp_order[0][$add_key] = array('name' => $key, 'label' => $value['label']);
                if ($value['type'] == 'checkbox') {
                    $temp_order[0][$add_key]['checkbox'] = '1';
                }
            }
        }
        //var_dump($max_order);die;
        $max_order = $max_order + 1;
        $temp_order[$max_order] = $temp_order[0];
        unset($temp_order[0]);
        ksort($temp_order);

        $add_key = 0;
        foreach ($temp_order as $ord => $arr) {
            foreach ($arr as $field => $value) {
                $add_key = $add_key + 1;
                $ordered_form_options[$add_key] = $value;
                $ordered_form_options[$add_key]['label'] = is_array($value['label']) ? current($value['label']) : $value['label'];
            }
        }
        $max_order=0; 
        return $ordered_form_options;
    }
    /**
     * 
     * @param \Sl_Model_Abstract $object
     * @param type $config
     * @return type
     * На вході отримує об'єкт і, як опцію, конфіг виводу в дітейлед. Якщо отримує конфіг на вході - виводить дані по ньому,
     * в інакшому випадку шукає відповідний конфіг в module.php в розділі detailed. Якщо не знаходить - записує до конфігу дані 
     * з налаштувань module, а якщо там нема - з налаштувань model. Отриманий результат мерджить із налаштуванням моделі, сортує по
     *  "sort_order" і повертає отриманий масив. 
     * Прошу вибачення за неоптимізований код. Мачехін І.О. 
     * 
     */        
    
    public static function getDetailedOptions(\Sl_Model_Abstract $object, $config = null) {
            
        if (isset($config)) {
            $form_options = self::orderFormOptions($config);
        } else {
            $model = \Sl_Model_Factory::object($object);
            
            $config_options = \Sl_Module_Manager::getInstance()
                                    ->getCustomConfig($model->findModuleName(),'detailed');
            
            if(!$config_options){ 
            $config_options = \Sl_Module_Manager::getInstance()
                    ->getModule($model->findModuleName())
            ->generateDetailedOptions();} 
            $config_options = \Sl_Module_Manager::getInstance()
                                    ->getCustomConfig($model->findModuleName(),'detailed', $model->findModelName());
            
            
            if(!$config_options) {
                    $config_options = \Sl_Module_Manager::getInstance()
                                        ->getModule($model -> findModuleName())
            ->generateDetailedOptions($model);}
            
            $config_options = \Sl_Module_Manager::getInstance()
                                    ->getCustomConfig($model->findModuleName(),'detailed', $model->findModelName());  
                
            $config_options=$config_options->toArray();
            $form_model_options = $object->describeFields(); 
            $copy_form_model_options = $form_model_options;
            $copy_config_options = $config_options; 
            foreach ($copy_form_model_options as $key => $value) {//видаляємо поле sort_order з налаштувань моделі
                if (isset($value['sort_order'])) {                //якщо такі є в налаштуваннях модуля
                    unset($form_model_options[$key]['sort_order']);
                }
            }
            $form_options = array_merge_recursive($config_options, $form_model_options);
            $form_options = self::orderFormOptions($form_options);
        }   
        return $form_options;
    }

    /**
     * 
     * @param \Sl_Model_Abstract $object
     * @param type $form_options
     * @return array
     * На вході бере поточний об"єкт і впорядкований по зростанню параметра sort_order масив опцій.
     * Повертає масив заповнених даних по ключу назви поля/зв"язку, впорядкований по порядку sort_order вхідного 
     * масиву опцій а також вкладених об'єктів по зв'язку типа ITEMOWNER із наповненими зв'язками першого рівня.
     */
    public static function prepareData(\Sl_Model_Abstract $object, $form_options = array(), $processing = FALSE) {

        $relations = \Sl_Modulerelation_Manager::getRelations($object);
        $relations_array = array_keys($relations); 
        $relation_names = array();
        $copy_form_options = $form_options; //print_r($form_options);die;
        foreach ($copy_form_options as $k => $v) {
            if (preg_match('/modulerelation_(.+)/', $v['name'], $matches)) {
                $priv_read = \Sl_Service_Acl::isAllowed(array($object, $matches[1]), \Sl_Service_Acl::PRIVELEGE_READ);
                if ($priv_read) {
                    $relation_names[] = $matches[1];
                }
            } else {
                $priv_read = \Sl_Service_Acl::isAllowed(array($object, $v['name']), \Sl_Service_Acl::PRIVELEGE_READ);
                if (!$priv_read) {
                    unset($form_options[$k]);
                }
            }
        }
        
        //print_r(array($relation_names,$relations_array));
        $relation_names = array_intersect($relations_array, $relation_names);
        if (count($relation_names)) $object = \Sl_Model_Factory::mapper($object)->findAllowExtended($object, $relation_names);
           // print_r($relations_array); die;
        $relations_array = $object->fetchRelated();
//print_r($relations_array); die;
        foreach ($form_options as $array) {
            if (preg_match('/modulerelation_(.+)/', $array['name'], $matches)) {
                 $prepared_data[$matches[1]]['label'] = $array['label'];
                
                $prepared_data[$matches[1]]['class_name'] = $object->findModelName() . '-' . $matches[1];

                foreach ($relations_array[($matches[1])] as $it => $Obj) {
                    $prepared_data[$matches[1]]['value'][$it] = $Obj->__toString();
                }
            } else {
                $prepared_data[$array['name']]['label'] = $array['label'];
                
                $prepared_data[$array['name']]['value'] = $object->Lists($array['name']);
                if (isset($array['checkbox'])) {
                    if ($prepared_data[$array['name']]['value'] == '0') {
                        $prepared_data[$array['name']]['value'] = '-';
                    } else {
                        $prepared_data[$array['name']]['value'] = '+';
                    }
                }
                $prepared_data[$array['name']]['class_name'] = $object->findModelName() . '-' . $array['name'];
            }
        } 
        if ($processing == FALSE) { 
            foreach ($relations_array as $key => $value) { 

                $rl = \Sl_Modulerelation_Manager::getRelations($object, $key);
                if ($rl->getType() == \Sl_Modulerelation_Manager::RELATION_ITEM_OWNER) {
                    $items = $object->fetchRelated($key);
                    foreach ($items as $obj) {
                        $obj = \Sl_Model_Factory::mapper($obj)->findAllowExtended($obj->getId());
                        $item_owner[$key] = $items;
                    } //print_r($item_owner); die;
                }
            }
            foreach ($item_owner as $rel => $arr) {
                $detailed_data = array();
                foreach ($arr as $key => $ob) {
                    $form_options=self::getDetailedOptions($ob, $config);

                    $detailed_data[$rel][$key] = self::prepareData($ob, $form_options, $processing = TRUE);
                }   
           
            foreach ($detailed_data as $name => $data) {
                $prepared_data[$name]['value'] = $detailed_data[$name];
                $prepared_data[$name]['itemowner'] = '1';
            }
        }
 }


        return $prepared_data;
    }

    public static function getDetailedTemplate(\Sl_Model_Abstract $object, $config =null) {
        $view = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_DETAILED_VIEW_BASE_PATH));
        $form_options=self::getDetailedOptions($object, $config);
        $data = self::prepareData($object, $form_options, $processing);
        $view->data = $data;
        return $view->render('table.php');
    }
      
        public static function getChartTemplate($titles, $data, $chartType){
           $view = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_CHART_VIEW_BASE_PATH));
           $view->chartType = $chartType;// 'ColumnChart';
           $view->data = $data;
           $view->titles = $titles;
          return $view->render('chart.phtml');
           
           
           
    }
 
    /**
     * Возвращает шаблон страницы для заполнения c помощьью AJAX (jQuery DataTables Plugin)
     * 
     * @param array $models
     * @param type $fields
     */
    public static function getDTTemplate(\Sl_Model_Abstract $object, \Sl\Model\Identity\Identity $identity = null, $check_type = false, array $selected = array(), array $filter_fields = array(), array $calculators = array(), array $customs = array(), $is_popup = false, $is_iframe = false, $returnfields = false) {
        if (is_null($identity)) {
            $identity = \Sl_Model_Factory::identity($object);
        }

        if ($calculators) {
            $identity->setCurrentCalculator(array_shift($calculators));
        }

        $translator = \Zend_Registry::get('Zend_Translate');
        //print_r($identity->getObjectFields(true));die;

        $fields = $identity->getCalculatedObjectFields(true, true, true);
        //print_r(array_map(function($el){ return $el['name'].' : '.$el['sort_order']; }, $fields));die;
        //print_r($fields);die;
        // print_r($identity->getCalculatedObjectFields(true, true, true));die;
        //////
        //print_r($fields);die;
        foreach ($fields as &$field) {
            $field['column_name'] = $field['name'];
            $field['tr_name'] = $translator->translate($field['label']);
        }

        $table = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_LIST_VIEW_BASE_PATH));
        $table->fields = $fields;

        $table->rights = array(
            'detailed' => array(
                'access' => \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'detailed'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => 'home',
                    'controller' => 'controller',
                    'action' => 'detailed'))
            ),
            'create' => array(
                'access' => \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'create'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => $object->findModuleName(),
                    'controller' => $object->findModelName(),
                    'action' => 'create'))
            ),
            'edit' => array(
                'access' => \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'edit'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => 'home',
                    'controller' => 'controller',
                    'action' => 'edit'))
            ),
            
            'ajaxdelete' => array(
                'access' => \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'ajaxdelete'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => 'home',
                    'controller' => 'controller',
                    'action' => 'ajaxdelete'))
            ),
            'export' => array(
                'access' => \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'export'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => 'home',
                    'controller' => 'controller',
                    'action' => 'export'))
            ),
           
            'ajaxarchive' => array(
                'access' =>   \Sl_Service_Acl::isAllowed(
                        \Sl_Service_Acl::joinResourceName(array(
                            'type' => \Sl_Service_Acl::RES_TYPE_MVC,
                            'module' => $object->findModuleName(),
                            'controller' => $object->findModelName(),
                            'action' => 'ajaxarchive'
                                ), \Sl_Service_Acl::PRIVELEGE_ACCESS)
                ),
                'base_url' => $table->url(array(
                    'module' => 'home',
                    'controller' => 'controller',
                    'action' => 'ajaxarchive'))
            ),
        );

        //$table->context_menu = $object->contextMenu('list_item');

        $table->check_type = $check_type;
        $table->is_popup = $is_popup;
        $table->is_iframe = $is_iframe;
        $table->filter_fields = $filter_fields;
        $table->returnfields = $returnfields;

        $table->selected_data = $selected;
        $table->customs = $customs;
        $table->handling = $identity->findHandling();
        $table->calcs_data = $identity->getCalculator() ? get_class($identity->getCalculator()) : '';
        $table->request_data = array_map(function($el) {
                    return $el['name'];
                }, $identity->getCalculatedObjectFields(true, true, true));
        //$table->request_data = $identity->getCalculatedObjectFields(true);
        $table->export_entry_point = \Sl\Service\Helper::exportListUrl($object);
        $table->selected_items_entry_point = \Sl\Service\Helper::selectedItemsListUrl($object);
        $table->ajax_base_url = \Sl\Service\Helper::ajaxListUrl($object);
        $table->selected_strings_url = implode('/', array('', $object->findModuleName(), $object->findModelName(), 'ajaxdetailed'));
        $table->base_object = $object;
        return $table->render('dt/table.php');
    }

    public static function getCalculatorsJS(\Sl_Model_Abstract $model) {

        $Calculator_view = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_JS_VIEW_BASE_PATH));
        $calculator_fields = \Sl_Calculator_Manager::getFieldsCalculator($model);
        $Calculator_view->model_name = $model->findModelName();
        $Calculator_view->field_prefix = \Sl_Calculator_Manager::CALCULATOR_CLASS_PREFIX;
        $Calculator_view->separator = \Sl_Calculator_Manager::CALCULATOR_CLASS_SEPARATOR;
        $Calculator_view->fields = $calculator_fields;
        
        return $Calculator_view->render('calculator_init.php');
    }

    public static function getDtFieldTemplate(\Sl_Model_Abstract $model, $field, $value = null, $aggregated_columns = array(), $field_array = array(), $raw_data = false) {
        return self::quickRender($model, $field, $value, $aggregated_columns, $field_array, $raw_data);
        /*
        if (!in_array($field, $aggregated_columns)) {

            $field_description = $model->describeField($field);
        }

        switch ($field_description['type']) {
            case 'checkbox':
                $value = $value ? self::DT_FIELD_CHECKBOX_CHECKED : self::DT_FIELD_CHECKBOX_UNCHECKED;
                break;
        }
        //$field_tpl = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_LIST_VIEW_BASE_PATH));
        $field_tpl = self::getView();
        $field_tpl->description = $field_description;
        $field_tpl->class = 'dt-' . $model->findModelName() . '-' . $field;
        $field_tpl->name = $field;
        $field_tpl->field_data = $field_array;
        if (!is_null($value)) {
            $field_tpl->list_value = $model->Lists($field, $value);
        } else {
            $field_tpl->list_value = $value;
        }
        
        if ($list_name=$model->ListsAssociations($field)){
            $field_tpl->control_field = '1';
            
            $field_tpl->control_value = $value;
        } else {
         
        }
        
        
        return $field_tpl->render('dt/field.php');*/
    }

    public static function getDtArchiveFieldTemplate(\Sl_Model_Abstract $model, $field, $value = null, $aggregated_columns = array(), $field_array = array()) {
        $field_tpl = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_LIST_VIEW_BASE_PATH));
        $field_tpl->url = \Sl\Service\Helper::ajaxarchiveUrl($model);
        $field_tpl->archived = (int) $model->getArchived();
        return $field_tpl->render('dt/archive.field.php');
    }
    
    public static function getDtDeleteFieldTemplate(\Sl_Model_Abstract $model, $field, $value = null, $aggregated_columns = array(), $field_array = array()) {
        $field_tpl = new \Sl_View(array('scriptPath' => LIBRARY_PATH . self::SCRIPT_LIST_VIEW_BASE_PATH));
        $field_tpl->url = \Sl\Service\Helper::ajaxdeleteUrl($model);
        return $field_tpl->render('dt/delete.field.php');
    }
    
    /**
     * 
     * @return \Sl_View
     */
    public static function getView() {
        $scriptPath = array(LIBRARY_PATH . self::SCRIPT_LIST_VIEW_BASE_PATH, 
                            LIBRARY_PATH . self::SCRIPT_FIELD_BASE_PATH);
        if(!isset(self::$_view)) {
            self::$_view = new \Sl_View(array('scriptPath' => $scriptPath));
        }
        return self::$_view;
    }
    
    public static function quickRender(\Sl_Model_Abstract $model, $field, $value = null, $aggregated_columns = array(), $field_array = array(), $raw_data = false) {
        if (!in_array($field, $aggregated_columns)) {
            $field_description = $model->describeField($field);
        }

        switch ($field_description['type']) {
            case 'checkbox':
                $value = $value ? self::DT_FIELD_CHECKBOX_CHECKED : self::DT_FIELD_CHECKBOX_UNCHECKED;
                break;
        }
        
        $class = 'dt-' . $model->findModelName() . '-' . $field;
        
        if (!is_null($value)) {
            $list_value = $model->Lists($field, $value);
        } else {
            $list_value = $value;
        }
        
        $control_field = false;
        $config = $model->describeFields();
        if ($model->ListsAssociations($field) || $field_array['lists_control']){
            $control_field = '1';
        } 
        
        $html = (bool) $field_array['html'];
        if(preg_match('/htmlify/', $value)) {
            $html = true;
        }
        if($raw_data) {
            $html = true;
        }
        //return $value;
        
        $styles = array();
        
        if($field_array['type'] == 'date') {
            $styles[] = 'white-space: nowrap';
        }
        $limit = 40;
	//if($field == 'description') { $limit = 60; }	
        $result = $html?'':'<span style="'.implode(' ', $styles).'" '.($class?' class="'.$class.'"':'').' '.(mb_strlen($list_value,'utf-8') > $limit?'title="'.strip_tags($list_value).'"':'').' data-field="'.$field.'"'.'  '.($control_field?' data-lists="'.$field.'_'.$value.'"':'').' '.($model->getArchived()?' data-archived="1" ':'').' >';
        $result .= ((!$html && mb_strlen($list_value,'utf-8') > $limit)?mb_substr($list_value,0,$limit,'utf-8').'…':$list_value);
        $result .= $html?'':'</span>';
        
        return $result;
    }
    
    public static function renderFieldComparison($comp, $type) {
        // Нужно отрендерить сравнение.
        // Отрендерить нужно все варианты
        /**
         * Пример:
         *  between.
         *  Нужно найти что-то вроде between.phtml  - просто от - до
         *  Проверить нет ли between-date.phtml     - от даты - до даты
         *  Проверить нет ли between_fixed.phtml    - последние
         */
        if(!is_array($comp)) {
            $type = $comp;
            $comp = array($type);
        }
        $html = array();
        foreach($comp as $subtype) {
            $partial_name_tpl = 'field/comparison/%NAME%.phtml';
            $partial_path_tpl = LIBRARY_PATH.self::SCRIPT_LIST_VIEW_BASE_PATH.'/'.$partial_name_tpl;
            foreach(\Sl\Model\Identity\Field\Factory::getAvailableTypes() as $field_type) {
                list($name, $path) = str_replace('%NAME%', $subtype.'-'.$field_type, array($partial_name_tpl, $partial_path_tpl));
                if(file_exists($path)) {
                    $html[] = self::getView()->partial($name, array(
                        'type' => $subtype.'-'.$field_type,
                    ));
                }
            }
            list($name, $path) = str_replace('%NAME%', $subtype, array($partial_name_tpl, $partial_path_tpl));
            if(file_exists($path)) {
                $html[] = self::getView()->partial($name, array(
                    'type' => $subtype
                ));
            } else {
                list($name, $path) = str_replace('%NAME%', 'default', array($partial_name_tpl, $partial_path_tpl));
                $html[] = self::getView()->partial($name, array(
                    'type' => $subtype
                ));
            }
        }
        return implode(PHP_EOL, $html);
    }
}
