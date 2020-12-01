<?php
namespace Sneedus\Acquiring;

use \Sneedus\Acquiring\PaymentInfo;
use \Sneedus\Acquiring\PaymentLink;
use \Sneedus\Acquiring\Models\Payment;
use Sneedus\Acquiring\Clients\BankClient;

class Acquiring
{
    private BankClient $client;

    public function __construct(BankClient $client)
    {
        $this->client = $client;
    }

    public function getPaymentLink(PaymentInfo $info): PaymentLink
    {
        $paymentLink = $this->client->fetchPaymentLink($info);
        $payment = $this->saveInternalPayment($paymentLink);
        
        return new PaymentLink($payment->id, $paymentLink->getUrl());
    }

    private function saveInternalPayment(PaymentLink $link): Payment
    {
        $payment = new Payment();
        $payment->payment_id = $link->getPaymentId();
        $payment->status = false;
        $payment->save();
        return $payment;
    }

    public function getPaymentStatus(int $id): bool
    {
        $payment = Payment::find($id);
        return $payment->status ?: $this->client->fetchPaymentStatus($payment->payment_id);
    }
}