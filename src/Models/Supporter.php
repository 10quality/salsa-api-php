<?php

namespace Salsa\Models;

use Exception;
use Salsa\Base\Model;

/**
 * Salsas supporter data model.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.1
 * @package Salsa
 * @license MIT
 */
class Supporter extends Model
{
    /**
     * Valid properties.
     * @since 1.0.0
     * @var array 
     */
    protected $properties = array(
        'supporterId',
        'title',
        'firstName',
        'middleName',
        'lastName',
        'suffix',
        'dateOfBirth',
        'gender',
        'externalSystemId',
        'address',
    );
    /**
     * Custom fields.
     * @since 1.0.1
     * @var array
     */
    protected $customFields = array();
    /**
     * Returns property value.
     * GETTER function.
     * @since 1.0.1
     *
     * @param string $property Property name.
     *
     * @return mixed 
     */
    public function &__get($property)
    {
        if (isset($this->customFields[$property]))
            return $this->customFields[$property];
        return parent::__get($property);
    }
    /**
     * Sets an attribute value.
     * SETTER function.
     * @since 1.0.1
     *
     * @param string $property Property/attribute name.
     * @param mixed  $value    Value.
     */
    public function __set($property, $value)
    {
        if (isset($this->customFields[$property])) {
            $this->customFields[$property] = $value;
        } else {
            return parent::__set($property, $value);
        }
    }
    /**
     * Called when casting model to array to let parent classes override and add special content.
     * @since 1.0.0
     * @since 1.0.1 Adds custom fields and phones.
     *
     * @param array $output Output
     */
    protected function onCasting(array &$output)
    {
        if (!isset($this->attributes['email'])) {
            $output = array();
            return;
        }
        $output['contacts'] = array();
        // Add email
        $output['contacts'][] = array(
            'type'      => 'EMAIL',
            'value'     => $this->attributes['email'],
            'status'    => 'OPT_IN',
        );
        // Add phones
        if (isset($this->attributes['cellphone']))
            $output['contacts'][] = array(
                'type'      => 'CELL_PHONE',
                'value'     => $this->phoneTransform($this->attributes['cellphone']),
            );
        if (isset($this->attributes['workphone']))
            $output['contacts'][] = array(
                'type'      => 'WORK_PHONE',
                'value'     => $this->phoneTransform($this->attributes['workphone']),
            );
        if (isset($this->attributes['homephone']))
            $output['contacts'][] = array(
                'type'      => 'HOME_PHONE',
                'value'     => $this->phoneTransform($this->attributes['homephone']),
            );
        // Add custom fields 
        if (count($this->customFields) > 0) {
            $output['customFieldValues'] = array();
            foreach ($this->customFields as $field) {
                $row = array();
                if ($field['fieldID'])
                    $row['fieldId'] = $field['fieldID'];
                if ($field['name'])
                    $row['name'] = $field['name'];
                $row['value'] =  method_exists($this, $field['property'].'Transform')
                    ? $this->{$field['property'].'Transform'}($value)
                    : $field['value'];
                $output['customFieldValues'][] = $row;
            }
        }
    }
    /**
     * Adds a custom field value.
     * @since 1.0.1
     *
     * @param string $fieldID Field ID.
     * @param string $name    Field name.
     * @param mixed  $value   Field value.
     * @param string $type    Field type.
     */
    public function addCustomField($fieldID = null, $name = null, $value = null, $type = null)
    {
        if ($fieldID === null && $name === null)
            throw new Exception('Custom field can not be added without an ID or a name.');
        if (is_array($value))
            throw new Exception('Array value as custom field is not supported.');
        $key = $name ? lcfirst(preg_replace('/[\s\.\?\@\-\_]+/', '', $name)) : $fieldID;
        $this->customFields[$key] = array(
            'fieldID'   => $fieldID,
            'name'      => $name,
            'value'     => $this->evalCustomField($value, $type),
            'property'  => $name ? lcfirst(preg_replace('/[\s\.\?\@\-\_]+/', '', $name)) : uniqid(),
        );
    }
    /**
     * Returns any address value into a valid one during casting.
     * @since 1.0.0
     *
     * @param mixed $value Value
     *
     * @return array
     */
    public function addressTransform($value)
    {
        // Force array
        return (array)$value;
    }
    /**
     * Returns date of birth with a valid Salsa format.
     * @since 1.0.0
     *
     * @see http://stackoverflow.com/questions/16516136/convert-date-to-t-z-format
     *
     * @param string $date Value.
     *
     * @return string
     */
    public function dateOfBirthTransform($value)
    {
        return str_replace('+00:00', '.000Z', gmdate('c', strtotime($value)));
    }
    /**
     * Returns gender valid value.
     * @since 1.0.1
     *
     * @param mixed $value Value
     *
     * @return mixed string or null
     */
    public function genderTransform($value)
    {
        // Force array
        return preg_match('/f|female|feme/', strtolower($value))
            ? 'FEMALE'
            : (preg_match('/m/', strtolower($value))
                ? 'MALE'
                : null
            );
    }
    /**
     * Returns phone valid value.
     * @since 1.0.1
     *
     * @param mixed $value Value
     *
     * @return mixed string or null
     */
    public function phoneTransform($value)
    {
        $value = preg_replace('/[\.\-\(\)\+\-]/', '', $value);
        if (preg_match('/(\d{3})(\d{3})(\d{4})$/', $value, $matches))
            return $matches[1].'-'.$matches[2].'-'.$matches[3];
        return $value;
    }
    /**
     * Evaluates and returns transformed custom filed value based on type.
     * @since 1.0.1
     *
     * @param mixed  $value Field value.
     * @param string $type  Field type.
     *
     * @return mixed
     */
    private function evalCustomField($value, $type = null)
    {
        if ($type === null)
            $type = is_bool($value) || $value === 'true' || $value === 'false'
                ? 'BOOL'
                : 'STRING';
        switch ($type) {
            case 'DATE':
            case 'DATETIME':
            case 'TIMESTAMP':
            case 'TIME':
                return str_replace('+00:00', '.000Z', gmdate('c', strtotime($value)));
            case 'BOOL':
            case 'BOOLEAN':
                return (is_string($value) && $value == 'true') || $value ? true : false;
        }
        return $value;
    }
}