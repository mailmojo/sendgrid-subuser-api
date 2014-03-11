PHP SendGrid Subuser API
========================

This PHP library makes it easy to work with the `SendGrid Subuser API`_.
It was made to scratch our own itch, and as such it currently only
implements the parts of the API that we needed. Please feel free to
request additional features or send pull requests.

.. _SendGrid Subuser API: http://docs.sendgrid.com/documentation/api/customer-subuser-api/customer-api/

Basic Usage
-----------

::

    $sendgrid = new SendGrid\Api("my_username", "my_password");
    // Create a new sub user
    $user = new SendGrid\SubUser("wanted_username", "password",
            "email@example.com", "email.domain.example.com");
    $sendgrid->addSubUser($user);
    // Retrieve existing sub user
    $other = $sendgrid->getSubUser("sub_username");
    // Assign IPs to a user
    $other->assignIps(array("1.2.3.4"));

See ``example/example.php`` for more examples.
