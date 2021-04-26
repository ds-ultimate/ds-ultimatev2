<?php

namespace App\Util;

use Carbon\Carbon;

/**
 * Custom Class to perform outgoing HTTP Requests
 * Saves the Headers for later use / checking
 * Customizable Request Headers
 */
class HTTPRequests
{
    private static $PROTOCOL_HTTP = 1;
    private static $PROTOCOL_HTTPS = 2;
    private $protocol = 0;
    
    private $url;
    private $server;
    private $port = null;
    
    private $reqType = "GET";
    private $reqVersion = "HTTP/1.1";
    private $reqHeaders = [];
    private $reqData = null;
    private $respHeaders = [];
    private $respData = null;
    private $respCode = 0;
    private $respName = null;
    
    public function __construct($url) {
        if(BasicFunctions::startsWith($url, "https://")) {
            $this->protocol = 2;
            $url = substr($url, 8);
        } else if(BasicFunctions::startsWith($url, "http://")) {
            $this->protocol = 1;
            $url = substr($url, 7);
        } else {
            throw new \Exception("Unknown protocol");
        }
        
        $sep = strpos($url, "/");
        if($sep === false) {
            $this->url = "/";
            $this->server = $url;
        } else {
            $this->url = substr($url, $sep);
            $this->server = substr($url, 0, $sep);
        }
        
        $portPos = strpos($this->server, ":");
        if($portPos !== false) {
            //port given
            $this->port = intval(substr($this->server, $portPos + 1));
            $this->server = substr($this->server, 0, $portPos);
        }
        
        $this->setHeader("Host", $this->server . (($this->port == null)?(""):(":" . $this->port)));
        $this->setHeader("Accept-Encoding", "chunked");
    }
    
    public function send() {
        if($this->protocol == static::$PROTOCOL_HTTP) {
            $hostname = $this->server;
            $port = $this->port ?? 80;
        } else if($this->protocol == static::$PROTOCOL_HTTPS) {
            $hostname = "ssl://" . $this->server;
            $port = $this->port ?? 443;
        } else {
            throw new \Exception("Unknown protocol");
        }
        
        $data = "{$this->reqType} {$this->url} {$this->reqVersion}\r\n";
        foreach($this->reqHeaders as $key => $value) {
            $data .= "$key: $value\r\n";
        }
        $data .= "\r\n";
        
        if($this->reqData !== null) {
            $data .= $this->reqData;
            $data .= "\r\n";
        }
        
        $socket = fsockopen($hostname, $port, $errno, $errMess, 10);
        fwrite($socket, $data);
        
        $firstLine = explode(" ", trim(fgets($socket)), 3);
        $this->respCode = intval($firstLine[1]);
        $this->respName = $firstLine[2];
        
        do {
            $line = trim(fgets($socket));
            $idx = strpos($line, ":");
            if($idx !== false) {
                $key = trim(substr($line, 0, $idx));
                $val = trim(substr($line, $idx + 1));
                $this->respHeaders[$key] = $val;
            }
        } while($line !== "");

        if(!isset($this->respHeaders["Transfer-Encoding"]) ||
                $this->respHeaders["Transfer-Encoding"] == "identity") {
            if(! isset($this->respHeaders["Content-Length"])) {
                throw new \Exception("Need lenght");
            }
            $len = $this->respHeaders["Content-Length"];
            //identity read
            $this->respData = stream_get_contents($socket, $len);
        } else if($this->respHeaders["Transfer-Encoding"] == "chunked") {
            $this->respData = "";
            do {
                $line = trim(fgets($socket));
                $chunkSize = hexdec($line);
                if($chunkSize > 0) {
                    $read = "";
                    while(strlen($read) < $chunkSize) {
                        $read .= fread($socket, $chunkSize - strlen($read));
                    }
                    $this->respData .= $read;
                    $end = fread($socket, 2); //throw away chunk ending
                    if($end != "\r\n") {
                        throw new \Exception("Invalid chunk ending [$end]");
                    }
                }
            } while($chunkSize > 0);
        } else {
            throw new \Exception("Unknown encoding " . $this->respHeaders["Transfer-Encoding"]);
        }
        
        fclose($socket);
        return $this;
    }
    
    public function gunzipData() {
        $this->respData = gzdecode($this->respData);
        return $this;
    }
    
    public function setHeader($name, $value) {
        $this->reqHeaders[$name] = $value;
    }
    
    public function setRequestData($data) {
        $this->reqData = $data;
    }
    
    public function responseCode() {
        return $this->respCode;
    }
    
    public function responseHeader($key) {
        return $this->respHeaders[$key] ?? null;
    }
    
    public function modificationTime() {
        $lastMod = $this->responseHeader("Last-Modified");
        if($lastMod == null) return Carbon::now();
        
        $time = Carbon::parse($lastMod);
        $time->setTimezone(config('app.timezone'));
        return $time;
    }
    
    public function responseData() {
        return $this->respData;
    }
}
