<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MoovMoney\Exceptions\ServerErrorException;
use MoovMoney\MoovMoneyAPI;
use MoovMoney\MoovMoneyAPIConfig;
use MoovMoney\Response\MoovMoneyApiResponse;

it("should send push transaction request", function() {

    $config = new MoovMoneyAPIConfig();
    $config->setUsername('username');
    $config->setPassword('password');
    $config->setBaseUrl('http://localhost:8080');

    $mock = new MockHandler([
        new Response(status: 200, body: getPushTransactionResponse()),
        new Response(status: 400, body: getResponseError())
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    
    $sdk = new MoovMoneyAPI($config, $client);

    $response = $sdk->pushTransaction("90457142", 2000, "message");
    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(111);
    expect($response->getDescription())->toBe('description');
    expect($response->getReferenceId())->toBe('12345678');
    expect($response->getTransactionData())->toBe('tag');

    $sdk->pushTransaction("90457142", 2000, "message");

})->throws(
    ServerErrorException::class,
    '[soap:Server] : "For input string"'
);

it("should send push with pending transaction request", function() {

    $config = new MoovMoneyAPIConfig();
    $config->setUsername('username');
    $config->setPassword('password');
    $config->setBaseUrl('http://localhost:8080');

    $mock = new MockHandler([
        new Response(status: 200, body: getPushWithPendingSuccessResponse()),
        new Response(status: 200, body: getPushWithPendingResponse())
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);
    
    $sdk = new MoovMoneyAPI($config, $client);

    $response = $sdk->pushWithPendingTransaction("90457142", 2000, "message");
    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(0);
    expect($response->getDescription())->toBe('description');
    expect($response->getReferenceId())->toBe('12345678');
    expect($response->getTransactionData())->toBe('tag');
    expect($response->isSuccess())->toBeTrue();


    $response = $sdk->pushWithPendingTransaction("90457142", 2000, "message");
    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(100);
    expect($response->getDescription())->toBe('pending');
    expect($response->getLongDescription())->toBe('In pending state');
    expect($response->getReferenceId())->toBeNull(); // no reference id if request is pending
    expect($response->getTransactionData())->toBeNull(); // no transaction data if request is pending
    expect($response->isSuccess())->toBeFalse();
    expect($response->isInPendingState())->toBeTrue();

});


it("should check a transaction status", function() {

    $config = new MoovMoneyAPIConfig();
    $config->setUsername('username');
    $config->setPassword('password');
    $config->setBaseUrl('http://localhost:8080');

    $mock = new MockHandler([
        new Response(status: 200, body: getTransactionStatusResponse())
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $sdk = new MoovMoneyAPI($config, $client);

    $response = $sdk->getTransactionStatus("12345678");
    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(0);
    expect($response->getDescription())->toBe('SUCCESS');
    expect($response->getReferenceId())->toBe('12345678');
});

it("should get the subscriber balance", function() {

    $config = new MoovMoneyAPIConfig();
    $config->setUsername('username');
    $config->setPassword('password');
    $config->setBaseUrl('http://localhost:8080');

    $mock = new MockHandler([
        new Response(status: 200, body: getGetBalanceResponse())
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $sdk = new MoovMoneyAPI($config, $client);

    $response = $sdk->getBalance("98239988");

    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(0);
    expect($response->GetBalance->getMessage())->toBe('message');
    expect($response->GetBalance->getBalance())->toBe(382222);
    
});

it("should send a transfert flooz", function() {

    $config = new MoovMoneyAPIConfig();
    $config->setUsername('username');
    $config->setPassword('password');
    $config->setBaseUrl('http://localhost:8080');

    $mock = new MockHandler([
        new Response(status: 200, body: getTransferFloozResponse())
    ]);
    
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $sdk = new MoovMoneyAPI($config, $client);

    $response = $sdk->transfertFlooz("98239988", 1200, "reference");

    expect($response)->toBeInstanceOf(MoovMoneyApiResponse::class);
    expect($response->getStatusCode())->toBe(0);
    expect($response->TransferFlooz->getMessage())->toBe('message');
    expect($response->TransferFlooz->getTransactionID())->toBe("1234567890");
    expect($response->TransferFlooz->getSenderBalanceAfter())->toBe(16809);
    expect($response->TransferFlooz->getSenderBalanceBefore())->toBe(16819);
    expect($response->TransferFlooz->getSenderBonus())->toBe(0);
    expect($response->TransferFlooz->getSenderKeyCost())->toBe(0);
    
});