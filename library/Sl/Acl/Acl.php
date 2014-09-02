<?
namespace Sl\Acl;

/**
 * Список контроля доступа
 *
 */

class Acl  extends \Zend_Acl {
    const ASSERTION_KEY = 'assertion';
    const RESOURSE_KEY = 'resource';
    protected $_resources = array();

    /**
     * Проверка прав доступа
     *
     * @param mixed $role
     * @param mixed $resource
     * @param mixed $privilege
     * @return boolean
     */
    public function isAllowed($role = null, $resource = null, $privilege = null) {
        $resource_name = false;
        // Строим имя ресурса
        if (is_string($resource)) {
            $resource_name = $resource;
        } elseif (is_array($resource) && $resource[0] instanceof \Sl_Model_Abstract) {
            $resource_name = $resource[0] -> buildResourceName($resource[1]);
        }

        if (!$resource_name || !$this -> has($resource_name))
            return false;
        //        return parent::isAllowed($role, $resource_name, $privilege);
        $privilege = ($privilege == null) ? \Sl_Service_Acl::PRIVELEGE_ACCESS : $privilege;

        foreach ($this->_prepareRoles($role) as $role) {
            
            if (isset($this -> _resources[$resource_name][$role][$privilege]) && (bool)$this -> _resources[$resource_name][$role][$privilege]) {

                if (($assert = $this -> _resources[$resource_name][self::ASSERTION_KEY]) && ($assert instanceof \Zend_Acl_Assert_Interface)) {

                    if (!$assert -> assert($this, null, $this -> _resources[$resource_name][self::RESOURSE_KEY], $privilege)){
                        continue;
                    }
                }

                return true;

            }
        }

        return false;

    }

    public function has($resource) {

        return isset($this -> _resources[$this -> _prepareResource($resource)]);

    }

    public function add($resource) {

        if (!$this -> has($resource)) {
            $this -> _resources[$this -> _prepareResource($resource)] = array(self::RESOURSE_KEY => $resource);
            //echo 'add '.$this->_prepareResource($resource).PHP_EOL;
        }

    }

    public function allow($roles = null, $resources = null, $privileges = null, \Zend_Acl_Assert_Interface $assert = null) {
        $privileges = ($privileges === null) ? \Sl_Service_Acl::PRIVELEGE_ACCESS : $privileges;

        if ($resources === null)
            return;

        $resources = (!is_array($resources)) ? array($resources) : $resources;
        $privileges = (!is_array($privileges)) ? array($privileges) : $privileges;

        foreach ($resources as $resource) {
            $resource = $this -> _prepareResource($resource);
            foreach ($privileges as $privilege) {
                foreach ($this->_prepareRoles($roles) as $role)
                    $this -> _resources[$resource][$role][$privilege] = true;
            }
            if ($assert) {
                $this -> _resources[$resource][self::ASSERTION_KEY] = $assert;
            }

        }

    }

    protected function _prepareRoles($roles = null) {
        $roles = is_null($roles) ? '' : $roles;
        return is_array($roles) ? $roles : array($roles);

    }

    protected function _prepareResource($resource) {
        return ($resource instanceof \Zend_Acl_Resource) ? $resource -> getResourceId() : $resource;
    }

}
