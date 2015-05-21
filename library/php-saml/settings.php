<?php

$settings = array (
    // If 'strict' is True, then the PHP Toolkit will reject unsigned
    // or unencrypted messages if it expects them signed or encrypted
    // Also will reject the messages if not strictly follow the SAML
    // standard: Destination, NameId, Conditions ... are validated too.
    'strict' => false,

    // Enable debug mode (to print errors)
    'debug' => true,

    // Service Provider Data that we are deploying
    'sp' => array (
        // Identifier of the SP entity  (must be a URI)
//TODO: per ora dummy, non sembra necessario -- nessun vero flow SAML
        'entityId' => 'dummy',
        // Specifies info about where and how the <AuthnResponse> message MUST be
        // returned to the requester, in this case our SP.
        'assertionConsumerService' => array (
            // URL Location where the <Response> from the IdP will be returned
//TODO: per ora dummy, non sembra necessario -- nessun vero flow SAML
            'url' => 'https://dummy',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ),
        // Specifies info about where and how the <Logout Response> message MUST be
        // returned to the requester, in this case our SP.
        'singleLogoutService' => array (
            // URL Location where the <Response> from the IdP will be returned
//TODO: per ora dummy, non sembra necessario -- nessun vero flow SAML
            'url' => 'https://dummy',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // Specifies constraints on the name identifier to be used to
        // represent the requested subject.
        // Take a look on lib/Saml2/Constants.php to see the NameIdFormat supported
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',

        // Usually x509cert and privateKey of the SP are provided by files placed at
        // the certs folder. But we can also provide them with the following parameters
        'x509cert' => '',
        'privateKey' > '',
    ),

    // Identity Provider Data that we want connect with our SP
    'idp' => array (
        // Identifier of the IdP entity  (must be a URI)
//TODO: per ora dummy, non sembra necessario -- nessun vero flow SAML
        'entityId' => 'https://dummy',
        // SSO endpoint info of the IdP. (Authentication Request protocol)
        'singleSignOnService' => array (
            // URL Target of the IdP where the SP will send the Authentication Request Message
//TODO: per ora dummy, non sembra necessario -- nessun vero flow SAML
            'url' => 'https://dummy',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-POST binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // SLO endpoint info of the IdP.
        'singleLogoutService' => array (
            // URL Location of the IdP where the SP will send the SLO Request
            'url' => '',
            // SAML protocol binding to be used when returning the <Response>
            // message.  Onelogin Toolkit supports for this endpoint the
            // HTTP-Redirect binding only
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ),
        // Public x509 certificate of the IdP
        'x509cert' => '-----BEGIN CERTIFICATE-----
MIIDSzCCAjOgAwIBAgIJAKeRY/5t5tc0MA0GCSqGSIb3DQEBCwUAMDwxEzARBgNV
BAMMCkl2YW4gTHVjY2kxJTAjBgkqhkiG9w0BCQEWFmdhbGtpbWFzZXJhOUBnbWFp
bC5jb20wHhcNMTUwMTE0MTExMTI1WhcNMjUwMTExMTExMTI1WjA8MRMwEQYDVQQD
DApJdmFuIEx1Y2NpMSUwIwYJKoZIhvcNAQkBFhZnYWxraW1hc2VyYTlAZ21haWwu
Y29tMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxDY3ozGA3mNYM1eR
1GeffChdlqkUYbEmOSk+xZQ2yf6Kx7ugxMqWq2nf7I6G93EwWLs+8I94+wqqCcIp
92nrzDyIRp5jtd0Sk8AWc5hME1aaDks8usVye3ELBn8uZrBQ9HMjn4I2p1o8ghvM
KG65UKFqarNlGtl7YNTi+uBJYRUE7o88ci0bs18fluTwuwVWky2nKwMTcA/fOiGV
7avd1Qn+VZD9tHejHzhbSwKdqAQNelT/khNc8POYb4o+4lQf+6gnWfgT/qITRKAi
s9nxAhNz/dNq6dXIJFa9ZPc8iWBbRMx/UERAZGwieODBNGrFcYYQGVyvbQbz0ljd
H4tJDQIDAQABo1AwTjAdBgNVHQ4EFgQUpeU3w905ziItrmXX20ky6s89EMQwHwYD
VR0jBBgwFoAUpeU3w905ziItrmXX20ky6s89EMQwDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQsFAAOCAQEAC004mnEWkSCSLcwVF4fEqssOPF+SBpCnMhK7yxylP3m5
dgFmxiu0NJV1294xCm90L5JDZEPhOiUIfjdFWWaN9r4+t6KlYwKmYnlhGEn0q/RD
mWdz185Ez7YqAfM5lWgmJJJGYYLMo0t9lpOdAoY7OsgjzwWN8J/Lwo6e5IyYFwjH
49EzDK5ZRPf23Tm96lE2wtvRWwNlXfRPTxZSEu1YOrBkMAL7IVV2Dz4BaXPvQelS
P98CkRoQ3QAChESWYNcqGm2r1omsCdVvJNYikLhLY7fn+8/OOyRAZTIKGzLde3I6
BvvzpnR5a4xlQzsXaVtPz8Ta7kog9zeOoQum6nJcLg==
-----END CERTIFICATE-----',
        /*
         *  Instead of use the whole x509cert you can use a fingerprint
         *  (openssl x509 -noout -fingerprint -in "idp.crt" to generate it)
         */
        // 'certFingerprint' => '',
    ),
);
