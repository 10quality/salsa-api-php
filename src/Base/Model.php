<?php

namespace Salsa\Base;

use Salsa\Api;

/**
 * Base model class.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.0
 * @package Salsa
 * @license MIT
 */
abstract class Model
{
    /**
     * Attributes data.
     * @since 1.0.0
     * @var array 
     */
    protected $attributes = array();
    /**
     * Valid properties.
     * @since 1.0.0
     * @var array 
     */
    protected $properties = array();
    /**
     * Returns property value.
     * GETTER function.
     * @since 1.0.0
     *
     * @param string $property Property name.
     *
     * @return mixed 
     */
    public function &__get($property)
    {
        if (isset($this->attributes[$property]))
            return $this->attributes[$property];
        if (property_exists($this, $property))
            return $this->$property;
        $value = null;
        return $value;
    }
    /**
     * Sets an attribute value.
     * SETTER function.
     * @since 1.0.0
     *
     * @param string $property Property/attribute name.
     * @param mixed  $value    Value.
     */
    public function __set($property, $value)
    {
        if ($property === 'attributes') {
            $this->attributes = $value;
        } else {
            $this->attributes[$property] = $value;
        }
    }
    /**
     * Returns model as a valid array.
     * Attributes are filtered by the list of valid properties.
     * @since 1.0.0
     *
     * @return array
     */
    public function toArray()
    {
        $output = array();
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $this->properties))
                $output[$key] = method_exists($this, $key.'Transform')
                    ? $this->{$key.'Transform'}($value)
                    : $value;
        }
        $this->onCasting($output);
        return $output;
    }
    /**
     * Called when casting model to array to let parent classes override and add special content.
     * @since 1.0.0
     *
     * @param array $output Output
     */
    protected function onCasting(array &$output)
    {
        // TODO on parent
    }
    /**
     * Returns model as string.
     * @since 1.0.0
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray());
    }
}