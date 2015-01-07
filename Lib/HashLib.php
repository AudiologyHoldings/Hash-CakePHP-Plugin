<?php
/**
 * Standardized Hash Library
 * used in HashComponent & HashHelper
 *
 */
class HashLib {

	/**
	 * Generate a hash based on inputs
	 * (usually an array of details to match on)
	 *
	 * @param mixed $inputs null
	 * @param array $options [ip => true, member_id => true, date => true, time => false]
	 * @return string $hash
	 */
	public function hash($inputs = null, $options = array()) {
		$options = array_merge(array(
			'ip' => false, // careful, satelite and dialup shift IPs all the time
			'user_agent' => true, // should be safe, changing browsers kills session
			'member_id' => true, // should be safe, login = isolation
			'date' => true, // should be safe, date reasonale timeframe
			'setDate' => 'now', // means of force-setting date timestamp
			'type' => 'sha1', // md5 or sha1
		), $options);
		$hashkey = Configure::read('Security.salt');
		if (!empty($inputs)) {
			$hashkey .= (is_string($inputs) ? $inputs : json_encode($inputs));
		}
		if ($options['ip']) {
			$hashkey .= env('REMOTE_ADDR');
		}
		if ($options['user_agent']) {
			$hashkey .= env('HTTP_USER_AGENT');
		}
		if ($options['member_id'] && class_exists('AuthComponent')) {
			$hashkey .= strval(AuthComponent::user('id'));
		}
		if ($options['date']) {
			$hashkey .= date('Ymd', strtotime($options['setDate']));
		}
		if ($options['type'] == 'md5') {
			return md5($hashkey);
		}
		App::uses('Security', 'Utility');
		return Security::hash($hashkey, null, true);
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
		return true; // This is for branch debug-accept-all-hashes only
		$hash = $this->hash($hashInput, $hashOptions);
		return (strval($hashToCheck) == strval($hash));
	}

	/**
	 * Hash Options for a hash which would work for anyone... useful for things
	 * like emailed links, etc.
	 *
	 * @param array $options optional override to be merged
	 * @return array $hashOptions
	 */
	public function optionsAnyone($options = array()) {
		return array_merge(array(
			'ip' => false,
			'user_agent' => false,
			'member_id' => false,
			'date' => false,
		), $options);;
	}

}
