<?php

namespace Sl\Service;

use Sl_Model_Factory as ModelFactory;

use Sl_Model_Abstract as AbstractModel;
use Sl_Module_Abstract as AbstractModele;

use Sl_Module_Manager as ModuleManager;

use Exception;

class ClassCreator {

    protected static $create_table_query = 'CREATE TABLE IF NOT EXISTS [TABLE_NAME]  (
									`id` int(11) NOT NULL AUTO_INCREMENT,
									`active` tinyint(4) NOT NULL DEFAULT \'1\',
                                                                        `archived` tinyint(4) NULL DEFAULT \'0\',
									`create` timestamp NULL DEFAULT NULL,
									`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                                                        `extend` TEXT NOT NULL DEFAULT \'\',
									[FIELDS]
									PRIMARY KEY (`id`), INDEX(`archived`), INDEX(`active`)
									) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
    protected static $update_table_query = 'ALTER TABLE [TABLE_NAME] [FIELDS];';
    protected static $update_query_file = '/../db/patch.sql';
    protected static $create_relation_table_query = 'CREATE TABLE IF NOT EXISTS [TABLE_NAME]  (
									`id` int(11) NOT NULL AUTO_INCREMENT,
									`[FIELD_ID]` int(11) NOT NULL,
									`[TAGET_FIELD_ID]` int(11) NOT NULL,
									`create` timestamp NULL DEFAULT NULL,
									`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
									PRIMARY KEY (`id`),
									KEY `mr_[TABLE_NAME]_index_[FIELD_ID]` (`[FIELD_ID]`),
  									KEY `mr_[TABLE_NAME]_index_[TAGET_FIELD_ID]` (`[TAGET_FIELD_ID]`)
									) ENGINE = MYISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

    public static function createIdentity(\Sl_Module_Abstract $module, $classname, \Sl_Model_Abstract $inherit_from = null) {
        if (is_string($classname)) {

            $classname = ucfirst(strtolower($classname));

            $module_type = $module->getType('\\');
            $module_dir = APPLICATION_PATH . '/' . $module->getDir();
            //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Model';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }
                if (is_dir($model_dir)) {
                    $identity_dir = $model_dir . '/Identity';
                    if (!is_dir($identity_dir)) {
                        self::createDir($identity_dir);
                    }
                    if (is_dir($identity_dir)) {
                        $filename = $identity_dir . '/' . $classname . '.php';
                        $fh = fopen($filename, 'w+');
                        if ($fh) {
                            $tpl = preg_replace(array(
                                '/%ModuleType%/',
                                '/%Module%/',
                                '/%Class%/',
                                    ), array(
                                $module_type,
                                ucfirst($module->getName()),
                                $classname,
                                    ), self::getTemplate('identity', $inherit_from));
                            fwrite($fh, $tpl);
                            chmod($filename, 0777);
                            fclose($fh);
                        } else {
                            die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                        }
                    } else {
                        die('is_dir($identity_dir)');
                    }
                } else {
                    die('is_dir($model_dir)');
                }
            } else {
                echo $module_dir;
                die('is_dir($module_dir)');
            }
        }
    }

    public static function createModulerelation(\Sl_Module_Abstract $module, $classname) {
        if (is_string($classname)) {
            $classname = ucfirst(strtolower($classname));
            $module_type = $module->getType('\\');

            $module_dir = APPLICATION_PATH . '/' . $module->getDir();
            ;
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Modulerelation';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }
                if (is_dir($model_dir)) {

                    $filename = $model_dir . '/' . $classname . '.php';
                    $fh = fopen($filename, 'w+');
                    if ($fh) {
                        $tpl = preg_replace(array(
                            '/%ModuleType%/',
                            '/%Module%/',
                            '/%Class%/',
                                ), array(
                            $module_type,
                            ucfirst(strtolower($module->getName())),
                            $classname,
                                ), self::getTemplate('modulerelation'));
                        fwrite($fh, $tpl);
                        chmod($filename, 0777);
                        fclose($fh);
                    } else {
                        die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                    }
                } else {
                    die(' is_dir($model_dir) ');
                }
            } else {
                echo $module_dir;
                die('is_dir($module_dir)');
            }
        }
    }

    public static function createMysqlModulerelationTable($table_name, $id_name, $target_id_name) {

        $fields_string = implode(PHP_EOL, $fields_arr);
        $query = str_replace('[TABLE_NAME]', $table_name, str_replace('[FIELD_ID]', $id_name, str_replace('[TAGET_FIELD_ID]', $target_id_name, self::$create_relation_table_query)));

        $module = \Sl_Module_Manager::getInstance()->getModule('home');
        $db_table = \Sl_Model_Factory::dbTable('City', $module);

        $db_adapter = $db_table->getAdapter();
        $db_adapter->query($query);
    }

    public static function createModulerelationDbTable(\Sl_Module_Abstract $module, $classname, \Sl_Module_Abstract $source_module, $source_model, \Sl_Module_Abstract $target_module, $target_model, $table_name, $field_id, $target_field_id) {
        if (is_string($classname)) {

            $classname = ucfirst(strtolower($classname));

            $module_type = $module->getType('\\');

            $module_dir = APPLICATION_PATH . '/' . $module->getDir();
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Modulerelation';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }

                if (is_dir($model_dir)) {
                    $table_dir = $model_dir . '/Table';
                    if (!is_dir($table_dir)) {
                        self::createDir($table_dir);
                    }
                    if (is_dir($table_dir)) {
                        $filename = $table_dir . '/' . $classname . '.php';
                        $fh = fopen($filename, 'w+');
                        if ($fh) {
                            $tpl = self::getTemplate('modulerelation_table');
                            if ($name_array[2] == ucfirst(strtolower($target_module)) && $source_model == $target_model) {
                                $tpl = str_replace("Sl\Module\%TargetModule%\Model\%TargetModel%", \Sl_Modulerelation_Manager::SELFRELATION_PREFIX, self::getTemplate('modulerelation_table'));
                            }

                            $tpl = preg_replace(array(
                                '/%SourceModuleType%/',
                                '/%TargetModuleType%/',
                                '/%ModuleType%/',
                                '/%Module%/',
                                '/%Class%/',
                                '/%SourceModel%/',
                                '/%SourceModule%/',
                                '/%TargetModule%/',
                                '/%TargetModel%/',
                                '/%table%/',
                                '/%id_target_field%/',
                                '/%id_field%/',
                                    ), array(
                                $source_module->getType('\\'),
                                $target_module->getType('\\'),
                                $module_type,
                                ucfirst($module->getName()),
                                $classname,
                                ucfirst(strtolower($source_model)),
                                ucfirst(strtolower($source_module->getName())),
                                ucfirst(strtolower($target_module->getName())),
                                ucfirst(strtolower($target_model)),
                                $table_name,
                                $target_field_id,
                                $field_id,
                                    ), $tpl);
                            fwrite($fh, $tpl);
                            chmod($filename, 0777);
                            fclose($fh);
                        } else {
                            die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                        }
                    } else {
                        die("is_dir({$table_dir})");
                    }
                } else {
                    die("is_dir({$model_dir})");
                }
            } else {
                echo $module_dir;
                die("is_dir({$module_dir})");
            }
        }
    }

    public static function createDbTable(\Sl_Module_Abstract $module, $classname, $table_name, \Sl_Model_Abstract $inherit_from = null) {
        if (is_string($classname)) {
            $classname = ucfirst(strtolower($classname));

            $module_type = $module->getType('\\');
            $module_dir = APPLICATION_PATH . '/' . $module->getDir();

            //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Model';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }
                if (is_dir($model_dir)) {
                    $table_dir = $model_dir . '/Table';
                    if (!is_dir($table_dir)) {
                        self::createDir($table_dir);
                    }
                    if (is_dir($table_dir)) {
                        $filename = $table_dir . '/' . $classname . '.php';
                        $fh = fopen($filename, 'w+');
                        if ($fh) {
                            $tpl = preg_replace(array(
                                '/%ModuleType%/',
                                '/%Module%/',
                                '/%Class%/',
                                '/%table%/',
                                    ), array(
                                $module_type,
                                ucfirst($module->getName()),
                                $classname,
                                $table_name
                                    ), self::getTemplate('table', $inherit_from));
                            fwrite($fh, $tpl);
                            chmod($filename, 0777);
                            fclose($fh);
                        } else {
                            die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                        }
                    } else {
                        die("is_dir({$table_dir})");
                    }
                } else {
                    die("is_dir({$model_dir})");
                }
            } else {
                echo $module_dir;
                die("is_dir({$module_dir})");
            }
        }
    }

    public static function createMapper(\Sl_Module_Abstract $module, $classname, \Sl_Model_Abstract $inherit_from = null) {
        if (is_string($classname)) {

            $classname = ucfirst(strtolower($classname));
            $module_type = $module->getType('\\');
            $module_dir = APPLICATION_PATH . '/' . $module->getDir();

            //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Model';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }
                if (is_dir($model_dir)) {
                    $mapper_dir = $model_dir . '/Mapper';
                    if (!is_dir($mapper_dir)) {
                        self::createDir($mapper_dir);
                    }
                    if (is_dir($mapper_dir)) {
                        $filename = $mapper_dir . '/' . $classname . '.php';
                        $fh = fopen($filename, 'w+');
                        if ($fh) {
                            $tpl = preg_replace(array(
                                '/%ModuleType%/',
                                '/%Module%/',
                                '/%Class%/',
                                    ), array(
                                $module_type,
                                ucfirst($module->getName()),
                                $classname,
                                    ), self::getTemplate('mapper', $inherit_from));
                            fwrite($fh, $tpl);
                            chmod($filename, 0777);
                            fclose($fh);
                        } else {
                            die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                        }
                    } else {
                        die("is_dir({$mapper_dir})");
                    }
                } else {
                    die("is_dir({$model_dir})");
                }
            } else {
                echo $module_dir;
                die("is_dir({$module_dir})");
            }
        }
    }

    public static function createController(\Sl_Module_Abstract $module, $classname, \Sl_Model_Abstract $inherit_from = null) {
        if (is_string($classname)) {
            $classname = ucfirst(strtolower($classname));
            //$module = \Sl_Module_Manager::getInstance()->getModule($name_array[2]);
            $module_type = $module->getType('\\');
            $module_dir = APPLICATION_PATH . '/' . $module->getDir();
            //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];
            if (is_dir($module_dir)) {

                $controller_dir = $module_dir . '/Controller';
                if (!is_dir($controller_dir)) {
                    self::createDir($controller_dir);
                }
                if (is_dir($controller_dir)) {
                    $filename = $controller_dir . '/' . $classname . '.php';
                    $fh = fopen($filename, 'w+');
                    if ($fh) {
                        $tpl = preg_replace(array(
                            '/%ModuleType%/',
                            '/%Module%/',
                            '/%Class%/',
                                ), array(
                            $module_type,
                            ucfirst($module->getName()),
                            $classname,
                                ), self::getTemplate('controller', $inherit_from));
                        fwrite($fh, $tpl);
                        chmod($filename, 0777);
                        fclose($fh);
                    } else {
                        die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                    }
                } else {
                    die("is_dir({$controller_dir})");
                }
            } else {
                echo $module_dir;
                die("is_dir({$module_dir})");
            }
        }
    }

    public static function updateModel(\Sl_Model_Abstract $model, $fields) {
        $module = \Sl_Module_Manager::getInstance()->getModule($model->findModuleName());
        $module_type = $module->getType('\\');
        $module_dir = APPLICATION_PATH . '/' . $module->getDir();

        //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];

        $model_dir = $module_dir . '/Model';

        if (is_dir($model_dir)) {

            $filename = $model_dir . '/' . ucfirst($model->findModelName()) . '.php';
            $getters = $setters = $properties = '';
            $getters_arr = $setters_arr = $properties_arr = array();

            $file_content = preg_replace('/}[\s]*(\?>)?$/', "\t%getters%\r\n}", trim(file_get_contents($filename)));
            $file_content = preg_replace('/public/', "\r\n\r\n\t%setters%\r\n\r\n\tpublic", $file_content, 1);
            $file_content = preg_replace('/(protected \$_[^\s]+;)/', "$1 \r\n%properties%\r\n\r\n", $file_content, 1);

            foreach ($fields as $property_array) {
                if ($property_name['delete'] || !strlen($property_array['field_name']))
                    continue;
                $property = strtolower($property_array['field_name']);

                $prop_name_array = array_map('ucfirst', explode('_', strtolower($property)));
                $method = implode('', $prop_name_array);

                $getters_arr[] = preg_replace(array('/%Method%/', '/%property%/'), array($method, $property), self::getTemplate('getter', $property_array['field_type']));
                $setters_arr[] = preg_replace(array('/%Method%/', '/%property%/'), array($method, $property), self::getTemplate('setter', $property_array['field_type']));
                $properties_arr[] = preg_replace(array('/%property%/'), array($property), self::getTemplate('property'));
            }

            $getters = implode("\r\n\t", $getters_arr);
            $setters = implode("\r\n\t", $setters_arr);
            $properties = implode("\r\n\t", $properties_arr);

            $fh = fopen($filename, 'w+');
            if ($fh) {
                $tpl = preg_replace(array(
                    '/%properties%/',
                    '/%getters%/',
                    '/%setters%/',
                        ), array(
                    $properties,
                    $getters,
                    $setters,
                        ), $file_content);
                fwrite($fh, $tpl);
                chmod($filename, 0777);
                fclose($fh);
            } else {
                die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
            }
        } else {
            die("is_dir({$model_dir})");
        }
    }

    public static function createModule($name, array $simpleControllers = array(), $activate = false) {
        try {
            $modulename = ucfirst(strtolower(trim($name)));
            try {
                $module = ModuleManager::find($name);
            } catch(Exception $e) {
                // Все нормально. Так и должно быть
            }
            if($module && ($module instanceof AbstracModule)) {
                throw new Exception('Module "'.$name.'" already exists. '.__METHOD__);
            }
            $module_dir = APPLICATION_PATH.'/Module/'.$modulename;
            if(is_dir($module_dir)) {
                throw new Exception('Directory "'.$module_dir.'" already exists. '.__METHOD__);
            }
            if(false === mkdir($module_dir, 0777, true)) {
                throw new Exception(__METHOD__.' ('.print_r(error_get_last(), true).')');
            }
            $configs_dir = $module_dir.'/configs';
            if(false === mkdir($configs_dir, 0777, true)) {
                throw new Exception(__METHOD__.' ('.print_r(error_get_last(), true).')');
            }
            $module_file_contents = str_replace(array(
                '%Module%',
            ), array(
                $modulename
            ), self::getTemplate('module'));
            $module_file = $module_dir.'/Module.php';
            $fh = fopen($module_file, 'w');
            if(!$fh) {
                throw new Exception('Can\'t open file "'.$module_file.'" for writing. '.__METHOD__);
            }
            fwrite($fh, $module_file_contents);
            fclose($fh);
            chmod($module_file, 0777);
            $config_file = $configs_dir.'/module.php';
            $fh = fopen($config_file, 'w');
            if(!$fh) {
                throw new Exception('Can\'t open file "'.$config_file.'" for writing. '.__METHOD__);
            }
            fwrite($fh, "<?php\r\nreturn array();");
            fclose($fh);
            chmod($config_file, 0777);
            if(count($simpleControllers)) {
                $controller_dir = $module_dir.'/Controller';
                if(!is_dir($controller_dir)) {
                    if(false === mkdir($controller_dir, 0777, true)) {
                        throw new Exception(__METHOD__.' ('.print_r(error_get_last(), true).')');
                    }
                }
                foreach($simpleControllers as $raw_name) {
                    $controller_name = ucfirst(strtolower(trim($raw_name)));
                    $controller_file = $controller_dir.'/'.$controller_name.'.php';
                    $controller_file_contents = str_replace(array(
                        '%Module%',
                    ), array(
                        $modulename
                    ), self::getTemplate('controllerSimple', $raw_name));
                    $fh = fopen($controller_file, 'w');
                    if(!$fh) {
                        throw new Exception('Can\'t open file "'.$controller_file.'" for writing. '.__METHOD__);
                    }
                    fwrite($fh, $controller_file_contents);
                    fclose($fh);
                    chmod($controller_file, 0777);
                }
            }
            if($activate) {
                $modules_config_file = APPLICATION_PATH.'/configs/modules.php';
                if(!file_exists($modules_config_file) || !is_readable($modules_config_file)) {
                    throw new Exception('File "'.$modules_config_file.'" isn\'t exist or isn\'t readable. '.__METHOD__);
                }
                $config = new \Zend_Config(require $modules_config_file, true);
                $key = strtolower($modulename);
                $config->$key = array(
                    'file' => '../application/Module/'.$modulename.'/Module.php',
                    'type' => 'Application_Module',
                    'dir' => '../application/Module/'.$modulename,
                );
                $writer = new \Zend_Config_Writer_Array(array(
                    'config' => $config,
                ));
                chmod($modules_config_file, 0777);
                $writer->write($modules_config_file);
                $cache = \Zend_Registry::get('cache');
                if($cache) {
                    $cache->clean();
                }
            }
        } catch (Exception $e) {
            \Sl\Module\Home\Service\Errors::addError($e->getMessage(), 'Module creation');
            return;
        }
    }


    public static function createModel(\Sl_Module_Abstract $module, $classname, $fields, $is_loged = false, \Sl_Model_Abstract $inherit_from = null) {
        if (is_string($classname)) {
            $classname = ucfirst(strtolower($classname));

//				$module = \Sl_Module_Manager::getInstance()->getModule($name_array[2]);
            $module_type = $module->getType('\\');
            $module_dir = APPLICATION_PATH . '/' . $module->getDir();

            //$module_dir = APPLICATION_PATH.'/../library/Sl/Module/'.$name_array[2];
            if (is_dir($module_dir)) {
                $model_dir = $module_dir . '/Model';
                if (!is_dir($model_dir)) {
                    self::createDir($model_dir);
                }
                if (is_dir($model_dir)) {



                    $filename = $model_dir . '/' . $classname . '.php';
                    $getters = $setters = $properties = '';
                    $getters_arr = $setters_arr = $properties_arr = array();
                    
                    foreach ($fields as $property_array) {
                        if ($property_name['delete'] || !strlen($property_array['field_name']))
                            continue;
                        $property = strtolower($property_array['field_name']);

                        $prop_name_array = array_map('ucfirst', explode('_', strtolower($property)));
                        $method = implode('', $prop_name_array);

                        $getters_arr[] = preg_replace(array('/%Method%/', '/%property%/'), array($method, $property), self::getTemplate('getter', $property_array['field_type']));
                        $setters_arr[] = preg_replace(array('/%Method%/', '/%property%/'), array($method, $property), self::getTemplate('setter', $property_array['field_type']));
                        $properties_arr[] = preg_replace(array('/%property%/'), array($property), self::getTemplate('property'));
                    }

                    $getters = implode("\r\n\t", $getters_arr);
                    $setters = implode("\r\n\t", $setters_arr);
                    $properties = implode("\r\n\t", $properties_arr);

                    $fh = fopen($filename, 'w+');
                    if ($fh) {
                        $tpl = preg_replace(array(
                            '/%ModuleType%/',
                            '/%Module%/',
                            '/%Class%/',
                            '/%properties%/',
                            '/%getters%/',
                            '/%setters%/',
                            '/%loged%/'
                                ), array(
                            $module_type,
                            ucfirst($module->getName()),
                            $classname,
                            $properties,
                            $getters,
                            $setters,
                            ($is_loged ? '' : 'protected $_loged = false;')
                                ), self::getTemplate('model', $inherit_from));
                        fwrite($fh, $tpl);
                        chmod($filename, 0777);
                        fclose($fh);
                        self::createModelConfigs($module, $classname, $fields);
                    } else {
                        die('Can not open file ' . $filename . PHP_EOL . implode(PHP_EOL, error_get_last()));
                    }
                } else {
                    die("is_dir({$model_dir})");
                }
            } else {
                echo $module_dir;
                die("is_dir({$module_dir})");
            }
        }
    }

    public static function createDir($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
        }
    }

    public static function getTemplate($type, $param = null) {

        switch ($type) {
            case 'model':
                if(!is_null($param) && ($param instanceof AbstractModel)) {
                    $str  = '<?php'."\r\n";
                    $str .= 'namespace %ModuleType%\\%Module%\\Model;'."\r\n";
                    $str .= "\r\n";
                    $str .= 'class %Class% extends \\'.get_class($param).' {'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'%properties%'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'%loged%'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'%setters%'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'%getters%'."\r\n";
                    $str .= '}';
                } else {
                    $str = "<?php\r\nnamespace %ModuleType%\%Module%\Model;\r\n\r\n";
                    $str .= "class %Class% extends \Sl_Model_Abstract {\r\n\r\n\t%properties%\r\n\t%loged%\r\n\r\n\t%setters%\r\n\r\n\t%getters%\r\n\r\n\r\n\r\n}";
                }
                return $str;
                break;

            case 'property':
                $str = "protected $" . "_%property%;";
                return $str;
                break;

            case 'setter':
                switch($param) {
                    case 'date':
                    case 'timestamp':
                        $constant_name = 'FORMAT_'.strtoupper($field_type);
                        $str  = "\r\n";
                        $str .= "\t".'public function set%Method%($%property%) {'."\r\n";
                        $str .= "\t\t".'if($%property% instanceof \DateTime) {'."\r\n";
                        $str .= "\t\t\t".'$%property% = $%property%->format(self::'.$constant_name.');'."\r\n";
                        $str .= "\t\t".'}'."\r\n";
                        $str .= "\t\t".'$this->_%property% = $%property%;'."\r\n";
                        $str .= "\t\t".'return $this;'."\r\n";
                        $str .= "\t".'}';
                        break;
                    default:
                        $str = "public function set%Method% ($" . "%property%) {\r\n\t\t" . '$this->_%property% = $%property%;' . "\r\n\t\t" . 'return $this;' . "\r\n\t}";
                        break;
                }
                return $str;
                break;

            case 'getter':
                switch($param) {
                    case 'date':
                    case 'timestamp':
                        $constant_name = 'FORMAT_'.strtoupper($field_type);
                        $str  = "\r\n";
                        $str .= "\t".'public function get%Method%($as_object = false) {'."\r\n";
                        $str .= "\t\t".'if($as_object) {'."\r\n";
                        $str .= "\t\t\t".'return \DateTime::createFromFormat(self::'.$constant_name.', $this->get%Method%());'."\r\n";
                        $str .= "\t\t".'}'."\r\n";
                        $str .= "\t\t".'return $this->_%property%;'."\r\n";
                        $str .= "\t".'}';
                        break;
                    default:
                        $str = "public function get%Method% () {\r\n\t\t" . 'return $this->_%property%;' . "\r\n\t}";
                        break;
                }
                return $str;
                break;


            case 'identity':
                $str = "<?php\r\nnamespace %ModuleType%\%Module%\Model\Identity;\r\n\r\n";
                $str .= "class %Class% extends \Sl\Model\Identity\Identity {\r\n\r\n\r\n}";
                return $str;
                break;

            case 'modulerelation':
                $str = "<?php\r\nnamespace %ModuleType%\%Module%\Modulerelation;\r\n\r\n";
                $str .= "class %Class% extends \Sl\Modulerelation\Modulerelation {\r\n\r\n\r\n}";
                return $str;
                break;

            case 'table':
                if(!is_null($param) && ($param instanceof AbstractModel)) {
                    // Есть от кого наследоваться
                    $table = ModelFactory::table($param);
                    $str  = '<?php'."\r\n";
                    $str .= 'namespace %ModuleType%\%Module%\Model\Table;'."\r\n";
                    $str .= "\r\n";
                    $str .= 'class %Class% extends \\'.get_class($table).' {'."\r\n";
                    $str .= "\r\n";
                    $str .= '}';
                } else {
                    $str = "<?php\r\nnamespace %ModuleType%\%Module%\Model\Table;\r\n\r\n";
                    $str .= "class %Class% extends \Sl\Model\DbTable\DbTable {\r\n\tprotected $" . "_name = '%table%';\r\n\tprotected $" . "_primary = 'id';\r\n\r\n}";
                }
                return $str;
                break;


            case 'modulerelation_table':
                $str = "<?php\r\nnamespace %ModuleType%\%Module%\Modulerelation\Table;\r\n\r\n";
                $str .= "class %Class% extends \Sl\Modulerelation\DbTable {\r\n\tprotected $" . "_name = '%table%';\r\n\tprotected $" . "_primary = 'id';\r\n\tprotected $" . "_referenceMap = array(\r\n\t\t\t'%SourceModuleType%\%SourceModule%\Model\%SourceModel%' => array(\r\n\t\t\t'columns' => '%id_field%',\r\n\t\t\t'refTableClass' => '%SourceModuleType%\%SourceModule%\Model\Table\%SourceModel%',\r\n\t\t'refColums' => 'id'),
                		'%TargetModuleType%\%TargetModule%\Model\%TargetModel%' => array(\r\n\t\t\t'columns' => '%id_target_field%',\r\n\t\t\t'refTableClass' => '%TargetModuleType%\%TargetModule%\Model\Table\%TargetModel%',\r\n\t\t\t	'refColums' => 'id'	),\r\n\t);\r\n}";
                return $str;
                break;


            case 'controller':
                if(!is_null($param) && ($param instanceof AbstractModel)) {
                    $module = ModuleManager::find($param->findModuleName());
                    $ctrl_class = '\\'.($module->getType('\\')).'\\'.ucfirst($module->getName()).'\\Controller\\'.ucfirst($param->findModelName());
                    $str  = '<?php'."\r\n";
                    $str .= 'namespace %ModuleType%\%Module%\Controller;'."\r\n";
                    $str .= "\r\n";
                    $str .= 'class %Class% extends '.$ctrl_class.' {'."\r\n";
                    $str .= "\r\n\r\n";
                    $str .= '}';
                } else {
                    $str = "<?php\r\nnamespace %ModuleType%\%Module%\Controller;\r\n\r\n";
                    $str .= "class %Class% extends \Sl_Controller_Model_Action {\r\n\r\n\r\n}";
                }
                return $str;
                break;
            case 'mapper':
                if(!is_null($param) && ($param instanceof AbstractModel)) {
                    $base_mapper = ModelFactory::mapper($param);
                    $str  = '<?php'."\r\n";
                    $str .= 'namespace %ModuleType%\\%Module%\\Model\\Mapper;'."\r\n";
                    $str .= "\r\n";
                    $str .= 'class %Class% extends \\'.get_class($base_mapper).' {'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'protected function _getMappedDomainName() {'."\r\n";
                    $str .= "\t\t".'return \'\\%ModuleType%\\%Module%\\Model\\%Class%\';'."\r\n";
                    $str .= "\t".'}'."\r\n";
                    $str .= "\r\n";
                    $str .= "\t".'protected function _getMappedRealName() {'."\r\n";
                    $str .= "\t\t".'return \'\\%ModuleType%\\%Module%\\Model\\Table\\%Class%\';'."\r\n";
                    $str .= "\t".'}'."\r\n";
                    $str .= "\r\n";
                    $str .= '}';
                } else {
                    $str = "<?php\r\nnamespace %ModuleType%\%Module%\Model\Mapper;\r\n\r\n";
                    $str .= "class %Class% extends \Sl_Model_Mapper_Abstract {
            protected function _getMappedDomainName() {
            return '\%ModuleType%\%Module%\Model\%Class%';
        }

        protected function _getMappedRealName() {
            return '\%ModuleType%\%Module%\Model\Table\%Class%';
        }\r\n}";
                }
                return $str;
                break;
            case 'module':
                $str  = '<?php'."\r\n";
                $str .= 'namespace Application\Module\%Module%;'."\r\n";
                $str .= "\r\n";
                $str .= 'use Sl_Module_Abstract as AbstractModule;'."\r\n";
                $str .= "\r\n";
                $str .= 'class Module extends AbstractModule { '."\r\n";
                $str .= "\r\n";
                $str .= "\t".'public function getListeners() {'."\r\n";
                $str .= "\t\t".'return array();'."\r\n";
                $str .= "\t".'}'."\r\n";
                $str .= '}';
                return $str;
                break;
            case 'controllerSimple':
                $name = 'main';
                if(!is_null($param)) {
                    $name = strtolower($param);
                }
                $str  = '<?php'."\r\n";
                $str .= 'namespace Application\Module\%Module%\Controller;'."\r\n";
                $str .= "\r\n";
                $str .= 'use Sl_Controller_Action as ActionController;'."\r\n";
                $str .= "\r\n";
                $str .= 'class '.ucfirst($name).' extends ActionController {'."\r\n";
                $str .= "\r\n";
                $str .= '}';
                return $str;
                break;
        }
    }

    public static function updateMysqlTable($table_name, array $fields, $write_sql = false) {
        $fields_arr = array();
        $fields_string = '';
        if(count($fields) === 0) return;
        foreach ($fields as $field) {
            if ($field['delete'] || !strlen($field['field_name']))
                continue;
            $field_name = $field['field_name'];
            $field_type = $field['field_type'];
            $field_value = $field['values'];
            if (!strlen($field_value)) {
                switch ($field_type) {
                    case 'int' : $field_value = '11';
                        break;
                    case 'tinyint' : $field_value = '4';
                        break;
                    case 'varchar' : $field_value = '100';
                        break;
                }
            }


            $field_value = (strlen($field_value) && !preg_match('/^\(.+\)$/', $field_value)) ? "({$field_value})" : $field_value;
            $null = $field['null'] ? 'NULL' : 'NOT NULL';
            $fields_arr[] = " ADD {$field_name} {$field_type}{$field_value} {$null} ";
        }
        /*
         * `active` tinyint(4) NOT NULL DEFAULT \'1\',
          `create` timestamp NULL DEFAULT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         * */

        $fields_string = implode(', ' . PHP_EOL, $fields_arr);
        $query = str_replace('[TABLE_NAME]', $table_name, str_replace('[FIELDS]', $fields_string, self::$update_table_query));

        $module = \Sl_Module_Manager::getInstance()->getModule('home');
        $db_table = \Sl_Model_Factory::dbTable('City', $module);

        $db_adapter = $db_table->getAdapter();
        $db_adapter->query($query);
        if ($write_sql) {
            $filename = APPLICATION_PATH . self::$update_query_file;
            $fh = fopen($filename, 'a+');
            if ($fh) {
                fwrite($fh, PHP_EOL . $query . PHP_EOL);
                chmod($filename, 0777);
                fclose($fh);
            } else {


                die('can not open file ' . $filename);
            }
        }
    }

    public static function createMysqlTable($table_name, array $fields, $write_sql = false) {
        $fields_arr = array();
        $fields_string = '';
        foreach ($fields as $field) {
            if ($field['delete'] || !strlen($field['field_name']))
                continue;
            $field_name = $field['field_name'];
            $field_type = $field['field_type'];
            $field_value = $field['values'];
            if (!strlen($field_value)) {
                switch ($field_type) {
                    case 'int' : $field_value = '11';
                        break;
                    case 'tinyint' : $field_value = '4';
                        break;
                    case 'varchar' : $field_value = '100';
                        break;
                }
            }
            $field_value = (strlen($field_value) && !preg_match('/^\(.+\)$/', $field_value)) ? "({$field_value})" : $field_value;
            $null = $field['null'] ? 'NULL' : 'NOT NULL';
            $fields_arr[] = "{$field_name} {$field_type}{$field_value} {$null},";
        }
        /*
         * `active` tinyint(4) NOT NULL DEFAULT \'1\',
          `create` timestamp NULL DEFAULT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
         * */

        $fields_string = implode(PHP_EOL, $fields_arr);
        $query = str_replace('[TABLE_NAME]', $table_name, str_replace('[FIELDS]', $fields_string, self::$create_table_query));

        $module = \Sl_Module_Manager::getInstance()->getModule('home');
        $db_table = \Sl_Model_Factory::dbTable('City', $module);

        $db_adapter = $db_table->getAdapter();
        $db_adapter->query($query);
        
        if ($write_sql) {
            $filename = APPLICATION_PATH . self::$update_query_file;
            $fh = fopen($filename, 'a+');
            if ($fh) {
                fwrite($fh, PHP_EOL . $query . PHP_EOL);
                chmod($filename, 0777);
                fclose($fh);
            } else {
                die('can not open file ' . $filename);
            }
        }
    }
    
    public static function createModelConfigs(\Sl_Module_Abstract $module, $modelname, array $fields) {
        $model_fields = array();
        foreach($fields as $field) {
            if(!$field['field_name']) continue;
            $model_fields[$field['field_name']] = array(
                'label' => strtoupper($field['field_name']),
                'type' => 'text',
            );
            switch($field['field_type']) {
                case 'date':
                case 'timestamp':
                    $model_fields[$field['field_name']]['type'] = $field['field_type'];
                    break;
            }
        }
        $defaults = array(
            'module' => array(
                'relations' => array(),
                'lists' => array(),
            ),
            'model' => array(
                'model' => $model_fields,
                'detailed' => array(),
                'fieldsets' => array(
                    '_default' => array(
                        'name' => '_default',
                        'label' => 'По-умолчанию',
                        'fields' => isset($model_fields['name'])?array('name'):(count($model_fields)?array(key($model_fields)):array()),
                    ),
                    '_popup' => array(
                        'name' => '_popup',
                        'label' => 'По-умолчанию',
                        'fields' => isset($model_fields['name'])?array('name'):(count($model_fields)?array(key($model_fields)):array()),
                    ),
                ),
                'filters' => array(
                    '_default' => array(
                        'name' => 'По-умолчанию',
                        'filter' => array(
                            'type' => 'multi',
                            'comparison' => 1,
                            'comps' => array(
                                '_system' => array(
                                    'type' => 'multi',
                                    'comparison' => 1,
                                    'comps' => array(
                                        'active' => array(
                                            'field' => 'active',
                                            'type' => 'eq',
                                            'value' => 1
                                        ),
                                    ),
                                ),
                                '_user' => array(
                                    'type' => 'multi',
                                    'comparison' => 2,
                                    'comps' => array(
                                        '_custom' => array(
                                            'type' => 'multi',
                                            'comparison' => 1,
                                            'comps' => array(),
                                        ),
                                        '_id' => array(
                                            'field' => 'id',
                                            'type' => 'in',
                                            'value' => array(
                                                
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                'form' => array(),
                'list' => array(),
                'listview' => array(),
            ),
        );
        foreach($defaults as $type=>$data) {
            foreach($data as $section=>$item) {
                try {
                    $tmp_src = ($type === 'module')?$module:\Sl_Model_Factory::object($modelname, $module);
                    \Sl\Service\Config::write($tmp_src, $section, $item, true);
                } catch(\Exception $e) {
                    throw new \Exception('Insure that model (file with class) already created before using this method. '.__METHOD__);
                }
            }
        }
    }
    
    public static function updateModelConfigs(\Sl_Model_Abstract $model, $fields) {
        foreach($fields as $field) {
            if(!$field['field_name']) continue;
            $name = $field['field_name'];
            $data = array(
                'label' => strtoupper($field['field_name']),
                'type' => 'text',
            );
            switch($field['field_type']) {
                case 'date':
                case 'timestamp':
                    $data['type'] = $field['field_type'];
                    break;
            }
            \Sl\Service\Config::write($model, 'model/'.$name, $data, true);
        }
    }

}
