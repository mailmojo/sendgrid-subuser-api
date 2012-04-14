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

License
-------

This library is licensed under The BSD License:

| Copyright (c) 2012, Eliksir AS.
| All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are
met:

- Redistributions of source code must retain the above copyright notice,
  this list of conditions and the following disclaimer.

- Redistributions in binary form must reproduce the above copyright
  notice, this list of conditions and the following disclaimer in the
  documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
