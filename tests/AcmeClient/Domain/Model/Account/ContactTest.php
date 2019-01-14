<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Account;

use AcmeClient\Domain\Model\Account\Contact;

class ContactTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var array
     */
    private const VALID_VALUE = [
        'mailto:cert-admin@example.com',
        'mailto:admin@example.com',
    ];

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Contact::class, new Contact(self::VALID_VALUE));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testInstantiateWithoutArgument(): void
    {
        $this->assertInstanceOf(Contact::class, new Contact());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithoutScheme(): void
    {
        new Contact(['cert-admin@example.com']);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateWithoutEmail(): void
    {
        new Contact(['mailto:@example.com']);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-account
     * @return void
     */
    public function testGetValue(): void
    {
        $contact = new Contact(self::VALID_VALUE);
        $this->assertSame(self::VALID_VALUE, $contact->getValue());
    }
}
