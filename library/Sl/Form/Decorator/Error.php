<?
class Sl_Form_Decorator_Error extends Zend_Form_Decorator_Abstract {
	const ERROR_DIV_CLASS = 'alert alert-error';
	const ERROR_PREPEND_STRING ='<button class="close" data-dismiss="alert" type="button">&times;</button>'; 
	public function render($content = '') {

		$element = $this -> getElement();
		$view = $element -> getView();
		if (null === $view) {
			return $content;
		}
		$placement = $this -> getPlacement();
		$separator = $this -> getSeparator();
		$errors = $element -> getMessages();
		
		if ($element instanceof \Sl_Form_SubForm){
			if (!$element->isErrors()){
				return $content;
			}	
			//print_r($errors);
			$form_errors = array();
			foreach ($errors as $error){
				if (count($error)) $form_errors = array_merge($error,$form_errors);
			}
			$errors = $form_errors;
			//print_r($form_errors);
			//$errors = array_shift($errors);
		}
		
		if (empty($errors)) {
			return $content;
		}
		/*
		$errors = $view -> formErrors($errors, array(
			'markupElementLabelEnd' => '</b>',
			'markupElementLabelStart' => '<b>',
			'markupListEnd' => '</div>',
			'markupListItemEnd' => '',
			'markupListItemStart' => '',
			'markupListStart' => '<div>'
		));*/
		
		//print_r($errors);
		$output = '<div class="' . self::ERROR_DIV_CLASS . '">'.self::ERROR_PREPEND_STRING. '<div>' . implode(';'.PHP_EOL,$errors) .'</div></div>';

		switch ($placement) {
			case 'PREPEND' :
				return $output . $separator . $content;
			case 'APPEND' :
			default :
				return $content . $separator . $output;
		}
	}

}
