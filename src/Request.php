<?php

namespace Hsoderlind\FatfreePlugins\Http;

use Ramsey\Uuid\Uuid;

class Request
{

    /**
     * @var \Base
     */
    private $_f3;

    /**
     * @var Header
     */
    private $_headers;

    /**
     * @var DataContainer
     */
    private $_dataContainer;

    /**
     * @var string
     */
    private $_storageKey;

    public function __construct($f3)
    {
        $this->_f3 = $f3;
        $this->_storageKey = Uuid::uuid4()->toString();
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (method_exists($this, "set$name")) {
            $class = get_called_class();
            throw new \Exception("Property $name in class $class is writeonly");
        } else {
            $class = get_called_class();
            throw new \Exception("Property $name is missing in class $class");
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method($value);
        } elseif (method_exists($this, "get$name")) {
            $class = get_called_class();
            throw new \Exception("Property $name in class $class is readonly");
        } else {
            $class = get_called_class();
            throw new \Exception("Property $name is missing in class $class");
        }
    }

    public function __isset($name)
    {
        $method = 'get' . $name;
        if (method_exists($this, $method)) {
            return $this->$method() !== null;
        } else {
            $class = get_called_class();
            throw new \Exception("Property $name is missing in class $class");
        }
    }

    public function __unset($name)
    {
        $method = 'set' . $name;
        if (method_exists($this, $method)) {
            return $this->$method(null);
        } elseif (method_exists($this, "get$name")) {
            $class = get_called_class();
            throw new \Exception("Property $name in class $class is readonly");
        } else {
            $class = get_called_class();
            throw new \Exception("Property $name is missing in class $class");
        }
    }

    /**
     * Method of request (POST, GET, PUT, DELETE)
     * @return string the request method
     */
    public function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if request method is GET
     * @return boolean
     */
    public function isGet()
    {
        return $this->getRequestMethod() === 'GET';
    }

    /**
     * Check if request method is POST
     * @return boolean
     */
    public function isPost()
    {
        return $this->getRequestMethod() === 'POST';
    }

    /**
     * Check if request method is PUT
     * @return boolean
     */
    public function isPut()
    {
        return $this->getRequestMethod() === 'PUT';
    }

    /**
     * Check if request method is DELETE
     * @return boolean
     */
    public function isDelete()
    {
        return $this->getRequestMethod() === 'DELETE';
    }

    public function getRawBody()
    {
        return file_get_contents("php://input");
    }

    public function get($param = null)
    {
        if ($param === null) {
            return $_GET;
        } else {
            foreach ($_GET as $key => $value) {
                if ($key === $param) {
                    return $value;
                    break;
                }
            }
        }
    }

    public function post($param = null)
    {
        return (isset($_POST) ? $_POST : $this->getRawBody());
    }

    public function getHeaders()
    {
        if ($this->_headers === null) {
            $this->_headers = new Header();
            $this->_headers->fetchAllRequestHeaders();
        }

        return $this->_headers;
    }

    public function getData()
    {
        if ($this->_dataContainer === null) {
            $this->_dataContainer = new DataContainer($this->_storageKey);
        }

        return $this->_dataContainer;
    }
}
