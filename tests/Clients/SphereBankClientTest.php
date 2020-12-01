<?php
namespace Sneedus\Acquiring\Tests\Clients;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use \Sneedus\Acquiring\PaymentInfo;
use GuzzleHttp\Handler\MockHandler;
use Psr\Http\Message\RequestInterface;
use \Sneedus\Acquiring\Clients\SphereBankClient;
use Sneedus\Acquiring\Exceptions\Client\SphereBankClientException;

class SphereBankClientTest extends BankClientTest
{
    public function testFetchPaymentLink()
    {
        $mock = new MockHandler([
            $this->getMockResponse(200, [], ["payment_id"=>8, "url"=>"http://spherebank.org/8"]),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new SphereBankClient(new Client(['handler' => $handlerStack]));

        $link = $client->fetchPaymentLink($this->info);

        $this->assertEquals("http://spherebank.org/8", $link->getUrl());
        $this->assertEquals(8, $link->getPaymentId());
    }

    public function testRequestBody()
    {
        $requestBody="";
        $client = new Client(["handler" => function(RequestInterface $request) use(&$requestBody){
            $requestBody=$request->getBody()->getContents();
            return $this->getMockResponse(200, [], ["payment_id"=>8, "url"=>"http://spherebank.org/8"]);
        }]);

        $bankClient = new SphereBankClient($client);
        $bankClient->fetchPaymentLink($this->info);

        $this->assertEquals($requestBody, $bankClient->convertToXML($this->info)->__toString());
    }

    public function testFailedLinkRequestThrowsException()
    {
        $mock = new MockHandler([
            $this->getMockResponse(404),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new SphereBankClient(new Client(['handler' => $handlerStack]));
        $this->expectException(SphereBankClientException::class);

        $link = $client->fetchPaymentLink($this->info);
    }

    public function testFetchPaymentStatus()
    {
        $mock = new MockHandler([
            $this->getMockResponse(200, [], ["status"=>1]),
            $this->getMockResponse(200, [], ["status"=>0]),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new SphereBankClient(new Client(['handler' => $handlerStack]));

        $this->assertTrue($client->fetchPaymentStatus(12));
        $this->assertFalse($client->fetchPaymentStatus(13));
    }

    public function testFailedStatusRequestThrowsException()
    {
        $mock = new MockHandler([
            $this->getMockResponse(404),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new SphereBankClient(new Client(['handler' => $handlerStack]));
        $this->expectException(SphereBankClientException::class);

        $link = $client->fetchPaymentStatus(11);
    }

    private function getMockResponse($status, array $headers = [], array $body = []): Response
    {
        $xmlstr = "<?xml version='1.0'?><document>";
        foreach ($body as $name=>$value)
        {
            $xmlstr .= "<$name>$value</$name>";
        }
        $xmlstr .= "</document>";
        return new Response($status, array_merge(["Content-Type"=>"application/xnk"], $headers), $xmlstr);
    }
}