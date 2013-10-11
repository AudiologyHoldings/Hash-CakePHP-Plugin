<?php
/**
 * This is a very small helper, facilitating basic functionality for member
 * form data submission security... (no other users could submit your form)
 *
 */
App::uses('Helper', 'View');
class HashHelper extends Helper {

	/**
	 * placeholder for HashLib object
	 */
	public $HashLib = null;

	/**
	 *
	 *
	 */
	public $hiddenFormVerifyTemplate = '<input type="hidden" role="HashFormVerify" name="data[Verify][hash]" value="%s">';

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
	 * Set the standardized "Form Data Hash" for this user
	 * basically ensuring that no other user could submit this form
	 *
	 * @return string $formHash
	 */
	public function form() {
		$hashInput = null;
		// NOTE these options must match on the HashComponent & HashHelper
		$hashOptions = array(
			'ip' => false,
			'user_agent' => true,
			'member_id' => true,
			'date' => true,
		);
		return $this->setup()->hash($hashInput, $hashOptions);
	}

	/**
	 *
	 *
	 */
	public function hiddenFormVerify() {
		return sprintf($this->hiddenFormVerifyTemplate, $this->form());
	}

	/**
	 * Create a hash based on some inputs...
	 * usually this is used to create a link or key which will the be validated
	 *
	 * @param mixed $inputs
	 * @param array $options
	 * @return string $hash
	 */
	public function hash($inputs = null, $options = array()) {
		$options = array_merge(array(
			'ip' => false,
			'user_agent' => true,
			'member_id' => true,
			'date' => true,
		), $options);
		return $this->setup()->hash($inputs, $options);
	}

	/**
	 * Create a hash based on some inputs...
	 * usually this is used to create a link or key which will the be validated
	 * (this variant could be used by anyone with the link)
	 *
	 * @param mixed $inputs
	 * @param array $options
	 * @return string $hash
	 */
	public function hashAnyone($inputs = null, $options = array()) {
		$options = array_merge(array(
			'ip' => false,
			'user_agent' => false,
			'member_id' => false,
			'date' => false,
		), $options);
		return $this->setup()->hash($inputs, $options);
	}

}
