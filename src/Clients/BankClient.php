<?php
namespace Sneedus\Acquiring\Clients;

use GuzzleHttp\ClientInterface;
use Sneedus\Acquiring\PaymentInfo;
use Sneedus\Acquiring\PaymentLink;

abstract class BankClient
{
    protected ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    abstract public function fetchPaymentLink(PaymentInfo $info): PaymentLink;

    abstract public function fetchPaymentStatus(int $paymentId): bool;

}