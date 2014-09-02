<?php
namespace Sl\View\Helper;
use \Zend_Controller_Front;

class Partial extends \Zend_View_Helper_Partial {
    
    public function partial($name = null, $module = null, $model = null) {
        if (0 == func_num_args()) {
            return $this;
        }

        $view = $this->cloneView();
        if (isset($this->partialCounter)) {
            $view->partialCounter = $this->partialCounter;
        }
        if ((null !== $module) && is_string($module)) {
            require_once 'Zend/Controller/Front.php';
            $moduleDir = Zend_Controller_Front::getInstance()->getControllerDirectory($module);
            if (null === $moduleDir) {
                require_once 'Zend/View/Helper/Partial/Exception.php';
                $e = new Zend_View_Helper_Partial_Exception('Cannot render partial; module does not exist');
                $e->setView($this->view);
                throw $e;
            }
            //$viewsDir = dirname($moduleDir) . '/views';
            $view->addBasePath($moduleDir);
        } elseif ((null == $model) && (null !== $module)
            && (is_array($module) || is_object($module)))
        {
            $model = $module;
        }

        if (!empty($model)) {
            if (is_array($model)) {
                $view->assign($model);
            } elseif (is_object($model)) {
                if (null !== ($objectKey = $this->getObjectKey())) {
                    $view->assign($objectKey, $model);
                } elseif (method_exists($model, 'toArray')) {
                    $view->assign($model->toArray());
                } else {
                    $view->assign(get_object_vars($model));
                }
            }
        }

        return $view->render($name);
    }
}