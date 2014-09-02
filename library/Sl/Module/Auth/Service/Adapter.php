<? 
namespace Sl\Module\Auth\Service;

class Adapter {
    static protected $_adapter;
    const USER_TABLE = 'auth_users';
    const CREDENTIAL_COLUMN = 'password';
    const IDENTITY_COLUMN = 'login';
    
    static protected function getAdapter(){
            
        if (!self::$_adapter){
            self::$_adapter = new \Zend_Auth_Adapter_DbTable(); 
        }
        return self::$_adapter;
        
    }
    
    public static function authenticate($login, $password){
         $auth = self::getAdapter();
         $auth -> setTableName(self::USER_TABLE);
         $auth->setCredentialColumn(self::CREDENTIAL_COLUMN);
         $auth->setIdentity($login);
         $auth->setIdentityColumn(self::IDENTITY_COLUMN);
         $auth->setCredential(\Sl\Service\Helper::hash($password));
         $auth->getDbSelect()->where('blocked = 0');
         $auth->getDbSelect()->where('active = 1');
         return $auth->authenticate()->isValid()?$auth->getResultRowObject(array('id')):false; 
    }
    
    public static function authenticateHased($login, $password){
         $auth = self::getAdapter();
         $auth -> setTableName(self::USER_TABLE);
         $auth->setCredentialColumn(self::CREDENTIAL_COLUMN);
         $auth->setIdentity($login);
         $auth->setIdentityColumn(self::IDENTITY_COLUMN);
         $auth->setCredential($password);
         $auth->getDbSelect()->where('blocked = 0');
         $auth->getDbSelect()->where('active = 1');
         return $auth->authenticate()->isValid()?$auth->getResultRowObject(array('id')):false; 
    }  
    
}
