<?php

declare(strict_types=1);

namespace LupusCoding\Webhooks\SenderTests;

use LupusCoding\Webhooks\Sender\Sender;
use LupusCoding\Webhooks\SenderTests\Mock\JsonSerializeMock;
use PHPUnit\Framework\TestCase;

/**
 * Class SenderTest
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class SenderTest extends TestCase
{
    const WEBHOOK_URL = 'https://httpbin.org/post';

    /**
     * @covers \LupusCoding\Webhooks\Sender\Sender
     * @dataProvider mockProvider
     */
    public function testSendWebhook($mock): void
    {
        $mock['name'] = 'LupusCoding::testSendWebhook';
        $sender = new Sender(self::WEBHOOK_URL);
        $sender->send(new JsonSerializeMock($mock));

        $this->assertFalse($sender->hasError());
        $responseBody = json_decode($sender->getLastResponse(), true);
        $this->assertEquals('httpbin.org', $responseBody['headers']['Host']);
        $this->assertEquals('LupusCoding::testSendWebhook', $responseBody['json']['name']);
    }

    public function mockProvider(): array
    {
        return [
            [[
                'name' => 'LupusCoding::sampleMethod',
                'fields' => [ 'some', 'example', 'stuff', ],
                'values' => [
                    'some' => 'foo',
                    'example' => 'bar',
                    'stuff' => 'baz',
                ],
            ]],
        ];
    }

    /**
     * @covers \LupusCoding\Webhooks\Sender\Sender
     * @dataProvider mockProvider
     */
    public function testFailedSend($mock): void
    {
        $mock['name'] = 'LupusCoding::testSendWebhook';
        $sender = new Sender('http://no.endpoi.nt/should/fail', false);
        $sender->send(new JsonSerializeMock($mock));

        $this->assertTrue($sender->hasError());
    }
}