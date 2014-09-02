<?php

class Sl_Dispatcher extends Zend_Controller_Dispatcher_Standard {

	public function __construct(array $params = array()) {
		$this -> setParam('prefixDefaultModule', true);
	}

	public function formatControllerName($unformatted) {
		return 'Controller_' . ucfirst(strtolower($unformatted));
	}

	public function formatClassNameNamespace($moduleName, $className) {
		$prefix = Sl_Module_Manager::getInstance() -> getModule($moduleName) -> getType();
		$prefix = '\\' . preg_replace('/_/', '\\', $prefix) . '\\';
		return $prefix . ucfirst($moduleName) . '\\' . preg_replace('/_/', '\\', $className);
	}

	public function loadClass($className) {

		$finalClass = $className;
		if (($this -> _defaultModule != $this -> _curModule) || $this -> getParam('prefixDefaultModule')) {
			$finalClass = $this -> formatClassName($this -> _curModule, $className);
		}

		if (class_exists($finalClass, false)) {
			return $finalClass;
		} elseif (class_exists($finalClass, true)) {
			return $finalClass;
		} elseif (class_exists($nsClass = $this -> formatClassNameNamespace($this -> _curModule, $className))) {
			return $nsClass;
		}

		$dispatchDir = $this -> getDispatchDirectory();
		$loadFile = $dispatchDir . DIRECTORY_SEPARATOR . $this -> classToFilename($className);

		if (Zend_Loader::isReadable($loadFile)) {
			include_once $loadFile;
		} else {
			require_once 'Zend/Controller/Dispatcher/Exception.php';
			throw new Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $loadFile . "'");
		}

		if (!class_exists($finalClass, false)) {
			require_once 'Zend/Controller/Dispatcher/Exception.php';
			throw new Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
		}

		return $finalClass;
	}

	public function dispatch(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response) {
		$this -> setResponse($response);
		/**
		 * Get controller class
		 */
	
		 
		if (!$this -> isDispatchable($request)) {
			$controller = $request -> getControllerName();
			
			if (!$this -> getParam('useDefaultControllerAlways') && !empty($controller)) {
				require_once 'Zend/Controller/Dispatcher/Exception.php';
				throw new Zend_Controller_Dispatcher_Exception('Invalid controller specified (' . $request -> getControllerName() . ')');
			}

			$className = $this -> getDefaultControllerClass($request);
		} else {
			$className = $this -> getControllerClass($request);
			
			if (!$className) {
				$className = $this -> getDefaultControllerClass($request);
			}
		}

		/**
		 * Load the controller class file
		 */
		 
		$className = $this -> loadClass($className);

		/**
		 * Instantiate controller with request, response, and invocation
		 * arguments; throw exception if it's not an action controller
		 */
		$controller = new $className($request, $this -> getResponse(), $this -> getParams());
		
		if (!($controller instanceof Zend_Controller_Action_Interface) && !($controller instanceof Zend_Controller_Action)) {
			require_once 'Zend/Controller/Dispatcher/Exception.php';
			throw new Zend_Controller_Dispatcher_Exception('Controller "' . $className . '" is not an instance of Zend_Controller_Action_Interface');
		}
		
		/*
		  Include module js and css files
		*/
		
		// Установка текущего модуля
		
		
       
		
		/**
		 * Retrieve the action name
		 */
		$action = $this -> getActionMethod($request);

		/**
		 * Dispatch the method call
		 */
		$request -> setDispatched(true);

		// by default, buffer output
		$disableOb = $this -> getParam('disableOutputBuffering');
		$obLevel = ob_get_level();
		if (empty($disableOb)) {
			ob_start();
		}

		try {
			$controller -> dispatch($action);
		} catch (Exception $e) {
			// Clean output buffer on error
			$curObLevel = ob_get_level();
			if ($curObLevel > $obLevel) {
				do {
					ob_get_clean();
					$curObLevel = ob_get_level();
				} while ($curObLevel > $obLevel);
			}
			throw $e;
		}

		if (empty($disableOb)) {
			$content = ob_get_clean();
			$response -> appendBody($content);
		}

		// Destroy the page controller instance and reflection objects
		$controller = null;
	}

}
?>
