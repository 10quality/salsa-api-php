<?php

namespace Salsa\Base;

use Salsa\Models\Supporter;

/**
 * Response class.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.1
 * @package Salsa
 * @license MIT
 */
class Response
{
    /**
     * Response data.
     * @since 1.0.0
     * @var array 
     */
    protected $data = array();
    /**
     * Default constructor. Processes data.
     * @since 1.0.0
     *
     * @param mixed $data Response data.
     */
    public function __construct($data)
    {
        if (!is_string($data))
            throw new Exception('Can\'t process response from Salsa API.');
        $this->data = (array)json_decode($data);
    }
    /**
     * Returns property.
     * @since 1.0.0
     * @since 1.0.1 Support for phones and custom fields.
     *
     * @param string $property Property name.
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'supporters':
                if (isset($this->data['payload']->supporters)) {
                    $output = array();
                    foreach ($this->data['payload']->supporters as $attributes) {
                        $attributes = (array)$attributes;
                        if (isset($attributes['result'])
                            && in_array($attributes['result'], ['NOT_FOUND'])
                        )
                            continue;
                        $supporter = new Supporter;
                        $supporter->attributes = $attributes;
                        foreach ($attributes['contacts'] as $contact) {
                            switch ($contact->type) {
                                case 'EMAIL':
                                    $supporter->email = $contact->value;
                                    break;
                                case 'CELL_PHONE':
                                    $supporter->cellphone = $contact->value;
                                    break;
                                case 'WORK_PHONE':
                                    $supporter->workphone = $contact->value;
                                    break;
                                case 'HOME_PHONE':
                                    $supporter->homephone = $contact->value;
                                    break;
                            }
                        }
                        if (isset($attributes['customFieldValues']))
                            foreach ($attributes['customFieldValues'] as $field) {
                                $supporter->addCustomField(
                                    $field->fieldId,
                                    $field->name,
                                    $field->value,
                                    $field->type
                                );
                            }
                        $output[] = $supporter;
                    }
                    return $output;
                }
                break;
        }
        if (isset($this->data[$property]))
            return $this->data[$property];
        if (property_exists($this, $property))
            return $this->$property;
        return null;
    }
}