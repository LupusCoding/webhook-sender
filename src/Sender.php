<?php

declare(strict_types=1);

namespace LupusCoding\Webhooks\Sender;

use JsonSerializable;

/**
 * Class Sender
 * @package LupusCoding\Webhooks\Sender
 * @author Ralph Dittrich <dittrich.ralph@lupuscoding.de>
 */
class Sender
{
    private string $webhook;
    private bool $sslVerify;
    private string $lastResponse;

    public function __construct(string $webhook = '', bool $sslVerify = true)
    {
        $this->webhook = $webhook;
        $this->sslVerify = $sslVerify;
    }

    /** Get webhook url */
    protected function getWebhook(): string
    {
        return $this->webhook;
    }

    /** Set webhook url */
    public function setWebhook(string $webhook): Sender
    {
        $this->webhook = $webhook;
        return $this;
    }

    /** Get sslVerify option */
    public function isSslVerify(): bool
    {
        return $this->sslVerify;
    }

    /** Set sslVerify option */
    public function setSslVerify(bool $sslVerify): Sender
    {
        $this->sslVerify = $sslVerify;
        return $this;
    }

    /** Send serializable object to webhook */
    public function send(JsonSerializable $messageObject): bool
    {
        $success = false;

        $ch = $this->initCurlHandle();
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageObject));

        $this->setLastResponse(curl_exec($ch));
        if (0 === curl_errno($ch)) {
            $success = true;
        }
        curl_close($ch);

        return $success;
    }

    /** Initialize the curl handle
     *
     * @return false|resource
     */
    private function initCurlHandle()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->isSslVerify());
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_URL, $this->getWebhook());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);

        return $ch;
    }

    /** Get last response body */
    public function getLastResponse(): string
    {
        return ($this->lastResponse ?? '');
    }

    /** Set last response
     *
     * @param string|bool $response
     */
    private function setLastResponse($response): void
    {
        $this->lastResponse = ($response ?? '');
    }
}