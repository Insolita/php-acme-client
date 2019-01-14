<?php
declare(strict_types=1);

namespace Tests\AcmeClient\Domain\Model\Config;

use AcmeClient\Domain\Model\Config\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @group domain
     * @group domain-model
     * @group domain-model-config
     * @return void
     */
    public function testInstantiate(): void
    {
        $this->assertInstanceOf(Config::class, new Config($this->getConfigValues()));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-config
     * @expectedException \InvalidArgumentException
     * @return void
     */
    public function testInstantiateLackRequiredParam(): void
    {
        new Config([]);
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-config
     * @return void
     */
    public function testGetValue(): void
    {
        $config = new Config($this->getConfigValues());

        $this->assertSame('version', $config->getValue('version'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-config
     * @return void
     */
    public function testGetValueUndifinedIndex(): void
    {
        $config = new Config($this->getConfigValues());

        $this->assertSame('', $config->getValue('undefined'));
    }

    /**
     * @group domain
     * @group domain-model
     * @group domain-model-config
     * @return void
     */
    public function testFormatIni(): void
    {
        $config = new Config($this->getConfigValues());

        $expected = <<<EOT
version = version
endpoint = endpoint
account = account
commonName = *.example.com
fullchain = fullchain
cert = cert
chain = chain
privkey = privkey

EOT;

        $this->assertSame($expected, $config->formatIni());
    }

    /**
     * @return array
     */
    private function getConfigValues(): array
    {
        return [
            'version'    => 'version',
            'endpoint'   => 'endpoint',
            'account'    => 'account',
            'commonName' => '*.example.com',
            'fullchain'  => 'fullchain',
            'cert'       => 'cert',
            'chain'      => 'chain',
            'privkey'    => 'privkey',
        ];
    }
}
