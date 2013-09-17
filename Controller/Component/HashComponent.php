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
	 * Simple setup for the HashLib class
	 *
	 * @return object $this->HashLib
	 */
	public function setup() {
		if (is_object($this->HashLib)) {
			return $this->HashLib;
		}
		App::uses('HashLib', 'Lib');
		$this->HashLib = new HashLib;
		return $this->HashLib;
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
		$hashOptions = array(
			'ip' => true,
			'member_id' => false,
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

