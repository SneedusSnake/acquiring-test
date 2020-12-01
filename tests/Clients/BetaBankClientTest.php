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

class BetaBankClientTest extends BankClientTest
{
    public function testFetchPaymentLink()
    {
        $mock = new MockHandler([
            $this->getMockResponse(200, [], ['payment_id' => 8, 'payment_url'=>"http://betabank.org/payment/8"]),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new BetaBankClient(new Client(['handler' => $handlerStack]));

        $link = $client->fetchPaymentLink($this->info);

        $this->assertEquals("http://betabank.org/payment/8", $link->getUrl());
        $this->assertEquals(8, $link->getPaymentId());
    }

    public function testRequestBody()
    {
        $requestBody="";
        $client = new Client(["handler" => function(RequestInterface $request) use(&$requestBody){
            $requestBody=$request->getBody()->getContents();
            return $this->getMockResponse(200, [], ['payment_id'=>1, "payment_url"=>"http://betabank.org"]);
        }]);

        (new BetaBankClient($client))->fetchPaymentLink($this->info);

        $this->assertEquals($requestBody, json_encode([
            "credentials"  => $this->info->fullName,
            "amount"   => $this->info->sum,
            "invoice" => $this->info->invoiceId,
            "purpose"   => $this->info->purpose,
        ]));
    }

    public function testFailedLinkRequestThrowsException()
    {
        $mock = new MockHandler([
            $this->getMockResponse(404)
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new BetaBankClient(new Client(['handler' => $handlerStack]));
        $this->expectException(BetaBankClientException::class);

        $link = $client->fetchPaymentLink($this->info);
    }

    public function testFetchPaymentStatus()
    {
        $mock = new MockHandler([
            $this->getMockResponse(200, [], ["status"=>true]),
            $this->getMockResponse(200, [], ["status"=>false]),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new BetaBankClient(new Client(['handler' => $handlerStack]));

        $this->assertTrue($client->fetchPaymentStatus(12));
        $this->assertFalse($client->fetchPaymentStatus(13));
    }

    public function testFailedStatusRequestThrowsException()
    {
        $mock = new MockHandler([
            $this->getMockResponse(404),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new BetaBankClient(new Client(['handler' => $handlerStack]));
        $this->expectException(BetaBankClientException::class);

        $link = $client->fetchPaymentStatus(11);
    }

    private function getMockResponse($status, array $headers = [], array $body = []): Response
    {
        return new Response($status, array_merge(["Content-Type"=>"application/json"], $headers), json_encode($body));
    }
} 