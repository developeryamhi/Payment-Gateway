<?php namespace Developeryamhi\PaymentGateway\Object;

class Property extends \stdClass {

    //  Assign Value
    public function assign($key, $val) {

        //  Assign
        $this->{$key} = $val;
    }

    //  Unassign Value
    public function unassign($key) {

        //  Check Exists
        if($this->exists($key)) {

            //  Unset
            unset($this->$key);
        }
    }

    //  Check Exists
    public function exists($key) {
        return (isset($this->{$key}));
    }

    //  Reset
    public function reset() {

        //  Loop Each
        foreach($this as $key => $val) {

            //  Unset
            unset($this->{$key});
        }
    }

    //  Copy Array
    public function copyArray($array, $clear = true) {

        //  Check for Clear
        if($clear)  $this->reset();

        //  Loop Each
        foreach($array as $key => $val) {

            //  Set
            $this->{$key} = $val;
        }
    }

    //  Copy Object
    public function copyObject($obj, $clear = true) {

        //  Port to Array Copier
        $this->copyArray(self::object_to_array($obj), $clear);
    }

    //  Copy XML
    public function copyXML($xml, $clear = true) {

        //  Port to Array Copier
        $this->copyArray(self::object_to_array($obj), $clear);
    }

    //  To Array
    public function toArray() {

        //  Get Array
        $array = self::object_to_array($this);

        //  Return
        return $array;
    }

    //  To JSON
    public function toJSON() {

        //  Get the Array
        $array = $this->toArray();

        //  Return JSON
        return json_encode($array);
    }

    //  To XML
    public function toXML($rootElem = 'root') {

        //  XML
        $xml = '';

        //  XML Node
        if($rootElem)   $xml .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

        //  Open Root Node
        if($rootElem)   $xml .= "<{$rootElem}>" . PHP_EOL;

        //  Generate Inner XML Nodes
        $xml .= $this->_toXML();

        //  Close Root Node
        if($rootElem)   $xml .= "</{$rootElem}>" . PHP_EOL;

        //  Return the XMLDocument
        return ($rootElem ? simplexml_load_string($xml) : $xml);
    }

    //  Create XML Nodes
    public function _toXML($obj, $level = 1) {

        //  Check Object
        if(!$obj)   $obj = $this;

        //  Output
        $output = '';

        //  Loop
        foreach($obj as $key => $val) {

            //  Check
            if(is_array($val) || is_object($val)) {

                //  Go-Recursive
                $output .= str_repeat("\t", $level) . "<{$key}>" . PHP_EOL;
                $output .= $this->_toXML($val, $level + 1);
                $output .= str_repeat("\t", $level) . "</{$key}>" . PHP_EOL;
            } else {

                //  Append
                $output .= str_repeat("\t", $level) . "<{$key}>" . (is_numeric($val) ? $val : (string)$val) . "</{$key}>" . PHP_EOL;
            }
        }

        //  Return
        return $output;
    }

    //  Convert Object to Array
    public static function object_to_array($obj) {
        $arr = array();
        if($obj) {
            foreach($obj as $key => $val) {
                if(is_object($val) || is_array($val)) {
                    $arr[$key] = self::object_to_array($val);
                } else {
                    $arr[$key] = $val;
                }
            }
        }
        return $arr;
    }

    //  Convert Array to Object
    public static function array_to_object($arr) {
        $obj = new \stdClass();
        if($arr) {
            foreach($arr as $key => $val) {
                if(is_object($val) || is_array($val)) {
                    $obj->{$key} = self::array_to_object($val);
                } else {
                    $obj->{$key} = $val;
                }
            }
        }
        return $obj;
    }
}