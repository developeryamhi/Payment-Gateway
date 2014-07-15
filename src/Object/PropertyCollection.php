<?php namespace Developeryamhi\PaymentGateway\Object;

class PropertyCollection {

    //  Properties
    private $properties = array();


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
    
}