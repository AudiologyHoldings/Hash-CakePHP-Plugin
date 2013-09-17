<?php
/**
 * This is a simple "helper component" to create standardized hashes
 * for members and for general keys, and to check against them
 *
 * Sometimes hashes are specifc to a member, sometimes not
 * Sometimes hashes are specifc to an IP address, sometimes not
 * Sometimes hashes are time-based, sometimes not
 *
 *
 */
class HashComponent extends Component {

	/**
	 * placeholder for HashLib object
	 */
	public $HashLib = null;

	/**
	 * placeholder for Controller object
	 */
	public $request = null;

	/**
	 * Simple setup for the HashLib class
	 *
	 * @return object $this->HashLib
	 */
	public function setup() {
		if (is_object($this->HashLib)) {
			return $this->HashLib;
		}
		App::uses('HashLib', 'Hash.Lib');
		$this->HashLib = new HashLib;
		return $this->HashLib;
	}

	/**
	 * Called after the Controller::beforeFilter() and before the controller action
	 *
	 * @param Controller $controller Controller with components to startup
	 * @return void
	 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::startup
	 */
	public function startup(Controller $controller) {
		$this->request = $controller->request;
	}

	/**
	 * Generate a hash based on inputs
	 * (usually an array of details to match on)
	 *
	 * @param mixed $inputs null
	 * @param array $options [ip => true, member_id => true, date => true, time => false]
	 * @return string $hash
	 */
	public function hash($inputs = null, $options = array()) {
		return $this->setup()->hash($inputs, $options);
	}

	/**
	 * A standardized way to validate hashing
	 * (see OutputHelper::hashLink() for what should help make these links)
	 *
	 * @param string $hashToCheck
	 * @param mixed $hashInput
	 * @param mixed $hashOptions
	 * @return boolean
	 */
	public function validateHash($hashToCheck = null, $hashInput = null, $hashOptions = array()) {
		return $this->setup()->validateHash($hashToCheck, $hashInput, $hashOptions);
	}

	/**
	 * a special "validateHash" which gets the hashToCheck from $this->request->data['Verify']['hash']
	 *
	 * @param boolean $allowInputFromRequest [false]
	 * @return boolean
	 */
	public function verifyFormDataHash($allowInputFromRequest=false) {
		if ($allowInputFromRequest && empty($this->request->data['Verify']['hash'])) {
			$this->request->data['Verify']['hash'] == $this->request->query('v');
		}
		if (empty($this->request->data['Verify']['hash'])) {
			return false;
		}
		$hashToCheck = $this->request->data['Verify']['hash'];
		$hashInput = null;
		// NOTE these options must match on the HashComponent & HashHelper
		$hashOptions = array(
			'ip' => false,
			'user_agent' => true,
			'member_id' => true,
			'date' => true,
		);
		if ($this->validateHash($hashToCheck, $hashInput, $hashOptions)) {
			return true;
		}
		// was the hash submitted in the last hour, on a previous date?
		$hashOptions['setDate'] = '-1hour';
		if ($this->validateHash($hashToCheck, $hashInput, $hashOptions)) {
			return true;
		}
		return false;
	}

}

