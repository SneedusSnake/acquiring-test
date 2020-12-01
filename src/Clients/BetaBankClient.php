<?php
namespace Sneedus\Acquiring\Clients;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\ClientInterface;
use \Sneedus\Acquiring\PaymentInfo;
use \Sneedus\Acquiring\PaymentLink;
use Sneedus\Acquiring\Exceptions\Client\BetaBankClientException;

class BetaBankClient extends BankClient
{
    
    public function fetchPaymentLink(PaymentInfo $info): PaymentLink
    {
        try
        {
            $response = $this->client->send($this->getRequest($info));
            $body=json_decode($response->getBody()->getContents());
            return new PaymentLink($body->payment_id,$body->payment_url);
        } catch(\Exception $e)
        {
            throw new BetaBankClientException($e->getMessage());
        }
    }

    private function getRequest(PaymentInfo $info): Request
    {
        return new Request('POST', 'http://betabank.org/acquire_payment', ["Content-Type"=>"application/json; utf-8"], json_encode([
            "credentials"  => $info->fullName,
            "amount"   => $info->sum,
            "invoice" => $info->invoiceId,
            "purpose"   => $info->purpose,
        ]));
    }

    public function fetchPaymentStatus(int $paymentId): bool
    {
        try
        {
            $response = $this->client->send(new Request("GET", "http://betabank.org/payment/{$paymentId}"));
            $body=json_decode($response->getBody()->getContents());
            return $body->status;
        } catch(\Exception $e)
        {
            throw new BetaBankClientException($e->getMessage());
        }
    }
}