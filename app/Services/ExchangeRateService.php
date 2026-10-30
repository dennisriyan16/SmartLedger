<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExchangeRateService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.frankfurter.url', 'https://api.frankfurter.dev');
    }

    /**
     * Get live or cached exchange rates for a given base currency.
     * Caches successful responses for 1 hour.
     *
     * @return array{success: bool, base: string, date: string, rates: array<string, float>, error?: string, from_cache?: bool}
     */
    public function getLatestRates(string $base = 'USD'): array
    {
        $base = strtoupper(trim($base));
        $cacheKey = "frankfurter_rates_{$base}";

        return Cache::remember($cacheKey, 3600, function () use ($base) {
            try {
                $response = Http::timeout(5)
                    ->retry(2, 200)
                    ->get("{$this->baseUrl}/v1/latest", [
                        'base' => $base,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    return [
                        'success' => true,
                        'base' => $data['base'] ?? $base,
                        'date' => $data['date'] ?? date('Y-m-d'),
                        'rates' => $data['rates'] ?? [],
                        'error' => null,
                    ];
                }

                Log::warning("Frankfurter API returned HTTP {$response->status()} for base {$base}");

                return $this->getFallbackRates($base, "Service returned status code {$response->status()}");
            } catch (Exception $e) {
                Log::error('Frankfurter API connection error: '.$e->getMessage());

                return $this->getFallbackRates($base, 'Unable to connect to live exchange rate service.');
            }
        });
    }

    /**
     * Convert an amount from one currency to another using the latest rates.
     */
    public function convert(float $amount, string $from, string $to): array
    {
        $from = strtoupper(trim($from));
        $to = strtoupper(trim($to));

        if ($from === $to) {
            return [
                'success' => true,
                'amount' => $amount,
                'from' => $from,
                'to' => $to,
                'rate' => 1.0,
                'converted' => $amount,
            ];
        }

        $ratesData = $this->getLatestRates($from);

        if (! $ratesData['success'] || ! isset($ratesData['rates'][$to])) {
            return [
                'success' => false,
                'error' => "Exchange rate from {$from} to {$to} is currently unavailable.",
                'converted' => 0.0,
            ];
        }

        $rate = (float) $ratesData['rates'][$to];
        $converted = round($amount * $rate, 2);

        return [
            'success' => true,
            'amount' => $amount,
            'from' => $from,
            'to' => $to,
            'rate' => $rate,
            'converted' => $converted,
            'date' => $ratesData['date'],
        ];
    }

    /**
     * Fallback rates dictionary in case external network is unreachable.
     */
    protected function getFallbackRates(string $base, string $errorMessage): array
    {
        $mockUsdRates = [
            'EUR' => 0.92,
            'GBP' => 0.79,
            'INR' => 86.50,
            'CAD' => 1.38,
            'AUD' => 1.52,
            'JPY' => 155.20,
            'CHF' => 0.88,
            'SGD' => 1.34,
            'CNY' => 7.24,
        ];

        return [
            'success' => false,
            'base' => $base,
            'date' => date('Y-m-d'),
            'rates' => $base === 'USD' ? $mockUsdRates : [],
            'error' => $errorMessage,
        ];
    }
}
