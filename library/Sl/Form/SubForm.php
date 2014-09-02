<?
class Sl_Form_SubForm extends \Sl\Form\Form {

    /**
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;
    
    /**
     * Load the default decorators
     *
     * @return Zend_Form_SubForm
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset')
                 ->addDecorator('DtDdWrapper');
        }
        return $this;
    }
    
	protected $_validators;

	public function setLabel($label) {
		$this -> setAttrib('label', strval($label));
	}

	public function getLabel() {
		return $this -> getAttrib('label');
	}

	public function isRequired() {
		return false;
	}

	public function setRequired() {
		return;
	}

	// Validation

	public function isValid($data) {

		$result = true;

		foreach ($this->getValidators() as $validator) {
			$result &= $validator -> isValid(null, $data);
		}

		return $result && parent::isValid($data);
	}

	/**
	 * Add validator to validation chain
	 *
	 * Note: will overwrite existing validators if they are of the same class.
	 *
	 * @param  string|Zend_Validate_Interface $validator
	 * @param  bool $breakChainOnFailure
	 * @param  array $options
	 * @return Zend_Form_Element
	 * @throws Zend_Form_Exception if invalid validator type
	 */
	public function addValidator($validator, $breakChainOnFailure = false, $options = array()) {
		if ($validator instanceof Zend_Validate_Interface) {
			$name = get_class($validator);

			if (!isset($validator -> zfBreakChainOnFailure)) {
				$validator -> zfBreakChainOnFailure = $breakChainOnFailure;
			}
		} elseif (is_string($validator)) {
			$name = $validator;
			$validator = array(
				'validator' => $validator,
				'breakChainOnFailure' => $breakChainOnFailure,
				'options' => $options,
			);
		} else {
			require_once 'Zend/Form/Exception.php';
			throw new Zend_Form_Exception('Invalid validator provided to addValidator; must be string or Zend_Validate_Interface');
		}

		$this -> _validators[$name] = $validator;

		return $this;
	}

	/**
	 * Add multiple validators
	 *
	 * @param  array $validators
	 * @return Zend_Form_Element
	 */
	public function addValidators(array $validators) {
		foreach ($validators as $validatorInfo) {
			if (is_string($validatorInfo)) {
				$this -> addValidator($validatorInfo);
			} elseif ($validatorInfo instanceof Zend_Validate_Interface) {
				$this -> addValidator($validatorInfo);
			} elseif (is_array($validatorInfo)) {
				$argc = count($validatorInfo);
				$breakChainOnFailure = false;
				$options = array();
				if (isset($validatorInfo['validator'])) {
					$validator = $validatorInfo['validator'];
					if (isset($validatorInfo['breakChainOnFailure'])) {
						$breakChainOnFailure = $validatorInfo['breakChainOnFailure'];
					}
					if (isset($validatorInfo['options'])) {
						$options = $validatorInfo['options'];
					}
					$this -> addValidator($validator, $breakChainOnFailure, $options);
				} else {
					switch (true) {
						case (0 == $argc) :
							break;
						case (1 <= $argc) :
							$validator = array_shift($validatorInfo);
						case (2 <= $argc) :
							$breakChainOnFailure = array_shift($validatorInfo);
						case (3 <= $argc) :
							$options = array_shift($validatorInfo);
						default :
							$this -> addValidator($validator, $breakChainOnFailure, $options);
							break;
					}
				}
			} else {
				require_once 'Zend/Form/Exception.php';
				throw new Zend_Form_Exception('Invalid validator passed to addValidators()');
			}
		}

		return $this;
	}

	/**
	 * Set multiple validators, overwriting previous validators
	 *
	 * @param  array $validators
	 * @return Zend_Form_Element
	 */
	public function setValidators(array $validators) {
		$this -> clearValidators();
		return $this -> addValidators($validators);
	}

	/**
	 * Retrieve a single validator by name
	 *
	 * @param  string $name
	 * @return Zend_Validate_Interface|false False if not found, validator otherwise
	 */
	public function getValidator($name) {
		if (!isset($this -> _validators[$name])) {
			$len = strlen($name);
			foreach ($this->_validators as $localName => $validator) {
				if ($len > strlen($localName)) {
					continue;
				}
				if (0 === substr_compare($localName, $name, -$len, $len, true)) {
					if (is_array($validator)) {
						return $this -> _loadValidator($validator);
					}
					return $validator;
				}
			}
			return false;
		}

		if (is_array($this -> _validators[$name])) {
			return $this -> _loadValidator($this -> _validators[$name]);
		}

		return $this -> _validators[$name];
	}

	protected function _loadValidator(array $validator) {
		$name = $validator['validator'];
		$options = $validator['options'];
		try {
			return \Sl\Validate\Validate::factory($name, $options);
		} catch(Exception $e) {
			echo $e -> getMessage() . "\r\n";
			return null;
		}
	}

	/**
	 * Retrieve all validators
	 *
	 * @return array
	 */
	public function getValidators() {
		$validators = array();
		if (is_array($this -> _validators)) {
			foreach ($this->_validators as $key => $value) {
				if ($value instanceof Zend_Validate_Interface) {
					$validators[$key] = $value;
					continue;
				}
				$validator = $this -> _loadValidator($value);
				$validators[get_class($validator)] = $validator;
			}
		}
		return $validators;
	}

	/**
	 * Remove a single validator by name
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function removeValidator($name) {
		if (isset($this -> _validators[$name])) {
			unset($this -> _validators[$name]);
		} else {
			$len = strlen($name);
			foreach (array_keys($this->_validators) as $validator) {
				if ($len > strlen($validator)) {
					continue;
				}
				if (0 === substr_compare($validator, $name, -$len, $len, true)) {
					unset($this -> _validators[$validator]);
					break;
				}
			}
		}

		return $this;
	}

	/**
	 * Clear all validators
	 *
	 * @return Zend_Form_Element
	 */
	public function clearValidators() {
		$this -> _validators = array();
		return $this;
	}

	public function getMessages($name = null, $suppressArrayNotation = false) {
		$messages = array();
		foreach ($this->getValidators() as $name => $validator) {
			if ($validator -> getMessages()) {
				$messages[$name] = $validator -> getMessages();
			}
		}
		return array_merge($messages, parent::getMessages($name, $suppressArrayNotation));
	}

	/**
	 * Are there errors in the form?
	 *
	 * @return bool
	 */
	public function isErrors() {

		foreach ($this->getValidators() as $name => $validator) {
			if (count($validator -> getErrors())) {
				return true;
			}
		}
		return false;
	}

}
