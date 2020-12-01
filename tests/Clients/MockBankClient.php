<?php
namespace Sneedus\Acquiring\Tests\Clients;

use Sneedus\Acquiring\PaymentInfo;
use Sneedus\Acquiring\PaymentLink;
use Sneedus\Acquiring\Clients\BankClient;

class MockBankClient extends BankClient
{

    public int $paymentId = 1;
    public string $paymentUrl = "http://example.org";
    public bool $status = false;

    public function __construct()
    {
    }

    public function fetchPaymentLink(PaymentInfo $info): PaymentLink
    {
        return new PaymentLink($this->paymentId,$this->paymentUrl);
    }

    public function fetchPaymentStatus(int $paymentId): bool
    {
        return $this->status;
    }
}