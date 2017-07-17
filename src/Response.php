<?php

namespace Hsoderlind\FatfreePlugins\Http;

class Response
{
    /**
     * @var mixed
     */
    public $body;

    /**
     * @var \Base
     */
    private $_f3;

    /**
     * @var int
     */
    private $_statusCode = 200;

    /**
     * @var Header
     */
    private $_headers;

    public function __construct($f3)
    {
        $this->_f3 = $f3;
    }

    public function setStatusCode($statusCode)
    {
        $this->_statusCode = (int)$statusCode;
    }

    public function getHeaders()
    {
        if ($this->_headers === null) {
            $this->_headers = new Header();
        }

        return $this->_headers;
    }

    public function sendHeaders()
    {
        $headerIterator = $this->_headers->iterate();
        foreach ($headerIterator as $key => $value) {
            if (is_numeric($key)) {
                header($value);
            } else {
                header("$key: $value");
            }
        }
    }

    public function send($body = null, $statusCode = null)
    {
        if ($body !== null) {
            $this->body = $body;
        }

        $isJson = false;
        if (is_object($this->body) || is_array($this->body)) {
            $this->_headers['Content-Type'] = 'application/json';
            $isJson = true;
        }

        if ($statusCode !== null) {
            $this->_statusCode = (int)$statusCode;
        }

        http_response_code($this->_statusCode);
        $this->sendHeaders();

        echo $isJson ? json_encode($this->body) : (string)$this->body;
    }
}
