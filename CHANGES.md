## 1.6.4
* Will use POST instead of GET when retrieving result.

## 1.6.3
* Add minimum TLS 1.2 version to curl options as protocol negotiation on certain openssl/libcurl versions is flaky.

## 1.6.2
* Remove deprecated curl constant (https://php.watch/versions/8.4/CURLOPT_BINARYTRANSFER-deprecated)

## 1.6.1
* Fixed string interpolation for php 8.2: https://wiki.php.net/rfc/deprecate_dollar_brace_string_interpolation

## 1.6.0
* Support to run the unittests on newer versions of PHP (5.5 +)
* Add API methods for converting/transcoding and transformation
* Add helper function for returning the compressed file extension

## 1.5.2
* Fail early if version of curl/openssl is too old.

## 1.5.1
* Expose status of exceptions.

## 1.5.0
* Retry failed requests by default.
* Throw clearer errors when curl is installed but disabled.

## 1.4.0
* Support for HTTP proxies.
