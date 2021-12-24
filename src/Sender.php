<?php

declare(strict_types=1);

namespace LupusCoding\Webhooks\Sender;

use Exception;
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
    private string $lastError;

    public function __construct(string $webhook = '', bool $sslVerify = true)
    {
        $this->webhook = $webhook;
        $this->sslVerify = $sslVerify;
        $this->resetResultProperties();
    }

    private function resetResultProperties(): void
    {
        $this->lastResponse = '';
        $this->lastError = '';
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
        $this->resetResultProperties();

        try {
            $curlHandle = $this->initCurlHandle();
            $this->setCurlOptions($curlHandle);
            $this->setCurlMessage($curlHandle, $messageObject);
            $response = curl_exec($curlHandle);
            $this->setLastResponse(($response ?: ''));
            $this->setLastError(curl_error($curlHandle));
            curl_close($curlHandle);

            return !$this->hasError();

        } catch (Exception $exception) {
            $this->setLastError($exception->getMessage());
            return false;
        }
    }


    /** Initialize the curl handle
     *
     * @throws Exception
     */
    private function initCurlHandle()
    {
        $curlHandle = curl_init();
        if (!is_resource($curlHandle)) {
            throw new Exception(sprintf(
                'Curl handle could not be created. {url: "%s", sslVerify: %s}',
                $this->getWebhook(),
                ($this->isSslVerify() ? 'true' : 'false')
            ));
        }
        return $curlHandle;
    }

    /** Set cUrl options */
    private function setCurlOptions(&$curlHandle): void
    {
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, $this->isSslVerify());
        curl_setopt($curlHandle, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curlHandle, CURLOPT_URL, $this->getWebhook());
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_POST, 1);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    }

    /** Set the cUrl message */
    private function setCurlMessage(&$curlHandle, JsonSerializable $messageObject): void
    {
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($messageObject));
    }

    /** Get last response body */
    public function getLastResponse(): string
    {
        return $this->lastResponse;
    }

    /** Set last response */
    private function setLastResponse(string $response): void
    {
        $this->lastResponse = $response;
    }

    /** Get last error */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /** Test if an error occurred */
    public function hasError(): bool
    {
        return ($this->getLastError() !== '');
    }

    /** Set last error */
    private function setLastError(string $lastError): void
    {
        $this->lastError = $lastError;
    }

}