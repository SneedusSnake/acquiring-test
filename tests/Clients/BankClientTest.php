<?php
namespace Sneedus\Acquiring\Tests\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use \Sneedus\Acquiring\PaymentInfo;
use \Sneedus\Acquiring\PaymentLink;
use GuzzleHttp\Handler\MockHandler;
use Psr\Http\Message\RequestInterface;
use \Sneedus\Acquiring\Clients\BetaBankClient;
use Sneedus\Acquiring\Exceptions\Client\BetaBankClientException;

abstract class BankClientTest extends TestCase
{
    protected PaymentInfo $info;

    public function setUp(): void
    {
        $this->info = new PaymentInfo([
            "fullName" => "John Doe",
            "sum"   => 140,
            "invoiceId" => 223,
            "purpose"   => "test"
        ]);
    }

    abstract public function testFetchPaymentLink();

    abstract public function testRequestBody();

    abstract public function testFailedLinkRequestThrowsException();

    abstract public function testFetchPaymentStatus();

    abstract public function testFailedStatusRequestThrowsException();
}