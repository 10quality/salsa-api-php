<?php

namespace Salsa\Models;

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
     * Called when casting model to array to let parent classes override and add special content.
     * @since 1.0.0
     *
     * @param array $output Output
     */
    protected function onCasting(array &$output)
    {
        if (!isset($this->attributes['email'])) {
            $output = array();
            return;
        }
        $output['contacts'] = [[
            'type'      => 'EMAIL',
            'value'     => $this->attributes['email'],
            'status'    => 'OPT_IN',
        ]];
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
}