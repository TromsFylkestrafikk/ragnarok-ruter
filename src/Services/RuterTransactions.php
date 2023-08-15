<?php

namespace TromsFylkestrafikk\RagnarokRuter\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use TromsFylkestrafikk\RagnarokRuter\Facades\RuterAuth;

/**
 * Retrieval of transactions done in the KK 1.0 cooperation
 */
 class RuterTransactions
 {

     public function __construct(protected $config)
     {
         //
     }

     /**
      * Get all transactions for a single day.
      *
      * @param Carbon $date
      *
      * @return string
      */
     public function getTransactionsAsJson(Carbon $date): string
     {
         return $this->getTransactionsAsResponse($date)->body();
     }

     /**
      * Get all transactions for a single day as array.
      *
      * @param Carbon $date
      *
      * @return array
      */
     public function getTransactionsAsArray(Carbon $date): array
     {
         return $this->getTransactionsAsResponse($date)->json();
     }

     protected function getTransactionsAsResponse(Carbon $date)
     {
         $dateStr = $date->format('d-m-Y');
         return Http::withToken(RuterAuth::getApiToken())->get($this->url($dateStr));
     }

     protected function url($date)
     {
         return sprintf($this->config['transactions_url'], $date, $date);
     }
 }
