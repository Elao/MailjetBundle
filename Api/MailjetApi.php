<?php

/**
 * Mailjet Public API
 *
 * @package		API v0.1
 * @author		Mailjet
 * @link		http://api.mailjet.com/
 *
 */

namespace Elao\MailJetBundle\Api;

class MailjetApi {

    var $version = '0.1';

    # Choose your weapon : php, json, xml, serialize, html, csv
    var $output = 'php';

    # Connect thru https protocol
    var $secure = true;

    # Edit with your Mailjet Infos
    var $apiKey = '';
    var $secretKey = '';

    // Constructor function
    function __construct() {
        $this->apiUrl = (($this->secure) ? 'https' : 'http') . '://api.mailjet.com/' . $this->version . '';
    }

    function __call($method, $args) {

        # params
        $params = $args[0];

        # request method
        $request = isset($params["method"]) ? strtoupper($params["method"]) : 'GET';
        unset($params["method"]);

        # Make request
        $result = $this->sendRequest($method, $params, $request);

        # Return result
        return ($result === true) ? $this->_response : false;
    }

    function requestUrlBuilder($method, $params=array (), $request) {

        $query_string = array ('output' => 'output=' . $this->output);

        foreach ($params as $key => $value) {
            if ($request == "GET" || in_array($key, array ('output')))
                $query_string[$key] = $key . '=' . urlencode($value);
            if ($key == "output")
                $this->output = $value;
        }

        $this->call_url = $this->apiUrl . '/' . $method . '/?' . join('&', $query_string);

        return $this->call_url;
    }

    function sendRequest($method = false, $params=array (), $request="GET") {

        # Method
        $this->_method = $method;
        $this->_request = $request;

        # Build request URL
        $url = $this->requestUrlBuilder($method, $params, $request);

        # Set up and execute the curl process  
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 2);

        if ($method == 'POST') :
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $params);
        endif;

        curl_setopt($curl_handle, CURLOPT_USERPWD, $this->apiKey . ':' . $this->secretKey);


        $buffer = curl_exec($curl_handle);


        // Response code
        $this->_response_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        // Close curl process
        curl_close($curl_handle);


        /* RESPONSE */
        $this->_response = ($this->output == 'json') ? json_decode($buffer) : $buffer;
        return ($this->_response_code == 200) ? true : false;
    }

    function debug() {

        echo '<style type="text/css">';
        echo '
		
			#debugger {width: 100%; font-family: arial;}
			#debugger table {padding: 0; margin: 0 0 20px; width: 100%; font-size: 11px; text-align: left;border-collapse: collapse;}
			#debugger th, #debugger td {padding: 2px 4px;}
			#debugger tr.h {background: #999; color: #fff;}
			#debugger tr.Success {background:#90c306; color: #fff;}
			#debugger tr.Error {background:#c30029 ; color: #fff;}
			#debugger tr.Not-modified {background:orange ; color: #fff;}
			#debugger th {width: 20%;}
		
			';
        echo '</style>';

        echo '<div id="debugger">';



        if (isset($this->_response_code)) :

            if ($this->_response_code == 200) :

                echo '<table>';
                echo '<tr class="Success"><th>Success</th><td></td></tr>';
                echo '<tr><th>Status code</th><td>' . $this->_response_code . '</td></tr>';
                if (isset($this->_response)) :
                    echo '<tr><th>Response</th><td><pre>' . utf8_decode(print_r($this->_response, 1)) . '</pre></td></tr>';
                endif;
                echo '</table>';
            elseif ($this->_response_code == 304) :

                echo '<table>';
                echo '<tr class="Not-modified"><th>Error</th><td></td></tr>';
                echo '<tr><th>Error no</th><td>' . $this->_response_code . '</td></tr>';
                echo '<tr><th>Message</th><td>Not Modified</td></tr>';
                echo '</table>';

            else :

                echo '<table>';
                echo '<tr class="Error"><th>Error</th><td></td></tr>';
                echo '<tr><th>Error no</th><td>' . $this->_response_code . '</td></tr>';
                if (isset($this->_response->status)) :
                    echo '<tr><th>Status</th><td><pre>' . $this->_response->status . '</pre></td></tr>';
                endif;
                if (isset($this->_response) && count((array) $this->_response) > 1) :
                    unset($this->_response->status);
                    echo '<tr><th>Response</th><td><pre>' . print_r($this->_response, 1) . '</pre></td></tr>';
                endif;
                echo '</table>';

            endif;


        endif;



        $call_url = parse_url($this->call_url);

        echo '<table>';
        echo '<tr class="h"><th>API config</th><td></td></tr>';
        echo '<tr><th>Protocole</th><td>' . $call_url['scheme'] . '</td></tr>';
        echo '<tr><th>Host</th><td>' . $call_url['host'] . '</td></tr>';
        echo '<tr><th>Version</th><td>' . $this->version . '</td></tr>';
        echo '</table>';


        echo '<table>';
        echo '<tr class="h"><th>Call infos</th><td></td></tr>';
        echo '<tr><th>Method</th><td>' . $this->_method . '</td></tr>';
        echo '<tr><th>Request type</th><td>' . $this->_request . '</td></tr>';
        echo '<tr><th>Arguments</th><td>';

        $args = explode("&", $call_url['query']);
        foreach ($args as $arg) {
            list($key, $value) = explode("=", $arg);
            echo '' . $key . ' = <span style="color:#ff6e56;">' . $value . '</span><br/>';
        }

        echo '</td></tr>';

        echo '<tr><th>Call url</th><td>' . $this->call_url . '</td></tr>';
        echo '</table>';


        echo '</div>';
    }
}