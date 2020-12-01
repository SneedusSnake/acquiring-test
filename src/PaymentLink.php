<?php
namespace Sneedus\Acquiring;

class PaymentLink{
    private int $id;
    private string $url;

    public function __construct(int $id, string $url)
    {
        $this->id = $id;
        $this->url = $url;
    }

    public function getPaymentId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}