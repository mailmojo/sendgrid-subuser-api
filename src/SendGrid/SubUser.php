<?php
namespace SendGrid;

/**
 * This class represents a SendGrid sub users.
 *
 * All sub user API call, save for creating a retrieving specific users, goes through an
 * instance of this class.
 *
 * @copyright Eliksir AS
 */
class SubUser {
	// Fields representing the user's profile data
	public $username;
	private $password;
	private $email;
	private $mail_domain;
	private $first_name;
	private $last_name;
	private $address;
	private $city;
	private $state;
	private $zip;
	private $country;
	private $phone;
	private $website;

	/**
	 * @var SendGrid\Api
	 */
	private $api;

	/**
	 * Initialize sub user with the supplied profile data.
	 *
	 * This does not actually create a user, for that see Api::addSubUser().
	 */
	public function __construct ($user, $password, $email, $domain, $firstName = '-',
								 $lastName = '-', $address = '-', $city = '-',
								 $state = '-', $zip = '-', $country = '-',
								 $phone = '-', $website = '-', Api $api = null) {
		$this->username = $user;
		$this->password = $password;
		$this->email = $email;
		$this->mail_domain = $domain;
		$this->first_name = $firstName;
		$this->last_name = $lastName;
		$this->address = $address;
		$this->city = $city;
		$this->state = $state;
		$this->zip = $zip;
		$this->country = $country;
		$this->phone = $phone;
		$this->website = $website;
		$this->api = $api;
	}

	/**
	 * Assign or clear IPs available to the user.
	 *
	 * $ips should contain one or more IPs to assign to the user, or be empty to clear
	 * all assigned IPs.
	 *
	 * @param array $ips
	 */
	public function assignIps (array $ips) {
		$params = array('task' => 'append');

		if (!empty($ips)) {
			$this->api->debug("Assigning IPs to '{$this->username}': " . print_r($ips, true));
			$params['set'] = 'specify';
			$params['ip'] = $ips;
		}
		else {
			$this->api->debug("Clearing all IPs from '{$this->username}'");
			$params['set'] = 'none';
		}

		$this->execute('customer.sendip.json', $params);
	}

	/**
	 * Configure the specified app.
	 *
	 * Settings vary per app. The easiest way to inspect what settings are needed is to
	 * call getAppSettings() for the relevant app.
	 *
	 * @param string $app     Name of the app.
	 * @param array $settings Settings to configure the app with.
	 */
	public function configureApp ($app, array $settings) {
		$this->api->debug("Configuring app '$app' for '{$this->username}'");
		$this->execute('customer.apps.json', array(
			'task' => 'setup',
			'name' => $app), $settings);
	}

	/**
	 * Retrieve all available apps.
	 */
	public function getApps () {
		return $this->retrieve('customer.apps.json', array('task' => 'getavailable'));
	}

	/**
	 * Retrieve settings for the specific app.
	 *
	 * @param string $app Name of the app. Use getApps() to find their names.
	 */
	public function getAppSettings ($app) {
		$this->api->debug("Getting app '$app' settings for '{$this->username}'");
		return $this->retrieve('customer.apps.json', array(
			'task' => 'getsettings',
			'name' => $app
		));
	}

	/**
	 * Retrieve the current URL uses for event notifications.
	 */
	public function getEventNotificationUrl () {
		return $this->retrieve('customer.eventposturl.json', array('task' => 'get'));
	}

	/**
	 * Enables or disables an app.
	 *
	 * If the app is to be enabled, $settings may optionally be supplied for configuring
	 * the app without a need for an explicit configureApp() call.
	 *
	 * @param string $app     Name of the app.
	 * @param bool $enable    Whether to enable or disable the app.
	 * @param array $settings Optional settings when enabling an app.
	 */
	public function enableApp ($app, $enable, array $settings = null) {
		$action = $enable ? 'activate' : 'deactivate';
		$this->api->debug("Setting app '$app' to '$action' for '{$this->username}'");
		$this->execute('customer.apps.json', array(
			'task' => $action,
			'name' => $app));

		if ($enable && !empty($settings)) {
			$this->configureApp($app, $settings);
		}
	}

	/**
	 * Enable or disable access to the SendGrid website for the user.
	 */
	public function enableWebsiteAccess ($enable) {
		$action = $enable ? 'enable' : 'disable';
		$this->execute("customer.website_$action.json");
	}

	/**
	 * Retrieve the profile data as an associative array.
	 */
	public function extract () {
		$array = get_object_vars($this);
		unset($array['api']);
		$array['confirm_password'] = $array['password'];
		return $array;
	}

	/**
	 * Attach the SendGrid API object.
	 */
	public function attachApi (Api $api) {
		$this->api = $api;
	}

	/**
	 * Helper method for "retrieve something" calls.
	 */
	private function retrieve ($action, array $params = array()) {
		$user = array('user' => $this->username);
		return $this->api->retrieve($action, array_merge($user, $params));
	}

	/**
	 * Helper method for "do something" calls.
	 */
	private function execute ($action, array $params = array(), array $postParams = null) {
		$user = array('user' => $this->username);
		return $this->api->execute($action, array_merge($user, $params), $postParams);
	}
}
