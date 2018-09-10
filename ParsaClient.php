<?php
/**
 * Created by PhpStorm.
 * User: parsa
 * Date: 1/9/2018
 * Time: 10:44 AM
 */


class ParsaClient
{
    private $url;
    private $curl;
    private $result         = null;
    private $options        = [];
    private $parameters;
    private $postParameters = [];
    private $error          = null;
    private $headers        = [];



    /**
     * ParsaClient constructor.
     *
     * @param null $url
     */
    public function __construct($url = null)
    {
        $this->url  = $url;
        $this->curl = curl_init();
        //todo : make optional these
        $this->options = [
             CURLOPT_RETURNTRANSFER => true,   // return web page
             CURLOPT_USERAGENT      => "Ehda", // name of client
             CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
             CURLOPT_TIMEOUT        => 120,    // time-out on response
             CURLOPT_URL            => $this->url,
        ];
        curl_setopt_array($this->curl, $this->options);
    }



    /**
     * set user agent of request
     *
     * @param $value
     *
     * @return $this
     */
    public function setUserAgent($value)
    {
        curl_setopt($this->curl, CURLOPT_USERAGENT, $value);
        return $this;
    }



    /**
     * To make cURL follow a redirect
     *
     * @param $bool
     *
     * @return $this
     */
    public function setFollowRedirect($bool)
    {
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, $bool);
        return $this;
    }



    /**
     * set time out to response of request
     *
     * @param $time
     *
     * @return $this
     */
    public function setTimeOutToResponse($time)
    {
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $time);
        return $this;
    }



    /**
     * set time out to connect of request
     *
     * @param $time
     *
     * @return $this
     */
    public function setTimeOutToConnect($time)
    {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $time);
        return $this;
    }

    /**
     * when you want header only
     *
     * @return $this
     */
    public function dontNeedBody()
    {
        curl_setopt($this->curl, CURLOPT_NOBODY, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        return $this;
    }



    /**
     * when you want body and header
     *
     * @return $this
     */
    public function includeHeader()
    {
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        return $this;
    }



    /**
     * set header of request
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function setCustomHeader($key, $value)
    {
        $this->headers[] = "$key:$value";
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        return $this;
    }



    /**
     * make request with Get method
     *
     * todo : add array data feature like post method
     */
    public function get()
    {
        if (!is_null($this->parameters)) {
            curl_setopt($this->curl, CURLOPT_URL, $this->url . "?" . $this->parameters);
        }
        $this->result = curl_exec($this->curl);
        $this->error  = curl_error($this->curl);
    }



    /**
     * make request with Post method
     *
     * @param null $data
     */
    public function post($data = null)
    {
        if (!is_null($data)) {
            $this->postParameters = $data;
        }
        $jsonFields = json_encode($this->postParameters);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $jsonFields);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
             'Content-Type: application/json',
             'Content-Length:' . strlen($jsonFields),
        ]);

        $this->result = curl_exec($this->curl);
        $this->error  = curl_error($this->curl);
    }



    /**
     * close connection
     */
    public function close()
    {
        curl_close($this->curl);
    }



    /**
     * set url of request
     *
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url                  = $url;
        $this->options[CURLOPT_URL] = $this->url;
        curl_setopt_array($this->curl, $this->options);
        return $this;
    }



    /**
     * set parameter to send
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setParameter($name, $value)
    {
        $this->parameters            .= "&$name=$value";
        $this->postParameters[$name] = $value;
        return $this;
    }



    /**
     * return body of response
     *
     * @return HTTP_BODY
     */
    public function getResult()
    {
        return $this->result;
    }



    /**
     * return status of request
     *
     * @return HTTP status_code
     */
    public function getStatus()
    {
        $http_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        return $http_code;
    }



    /**
     * if error happen this show it
     *
     * @return HTTP error
     */
    public function getError()
    {
        return $this->error;
    }
}
