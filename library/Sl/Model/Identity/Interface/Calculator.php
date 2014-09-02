<? 

interface Sl_Model_Identity_Interface_Calculator {
        
    public function getRequestColumns($fields);
    public function getCalculatedColumns($fields);
	public function getRowController();
	public function calculateValues($values);
}

?>
