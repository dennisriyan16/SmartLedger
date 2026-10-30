<?php

namespace Tests\Feature;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_get_latest_rates_with_http_fake(): void
    {
        Http::fake([
            'https://api.frankfurter.dev/v1/latest*' => Http::response([
                'amount' => 1.0,
                'base' => 'USD',
                'date' => '2026-10-28',
                'rates' => [
                    'EUR' => 0.90,
                    'GBP' => 0.75,
                    'INR' => 95.00,
                ],
            ], 200),
        ]);

        $service = new ExchangeRateService;
        $result = $service->getLatestRates('USD');

        $this->assertTrue($result['success']);
        $this->assertEquals('USD', $result['base']);
        $this->assertEquals('2026-10-28', $result['date']);
        $this->assertEquals(0.90, $result['rates']['EUR']);
        $this->assertEquals(95.00, $result['rates']['INR']);
    }

    public function test_convert_currency_calculation(): void
    {
        Http::fake([
            'https://api.frankfurter.dev/v1/latest*' => Http::response([
                'amount' => 1.0,
                'base' => 'USD',
                'date' => '2026-10-28',
                'rates' => [
                    'INR' => 95.22,
                ],
            ], 200),
        ]);

        $service = new ExchangeRateService;
        $conversion = $service->convert(100.00, 'USD', 'INR');

        $this->assertTrue($conversion['success']);
        $this->assertEquals(100.00, $conversion['amount']);
        $this->assertEquals(9522.00, $conversion['converted']);
    }

    public function test_fallback_when_api_fails(): void
    {
        Http::fake([
            'https://api.frankfurter.dev/v1/latest*' => Http::response('Server Error', 500),
        ]);

        $service = new ExchangeRateService;
        $result = $service->getLatestRates('USD');

        $this->assertFalse($result['success']);
        $this->assertNotNull($result['error']);
        $this->assertArrayHasKey('EUR', $result['rates']);
    }
}
