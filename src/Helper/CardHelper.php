<?php namespace Developeryamhi\PaymentGateway;

class CardHelper {

    //  Get Card Type
    public static function getType($number) {

        //  Patterns
        $cards = array(
            "visa" => "(4\d{12}(?:\d{3})?)",
            "amex" => "(3[47]\d{13})",
            "jcb" => "((3[0-9]{4}|2131|1800)[0-9]{11}?)",
            "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
            "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
            "mastercard" => "(5[1-5]\d{14})",
            "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
            "diners" => "(3(?:0[0-5]|[68][0-9])[0-9]{11}?)",
            "discover" => "(6(?:011|5[0-9]{2})[0-9]{12}?)",
        );

        //  Match
        $matches = array();
        $pattern = "#^(?:".implode("|", $cards).")$#";
        $result = preg_match($pattern, str_replace(" ", "", $number), $matches);

        //  Keys
        $keys = array_keys($cards);

        //  Return
        return ($result > 0) ? $keys[sizeof($matches)-2] : null;
    }

    //  Match Card Type
    public static function isType($number, $type) {

        //  Match
        return (self::getType($number) == $type);
    }

    //  Get Card Type Label
    public static function typeLabel($type) {
        $labels = array(
            'visa' => 'Visa',
            'amex' => 'American Express',
            'jcb' => 'JCB',
            'maestro' => 'Maestro',
            'solo' => 'Solo',
            'mastercard' => 'Mastercard',
            'switch' => 'Switch',
            'diners' => 'Diners Club',
            'discover' => 'Discover'
        );
        return (isset($labels[$type]) ? $labels[$type] : $type);
    }

    //  Validate Credit Card Number
    public static function isValid($number) {

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number = preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length = strlen($number);
        $parity = $number_length % 2;

        // Loop through each digit and do the maths
        $total = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];

            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit *= 2;

                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            // Total up the digits
            $total += $digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? TRUE : FALSE;
    }
}