<?php 

class Application_Model_Bogus {
    
    public function __get($name) {
        return $name;
    }
    
    public function __call($method, $args) {
        return $method;
    }
    
    public function __set($name, $value) {
        return $name;
    }
}