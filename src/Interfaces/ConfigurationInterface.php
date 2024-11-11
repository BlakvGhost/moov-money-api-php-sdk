<?php

namespace MoovMoney\Interfaces;

interface ConfigurationInterface
{
    public function getUsername(): string;

    public function getPassword(): string;

    public function getBaseUrl(): string;

    /**
     * Method to get the the configured language or default one.
     *
     */
    public function getLang(): string;

    public function getEncryptionKey(): string;

    public function getRequestTimeout(): float;

    public function isValid(): bool;
}
