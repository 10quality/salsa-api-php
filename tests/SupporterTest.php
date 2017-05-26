<?php

use Salsa\Models\Supporter;
/**
 * Tests Supporter model.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.1
 * @package Salsa
 * @license MIT
 */
class SupporterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests casting without email.
     */
    public function testCastingWithoutEmail()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->randomValue = uniqid();
        // Assert
        $this->assertEmpty($supporter->toArray());
    }
    /**
     * Tests that casting only happends on properties.
     */
    public function testPropertiesCasting()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->randomValue = uniqid();
        $supporter->firstName = 'Alejandro';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertNotEmpty($supporter);
        $this->assertTrue(isset($supporter['firstName']));
        $this->assertFalse(isset($supporter['randomValue']));
    }
    /**
     * Tests string/json casting.
     */
    public function testStringCasting()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->firstName = 'Alejandro';
        // Assert
        $this->assertEquals(
            (string)$supporter,
            '{"firstName":"Alejandro","contacts":[{"type":"EMAIL","value":"test@testing.test","status":"OPT_IN"}]}'
        );
    }
    /**
     * Tests expected SALSA output. EMAIL
     */
    public function testSalsaOutputForEmail()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertTrue(isset($supporter['contacts']));
        $this->assertInternalType('array',$supporter['contacts']);
        $this->assertInternalType('array',$supporter['contacts'][0]);
        $this->assertTrue(isset($supporter['contacts'][0]['type']));
        $this->assertTrue(isset($supporter['contacts'][0]['value']));
        $this->assertTrue(isset($supporter['contacts'][0]['status']));
        $this->assertEquals($supporter['contacts'][0]['type'], 'EMAIL');
        $this->assertEquals($supporter['contacts'][0]['value'], 'test@testing.test');
        $this->assertEquals($supporter['contacts'][0]['status'], 'OPT_IN');
    }
    /**
     * Tests expected SALSA output. CELL_PHONE
     */
    public function testSalsaOutputForCellphone()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->cellphone = '123456789';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertTrue(isset($supporter['contacts']));
        $this->assertInternalType('array',$supporter['contacts']);
        $this->assertInternalType('array',$supporter['contacts'][1]);
        $this->assertTrue(isset($supporter['contacts'][1]['type']));
        $this->assertTrue(isset($supporter['contacts'][1]['value']));
        $this->assertFalse(isset($supporter['contacts'][1]['status']));
        $this->assertEquals($supporter['contacts'][1]['type'], 'CELL_PHONE');
        $this->assertEquals($supporter['contacts'][1]['value'], '123456789');
    }
    /**
     * Tests expected SALSA output. WORK_PHONE
     */
    public function testSalsaOutputForWorkphone()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->workphone = '123456789';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['contacts'][1]['type'], 'WORK_PHONE');
        $this->assertEquals($supporter['contacts'][1]['value'], '123456789');
    }
    /**
     * Tests expected SALSA output. HOME_PHONE
     */
    public function testSalsaOutputForHomephone()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->homephone = '123456789';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['contacts'][1]['type'], 'HOME_PHONE');
        $this->assertEquals($supporter['contacts'][1]['value'], '123456789');
    }
    /**
     * Tests addition of custom field.
     */
    public function testCustomField()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->addCustomField('abc', 'age', 20);
        // Assert
        $this->assertNotNull($supporter->age);
        $this->assertEquals($supporter->age, 20);
    }
    /**
     * Tests expected SALSA output. CUSTOM FIELDS
     */
    public function testSalsaOutputForCustomField()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->addCustomField('abc', 'age', 20);
        $supporter = $supporter->toArray();
        // Assert
        $this->assertTrue(isset($supporter['customFieldValues']));
        $this->assertInternalType('array',$supporter['customFieldValues']);
        $this->assertTrue(isset($supporter['customFieldValues'][0]['fieldId']));
        $this->assertTrue(isset($supporter['customFieldValues'][0]['name']));
        $this->assertTrue(isset($supporter['customFieldValues'][0]['value']));
        $this->assertEquals($supporter['customFieldValues'][0]['fieldId'], 'abc');
        $this->assertEquals($supporter['customFieldValues'][0]['name'], 'age');
        $this->assertEquals($supporter['customFieldValues'][0]['value'], 20);
    }
    /**
     * Test phone value transformation.
     */
    public function testPhoneTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->cellphone = '1234567890';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['contacts'][1]['type'], 'CELL_PHONE');
        $this->assertEquals($supporter['contacts'][1]['value'], '123-456-7890');
    }
    /**
     * Test date transform.
     */
    public function testBirthdayTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->dateOfBirth = '1985-08-06';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['dateOfBirth'], '1985-08-06T07:00:00.000Z');
    }
    /**
     * Test gender transform.
     */
    public function testGenderMaleTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->gender = 'm';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['gender'], 'MALE');
    }
    /**
     * Test gender transform.
     */
    public function testGenderFemaleTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->gender = 'f';
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals($supporter['gender'], 'FEMALE');
    }
    /**
     * Test custom field with bool value.
     */
    public function testCustomFieldBoolDefault()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->addCustomField(null, 'target', false);
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals(false, $supporter['customFieldValues'][0]['value']);
    }
    /**
     * Test custom field with bool value.
     */
    public function testCustomFieldBoolTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->addCustomField(null, 'target', 0, 'BOOLEAN');
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals(false, $supporter['customFieldValues'][0]['value']);
    }
    /**
     * Test custom field with bool value.
     */
    public function testCustomFieldDateTransform()
    {
        // Prepare
        $supporter = new Supporter();
        $supporter->email = 'test@testing.test';
        $supporter->addCustomField(null, 'target', '1985-08-06', 'DATE');
        $supporter = $supporter->toArray();
        // Assert
        $this->assertEquals('1985-08-06T07:00:00.000Z', $supporter['customFieldValues'][0]['value']);
    }
}