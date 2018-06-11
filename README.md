# SwagFixDeprecatedTlsInSmtp

## What does this plugin do?

This plugin patches the Zend_Mail_Protocol_Smtp class to allow all available encryption methods (TLS 1.0, TLS 1.1 or TLS 1.2) to be used if the mail server supports it.

## Why is this patch necessary?

The PHP constant `STREAM_CRYPTO_METHOD_TLS_CLIENT` changes it's values between versions: PHP versions 5.6.7 - 5.6.30 and 7.0.0 - 7.1.17 defined the constant as "Only TLS 1.0 is supported", leading to the result that only this encryption method is provided to an SMTP server as an available encryption option. Other PHP versions support multiple available encryption methods in this constant, resulting in an automatic upgrade of the encryption if the mail server requires it.

Since TLS 1.0 has been deprecated a long time, some mail hosters are starting to disable the support for it, resulting in shops running one of the broken PHP versions not being able to send any email.

## Installation

* Upload the plugin with plugin manager
* Install the plugin and activate it
* Clear all caches