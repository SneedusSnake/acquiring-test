<?php
namespace Sneedus\Acquiring\Tests;

use \Sneedus\Acquiring\Acquiring;
use \Sneedus\Acquiring\PaymentInfo;
use \Sneedus\Acquiring\Models\Payment;
use Sneedus\Acquiring\Tests\Clients\MockBankClient;

class AcquiringTest extends \Orchestra\Testbench\TestCase
{
    private PaymentInfo $info;

    private MockBankClient $client;

    private Acquiring $acquiring;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->info = new PaymentInfo([
            "fullName" => "John Doe",
            "sum"   => 140,
            "invoiceId" => 223,
            "purpose"   => "test"
        ]);
        $this->client = new MockBankClient;
        $this->acquiring = new Acquiring($this->client);
    }

    public function testInternalPaymentId()
    {
        $this->client->paymentId = 112;

        $internalId = $this->acquiring->getPaymentLink($this->info)->getPaymentId();

        $this->assertEquals(112, Payment::find($internalId)->payment_id);
    }

    public function testUrl()
    {
        $this->client->paymentUrl = "test";
    
        $url = $this->acquiring->getPaymentLink($this->info)->getUrl();

        $this->assertEquals("test", $url);
    }

    public function testStatus()
    {
        $payment = Payment::create(["payment_id" => 20, "status" => false]);

        $this->assertFalse($this->acquiring->getPaymentStatus($payment->id));
        $this->client->status = true;
        $this->assertTrue($this->acquiring->getPaymentStatus($payment->id));
    }

    public function testStatusCached()
    {
        $this->client->status = false;
        $payment = Payment::create(["payment_id" => 20, "status" => true]);

        $this->assertTrue($this->acquiring->getPaymentStatus($payment->id));
    }

    protected function getPackageProviders($app)
    {
        return ['Sneedus\Acquiring\Providers\AcquiringServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}