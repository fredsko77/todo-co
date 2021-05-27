<?php

namespace App\Traits;

use DateTime;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Symfony\Component\Validator\ConstraintViolationList;

trait ServicesTrait
{

    /**
     * now
     * @return string
     */
    public function now(): DateTime
    {
        return new DateTime('now');
    }

    /**
     * @param string $class
     *
     * @return array $properties
     */
    public function getProperties(string $class): array
    {

        $reflect = new ReflectionClass($class);

        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $properties = [];

        foreach ($props as $prop) {
            array_push($properties, $prop->getName());
        }

        return $properties;
    }

    /**
     * getMethod
     * @param  string $str
     * @return string
     */
    public function getMethod(string $str): string
    {
        $needle = '_';
        $method = "";
        if (preg_match("#{$needle}#", $str)) {
            $array = preg_split("#{$needle}#", $str);
            if (is_array($array)) {
                foreach ($array as $v) {
                    $method .= ucfirst($v);
                }
            }
            return $method;
        }
        return ucfirst($str);
    }

    /**
     * Generate a token
     * @param integer $length
     * @return string
     */
    public function generateShuffleChars(int $length = 10): string
    {
        $char_to_shuffle = 'azertyuiopqsdfghjklwxcvbnAZERTYUIOPQSDFGHJKLLMWXCVBN1234567890';
        return substr(str_shuffle($char_to_shuffle), 0, $length);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generateToken(int $length = 50): string
    {
        return $this->generateShuffleChars($length) . (new DateTime)->format('YmdHisu');
    }

    /**
     * @return string
     */
    public function generateIdentifier(string $type = "REF"): string
    {
        return $type . '_' . $this->generateShuffleChars(5) . '-' . (new DateTime('now'))->format('YmdHisu');
    }

    /**
     * @param array $data
     * @param mixed $class
     *
     * @return object $instance
     */
    public function _hydrate(array $data = [], $class): object
    {
        $instance = gettype($class) === 'string' ? new $class() : $class;

        foreach ($data as $k => $d) {
            $method = 'set' . $this->getMethod($k);
            method_exists($instance, $method) ? $instance->$method($d) : null;
        }

        return $instance;
    }

    /**
     * @param object $errors
     * @param stdClass $response
     *
     * @return object|null
     */
    public function sendErrors(ConstraintViolationList $violations, stdClass $response)
    {

        if (count($violations) > 0) {

            $errors = [
                'title' => 'Validation failed !',
                'violations' => [],
            ];

            foreach ($violations as $violation) {
                $errors['violations'][$violation->getPropertyPath()] = $violation->getMessage();
            }

            if (!property_exists($response, 'headers')) {
                $response->headers = [];
            }

            $response->data = $errors;

            return $response;
        }

        return null;
    }

}
