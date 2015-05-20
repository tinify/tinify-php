<?php

namespace Tinify;

class Client {
    const API_ENDPOINT = "https://api.tinify.com";

    private $options;

    public static function userAgent() {
        $curl = curl_version();
        return "Tinify/" . VERSION . " PHP/" . PHP_VERSION . " curl/" . $curl["version"];
    }

    private static function caBundle() {
        return __DIR__ . "/../data/cacert.pem";
    }

    function __construct($key, $app_identifier = NULL) {
        $this->options = array(
            CURLOPT_USERPWD => "api:" . $key,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_CAINFO => self::caBundle(),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => join(" ", array_filter(array(self::userAgent(), $app_identifier))),
        );
    }

    function request($method, $url, $body = NULL, $header = array()) {
        if ($body) {
            $body = json_encode($body);
            $header["Content-Type"] = "application/json";
        }

        $request = curl_init();
        $url = strtolower(substr($url, 0, 6)) == "https:" ? $url : Client::API_ENDPOINT . $url;
        curl_setopt_array($request, $this->options);
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_POSTFIELDS, $body);
        curl_setopt($request, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($request);

        if ($response === false) {
            curl_close($request);
            $message = sprintf("%s (#%d)", curl_error($request), curl_errno($request));
            throw new ConnectionException("Error while connecting: " . $message);
        } else {
            $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
            curl_close($request);

            $headers = self::parseHeaders(substr($response, 0, $headerSize));
            $body = substr($response, $headerSize);

            if ($status >= 200 && $status <= 299) {
                return array("body" => $body, "headers" => $headers);
            }

            $details = json_decode($body);
            if (!$details) {
                $details = (object) array(
                    "message" => "Error while parsing response: error #" . json_last_error(),
                    "error" => "ParseError"
                );
            }

            throw Exception::create($details->{"message"}, $details->{"error"}, $status);
        }
    }

    protected static function parseHeaders($headers) {
        if (!is_array($headers)) {
            $headers = explode("\r\n", $headers);
        }

        $res = array();
        foreach ($headers as $header) {
            $split = explode(":", $header, 2);
            if (count($split) === 2) {
                $res[strtolower($split[0])] = trim($split[1]);
            }
        }
        return $res;
    }
}
