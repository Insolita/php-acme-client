<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Certificate;

use AcmeClient\Domain\Model\Certificate\ExpiryDate;

class ExpiryDateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(
            ExpiryDate::class,
            new ExpiryDate(new \DateTime('yesterday'), new \DateTime())
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateInvalidExpiry(): void
    {
        $this->assertInstanceOf(
            ExpiryDate::class,
            new ExpiryDate(new \DateTime(), new \DateTime('yesterday'))
        );
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetFrom(): void
    {
        $expiryDate = new ExpiryDate(
            $expected = new \DateTime('yesterday'),
            new \DateTime()
        );

        $this->assertEquals($expected, $expiryDate->getFrom());
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-certificate
     * @return void
     */
    public function testGetTo(): void
    {
        $expiryDate = new ExpiryDate(
            new \DateTime('yesterday'),
            $expected = new \DateTime()
        );

        $this->assertEquals($expected, $expiryDate->getTo());
    }
}
