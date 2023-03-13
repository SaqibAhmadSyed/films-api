<?php

namespace Vanier\Api\Validations;

use DateTime;


/**
 * Summary of Input
 */
class Input
{

    /** checks if the single value is a letter
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function isAlpha($value)
    {
        $value = filter_var(trim($value), FILTER_SANITIZE_ADD_SLASHES);
        if (ctype_alpha($value)) {
            return $value;
        }
        return false;
    }

    /** checks if the given sentence in the stirng contains only letters
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function isOnlyAlpha($value)
    {
        $value = preg_replace('/[^a-zA-Z]/', '', $value); // remove non-letter characters
        if (ctype_alpha($value)) {
            return true;
        }
        return false;
    }

    /** checks if the given value is an round number
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function isInt($value)
    {
        //also makes sure the given value is checked as an int if a string is given
        if (is_numeric($value) && intval($value) == $value) {
            return true;
        }
        return false;
    }

    /** checks if the given string is a decimal number with 2 decimal places
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function isInDecimal($value)
    {
        $value = strval($value);
        if (preg_match('/^\d+(\.\d{1,2}0*)?$/', $value)) {
            return true;
        }
        return false;
    }


    /**
     * checks if the given value is in the array
     * @param mixed $value
     * @param mixed $array array to be compared
     * 
     * @return boolean
     */
    public static function isInArray($value, $array)
    {
        // Split the input value into an array of items
        $items = explode(',', $value);
        foreach ($items as $item) {
            // Trim whitespace and convert to uppercase for case-insensitive comparison
            $items = strtolower(trim($item));
            if (!in_array($item, $array)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validates if the string is equals/contains the given substring
     * @param string $value
     * @param string $substring
     * 
     * @return [type]
     */
    public static function stringContains(string $value, string $substring)
    {
        //need both of them to be lower case to compare
        $value = strtolower($value);
        $substring = strtolower($substring);

        if (strpos($value, $substring) == false) {
            return false;
        }
        return true;
    }

    /**
     * Checks if a date follows the established standard for the sake of consistency
     * @param mixed $value
     * 
     * @return [type]
     */
    public static function isFormattedDate($value)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $value);
    }

    public static function isEmail($value)
    {
        $pattern = "/^[a-zA-Z]+\\.[a-zA-Z]+\\.[a-zA-Z]+@sakilacustomer\\.org$/";
        return preg_match($pattern, $value) == 1;
    }

    /**
     * Checks whether a value is int and is within a range.
     * @param mixed $value
     * @param int $min
     * @param int $max
     * @return bool|array
     */
    public static function isIntInRange($value, int $min, int $max)
    {
        return filter_var($value, FILTER_VALIDATE_INT, static::getRangeOptions($min, $max));
    }


    /**
     * Checks whether a value is a valid int or not.
     * If the min value is provided and it's greater than 0, 
     * it verifies if the value is > min.
     * @param mixed $input
     * @return mixed bool|array
     */
    public static function isIntOrGreaterThan($input, int $min = -1): mixed
    {
        if ($min >= 0) {
            return filter_var($input, FILTER_VALIDATE_INT, self::getMinRangeOptions($min));
        }

        return filter_var($input, FILTER_VALIDATE_INT);
    }

    public static function getMinRangeOptions(int $min): array
    {
        return array("options" => array("min_range" => $min));
    }

    public static function getRangeOptions(int $min, int $max): array
    {
        return array(
            "options" =>
            array("min_range" => $min, "max_range" => $max)
        );
    }

    /**
     * Determines whether an array is associative or not.     
     * 
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     * Note that an array in PHP can be either sequential or associative. 
     *
     * @param  array  $array the array to be verified.
     * @return bool
     */
    public static function isAssoc(array $input): bool
    {
        if (empty($input)) {
            return false;
        }
        $keys = array_keys($input);
        return array_keys($keys) !== $keys;
    }
}
