<? 
namespace Sl\Module\Home\Informer;
class Informer {
    
    protected $_request;
    protected $_answer = array();
    
    public function getRequest($id = null){
        if (!$id) return $this->_request;
        if (is_array($this->_request) && isset($this->_request[$id])) return (isset($this->_request[$id]))?$this->_request[$id]:true;
        return false; 
    }
    
    public function setRequest(array $request){
        $this->_request = $request;
    }
    
    public function getAnswer(){
        
        return $this->_answer; 
    }
    
    public function setAnswer($id, array $answer){
        $this->_answer[$id] = $answer;
    }
    
}  