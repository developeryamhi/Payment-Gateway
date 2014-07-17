<?php namespace Developeryamhi\PaymentGateway\Object;

class PropertyCollection {

    //  Properties
    private $properties = array();

    //  Call Map Cache
    private $map_cache = array();


    //  Add Property to Collection
    public function addItem($index, Property $property) {

        //  Store
        $this->properties[$index] = $property;
    }

    //  Remove Property from Collection
    public function removeItem($index) {

        //  Clear
        if(isset($this->properties[$index])) {

            //  Unset
            unset($this->properties[$index]);
        }
    }

    //  Get Property from Collection
    public function getItem($index) {

        //  Check Exists
        if($this->hasItem($index)) {

            //  Return
            return $this->properties[$index];
        }
    }

    //  Check Exists
    public function hasItem($index) {
        return (isset($this->properties[$index]));
    }

    //  Get All Items
    public function getItems() {
        return $this->properties;
    }

    //  Reset
    public function reset() {

        //  Reset
        $this->properties = array();
    }

    //  Reset Items
    public function resetItems() {

        //  Loop Each
        foreach($this->properties as $property) {

            //  Reset
            $property->reset();
        }
    }

    //  To XML
    public function toXML($rootElem = 'root', $propertyElem = 'property') {

        //  XML Document
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

        //  Open Root Node
        $xml .= "<{$rootElem}>" . PHP_EOL;

        //  Loop Each
        foreach($this->properties as $index => $property) {

            //  Open Property Root Node
            $xml .= "\t<{$propertyElem} index='{$index}'>" . PHP_EOL;

            //  Generate Inner XML Nodes
            $xml .= $property->_toXML(null, 2);

            //  Open Property Root Node
            $xml .= "\t</{$propertyElem}>" . PHP_EOL;
        }

        //  Close Root Node
        $xml .= "</{$rootElem}>" . PHP_EOL;

        //  Return the XMLDocument
        return ($rootElem ? simplexml_load_string($xml) : $xml);
    }

    //  To String
    public function __toString() {
        return '#' . __CLASS__;
    }

    //  Listen Every Call
    public function __call($name, $arguments) {

        //  Callback Info
        $callback_info = null;

        //  Check for Map Cache
        if(isset($this->map_cache[$name]))
            $callback_info = $this->map_cache[$name];
        else {

            //  Loop Each Item Keys
            foreach(array_keys($this->properties) as $prop_key) {

                //  Check
                if(substr($name, 0, strlen($prop_key) + 1) == $prop_key . '_') {

                    //  Method to Call
                    $method = substr($name, strlen($prop_key) + 1);

                    //  Set Info
                    $callback_info = array(
                        'key' => $prop_key,
                        'method' => $method
                    );

                    //  Store Cache
                    $this->map_cache[$name] = $callback_info;

                    break;
                }
            }
        }

        //  Check
        if($callback_info) {

            //  Get Item
            $item = $this->getItem($callback_info['key']);

            //  Check Method Exists
            if(method_exists($item, $callback_info['method'])) {

                //  Run Callback
                return call_user_func_array(array($item, $callback_info['method']), $arguments);
            }
        }
    }
}