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
    /** @covers \LupusCoding\Webhooks\Sender\Sender */
    public function testSendWebhook(): void
    {
        $webhookUrl = 'https://httpbin.org/post';
        $mock = new JsonSerializeMock([
            'name' => 'LupusCoding::testSendWebhook',
            'fields' => [
                'some', 'example', 'stuff',
            ],
            'values' => [
                'some' => 'foo',
                'example' => 'bar',
                'stuff' => 'baz',
            ],
        ]);

        $sender = new Sender($webhookUrl, false);
        $sender->send($mock);

        $this->assertIsString(($response = $sender->getLastResponse()));
        $responseBody = json_decode($response, true);
        $this->assertEquals('httpbin.org', $responseBody['headers']['Host']);
        $this->assertEquals('LupusCoding::testSendWebhook', $responseBody['json']['name']);
    }
}