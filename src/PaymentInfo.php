<?php
namespace Sneedus\Acquiring;

class PaymentInfo
{
    public string $fullName;
    public float $sum;
    public int $invoiceId;
    public string $purpose;

    public function __construct($values = [])
    {
        $this->fullName = $values["fullName"] ?? "";
        $this->sum = $values["sum"] ?? "";
        $this->invoiceId = $values["invoiceId"] ?? "";
        $this->purpose = $values["purpose"] ?? "";
    }

    public function toArray(): array
    {
        return [
            "fullName"  => $this->fullName,
            "sum"       => $this->sum,
            "invoiceId" =>  $this->invoiceId,
            "purpose"   => $this->purpose,
        ];
    }
}