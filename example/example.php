<?php
// new Api("...", "...", false, true) to enable debug output
$sendgrid = new SendGrid\Api("my_username", "my_password");

// Initialize new user without actually creating it (the next step does that)
$user = new SendGrid\SubUser("wanted_username", "password", "email@example.com",
		"email.domain.example.com"); // Optional arguments are available

// Add the sub user to your account
$sendgrid->addSubUser($user);

// Assign an IP to the user
$user->assignIps(array("1.2.3.4"));

// Enable and configure an app
$user->enableApp("eventnotify", true, array(
		'processed' => false,
		'dropped' => true,
		'deferred' => false,
		'delivered' => false,
		'bounce' => true,
		'click' => false,
		'open' => false,
		'unsubscribe' => false,
		'spamreport' => true,
		'url' => 'http://example.com/url-to-event-receiver'));

// Instead of creating a user, we can retrieve one
$user = $sendgrid->getSubUser("another_sub_username");

// Get a list of available apps for the user
$apps = $user->getApps();

// Get current settings for a specific app
$settings = $user->getAppSettings("eventnotify");

// Delete the subuser
$user->delete();
