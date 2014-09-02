<?php
namespace Sl\Event;

class Fieldset extends \Sl_Event_Abstract {

    /**
     *
     * @var \Sl\Model\Identity\Fieldset
     */
    protected $_fieldset;

    public function __construct($type, array $options = array()) {
        if (!isset($options['fieldset']) || !($options['fieldset'] instanceof \Sl\Model\Identity\Fieldset)) {
            throw new \Exception('Param \'fieldset\' is required');
        }
        $this->setFieldset($options['fieldset']);

        parent::__construct($type, $options);
    }

    /**
     * 
     * @return \Sl_Model_Abstract
     */
    public function getModel() {
        return $this->getFieldset()->getModel();
    }

    /**
     * 
     * @param \Sl\Model\Identity\Fieldset $fieldset
     * @return \Sl\Event\Fieldset
     */
    public function setFieldset(\Sl\Model\Identity\Fieldset $fieldset) {
        $this->_fieldset = $fieldset;
        return $this;
    }

    /**
	 * 
	 * @return \Sl\Model\Identity\Fieldset
	 */
    public function getFieldset() {
        return $this->_fieldset;
    }

}
