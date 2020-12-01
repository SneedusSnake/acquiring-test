<?php
namespace Sneedus\Acquiring\Clients;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use \Sneedus\Acquiring\PaymentInfo;
use \Sneedus\Acquiring\PaymentLink;
use Sneedus\Acquiring\Exceptions\Client\SphereBankClientException;

class SphereBankClient extends BankClient
{
    public function fetchPaymentLink(PaymentInfo $info): PaymentLink
    {
        try
        {
            $response = $this->client->send($this->getRequest($info));
            $body=simplexml_load_string($response->getBody()->getContents());
            return new PaymentLink($body->payment_id->__toString(),$body->url->__toString());
        } catch(\Exception $e)
        {
            throw new SphereBankClientException($e->getMessage());
        }
    }

    private function getRequest(PaymentInfo $info): Request
    {
        return new Request('POST', 'http://spherebank.org/acquire_payment', ["Content-Type"=>"application/xml; utf-8"], $this->convertToXML($info)->__toString());
    }

    public function convertToXML(PaymentInfo $info): \SimpleXMLElement
    {
        $xmlstr = <<<XML
        <?xml version='1.0'?>
        <document>
            <name>$info->fullName</name>
            <sum>$info->sum</sum>
            <invoice>$info->invoiceId</invoice>
            <purpose>$info->purpose</purpose>
        </document>
        XML;
        return simplexml_load_string($xmlstr);
    }

    public function fetchPaymentStatus(int $paymentId): bool
    {
        try
        {
            $response = $this->client->send(new Request("GET", "http://spherebank.org/payment/{$paymentId}"));
            $body=simplexml_load_string($response->getBody()->getContents());
            return (bool)$body->status->__toString();
        } catch(\Exception $e)
        {
            throw new SphereBankClientException($e->getMessage());
        }
    }
}