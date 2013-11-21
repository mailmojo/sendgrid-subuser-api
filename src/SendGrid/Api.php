<?php
namespace SendGrid;
use \LogicException, \InvalidArgumentException, \RuntimeException;
require_once 'SubUser.php';

/**
 * Top-level API class for SendGrid.
 *
 * Currently, this class is only used for creating and retrieving sub users. All actions
 * relating to a specific sub user goes through the SubUser class.
 *
 * @copyright Eliksir AS
 */
class Api {
	/**
	 * Root URL to the SendGrid API.
	 */
	const ROOT_URL = "https://sendgrid.com/apiv2/";

	/**
	 * The API user.
	 */
	private $apiUser;

	/**
	 * The API key, or technically the user's password.
	 */
	private $apiKey;

	/**
	 * The curl resource.
	 */
	private $ch;

	/**
	 * Designates whether new created subusers should be given website access.
	 */
	private $defaultWebsiteAccess = false;

	/**
	 * Outputs debugging info to stdout if true.
	 */
	private $isDebugging = false;

	/**
	 * Initialize the API class with the given API user and key (password).
	 *
	 * @param string $apiUser
	 * @param string $apiKey
	 * @param bool $defaultWebsiteAccess Whether any created subuser be given access
	 *                                   to the SendGrid web site.
	 * @param bool $debug                Whether to output debugging info.
	 */
	public function __construct ($apiUser, $apiKey, $defaultWebsiteAccess = false, $debug = false) {
		$this->apiUser = $apiUser;
		$this->apiKey = $apiKey;
		$this->defaultWebsiteAccess = $defaultWebsiteAccess;
		$this->isDebugging = $debug;
		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
	}

	/**
	 * Add a subuser with profile details as specified in $user.
	 */
	public function addSubUser (SubUser $user) {
		$this->debug("Adding subuser '{$user->username}'");
		$this->execute('customer.add.json', $user->extract());
		$user->attachApi($this);

		// SendGrid defaults to give website access. Disable if requested.
		if (!$this->defaultWebsiteAccess) {
			$user->enableWebSiteAccess(false);
		}
	}

	/**
	 * Retrieve a subuser with username $username.
	 *
	 * The subuser is returned as a SendGridSubUser instance. If not found, an
	 * InvalidArgumentException is thrown.
	 */
	public function getSubUser ($username) {
		$this->debug("Getting subuser '$username'");
		$params = array('task' => 'get', 'username' => $username);
		$data = $this->retrieve('customer.profile.json', $params);

		if (empty($data)) {
			throw new InvalidArgumentException("No subuser '$username' found.");
		}

		extract(get_object_vars($data[0]));
		return new SubUser($username, null, $email, null, $first_name, $last_name,
						   $address, $city, $state, $zip, $country, $phone, $website,
						   $this);
	}

	/**
	 * Send an API call to "retrieve something".
	 *
	 * @param array $params Query parameters to supply.
	 */
	public function retrieve ($action, array $params) {
		$url = self::ROOT_URL . $action
				. "?api_user={$this->apiUser}&api_key={$this->apiKey}&"
				. http_build_query($params);

		$this->debug("Querying URL: $url");
		curl_setopt($this->ch, CURLOPT_URL, $url);
		$data = curl_exec($this->ch);
		$decoded = json_decode($data);

		if (empty($decoded)) {
			$error_code = json_last_error();
			throw new RuntimeException("Error retrieving data. No Internet connection? [$error_code]");
		}

		if (isset($decoded->error)) {
			$code = $decoded->error->code;
			$msg = $decoded->error->message;

			if ($code == 401) {
				$msg .= " (Attempted user '{$this->apiUser}' with key '{$this->apiKey}')";
			}

			throw new LogicException($msg . " [Code: $code]");
		}

		return $decoded;
	}

	/**
	 * Send an API call to "do something".
	 *
	 * @param array $params     Query parameters to supply.
	 * @param array $postParams If supplied, the request becomes a POST with these
	 *                          POST parameters.
	 */
	public function execute ($action, array $params, array $postParams = null) {
		if (is_array($postParams)) {
			$this->debug("Adding POST parameters: " . print_r($postParams, true));
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postParams);
		}

		$json = $this->retrieve($action, $params);
		if ($json->message != 'success') {
			throw new LogicException($json->errors[0]);
		}

		return $json;
	}

	/**
	 * Helper debug method.
	 */
	public function debug ($msg) {
		if ($this->isDebugging) {
			echo $msg . "\n";
		}
	}
}
