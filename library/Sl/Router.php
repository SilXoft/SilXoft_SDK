<?php

class Sl_Router extends Zend_Controller_Router_Rewrite {
    
    protected $_routes;
    
    /*public function __construct(array $params = array()) {
        parent::__construct($params);
    }
    
    public function assemble($userParams, $name = null, $reset = false, $encode = true) {
        return '/';//throw new Sl_Exception_Router('Not emplemented yet ...');
    }

    public function route(\Zend_Controller_Request_Abstract $request) {
        $match = false;
        foreach($this->getRoutes() as $route) {
            if(!$match && $route->match($request)) {
                $match = true;
            }
        }
        if(!$match) {
            throw new Exception('asdasdasd');
        }
    }
    
    public function addRoute(Sl_Router_Route_Simple $route) {
        $this->_routes[] = $route;
    }
    
    public function addRoutes(array $routes) {
        foreach($routes as $route) {
            if($route instanceof Sl_Router_Route_Simple) {
                $this->addRoute($route);
            }
        }
    }
    */
    /**
     * 
     * @return Sl_Router_Route_Simple[]
     */
    /*
    public function getRoutes() {
        return $this->_routes?$this->_routes:array();
    }*/
    
    public function useRequestParametersAsGlobal($use = null) {
        return false;
    }
    
    public function assemble($userParams, $name = null, $reset = false, $encode = true) {
        	
        if (!is_array($userParams)) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('userParams must be an array');
        }
        
        if ($name == null) {
          
                $name = 'default';
          
        }
		 
        // Use UNION (+) in order to preserve numeric keys
        $params = $userParams + $this->_globalParams;

        $route = $this->getRoute($name);
		 
        $url   = $route->assemble($params, true, $encode);
		
		
        if (!preg_match('|^[a-z]+://|', $url)) {
            $url = rtrim($this->getFrontController()->getBaseUrl(), self::URI_DELIMITER) . self::URI_DELIMITER . $url;
        }
        return $url;
        //return parent::assemble($userParams, $name, $reset, $encode);
    }
}

?>
