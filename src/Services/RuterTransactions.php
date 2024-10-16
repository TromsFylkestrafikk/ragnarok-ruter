<?php

namespace Ragnarok\Ruter\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Ragnarok\Ruter\Facades\RuterAuth;
use Ragnarok\Sink\Services\DbBulkInsert;

/**
 * Retrieval of transactions done in the KK 1.0 cooperation
 */
class RuterTransactions
{
    /**
     * @var DbBulkInsert
     */
    protected $transInserter = null;

    /**
     * @var DbBulkInsert
     */
    protected $paxInserter = null;

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

    /**
     * Import transactions to DB.
     *
     * @param string $chunkId
     * @param array $transactions
     *
     * @return int
     */
    public function import(string $chunkId, array $transactions): int
    {
        $rowCount = 0;

        $this->transInserter = new DbBulkInsert('ruter_transactions');
        $this->paxInserter = new DbBulkInsert('ruter_passengers');
        foreach ($transactions as $row) {
            $rowCount++;
            $this->insertTransaction($chunkId, $row);
        }
        $this->transInserter->flush();
        $this->paxInserter->flush();
        return $rowCount;
    }

    /**
     * Delete transactions from DB.
     *
     * @param string $chunkId
     *
     * @return $this
     */
    public function delete(string $chunkId): RuterTransactions
    {
        DB::table('ruter_transactions')->where('chunk_date', $chunkId)->delete();
        DB::table('ruter_passengers')->where('chunk_date', $chunkId)->delete();
        return $this;
    }

    /**
     * Insert a single transaction to DB.
     *
     * @param string $chunkId The associated chunk ID this dump belongs to
     * @param array $json The raw transaction data from Ruter
     */
    protected function insertTransaction(string $chunkId, array $json): void
    {
        $chunkDate = new Carbon($chunkId);
        $transId = md5($json['id']);
        $this->transInserter->addRecord([
            'id'                => $transId,
            'id_real'           => $json['id'],
            'chunk_date'        => $chunkDate,
            'order_id'          => $json['orderId'],
            'order_date'        => $this->safeDate($json['orderDate']),
            'order_status'      => $json['orderStatus'],
            'payer_app_instance_name' => $json['payerAppInstanceName'],
            'payer_app_platform' => $json['payerAppPlatform'],
            'payer_app_version' => $json['payerAppVersion'],
            'payer_phone_type'  => $json['payerTelephoneType'],
            'payer_os_version'  => $json['payerOsVersion'],
            'app_instance_name' => $json['appInstanceName'],
            'app_instance_id'   => $json['appInstanceId'],
            'payer_id'          => $json['payerId'],
            'payment_id'        => $json['paymentId'],
            'payment_method'    => $json['paymentMethod'],
            'payment_status'    => $json['paymentStatus'],
            'amount'            => $json['amount'],
            'vat_amount'        => $json['vatAmount'],
            'vat_percentage'    => $json['vatPercentage'],
            'credit_amount'     => $json['creditAmount'],
            'credit_date'       => new Carbon($json['creditDate']),
            'transaction_type'  => $json['transType'],
            'ticket_number'     => $json['ticketNumber'],
            'ticket_type'       => $json['ticketType'],
            'ticket_type_id'    => $json['productTemplateId'],
            'ticket_status'     => $json['ticketStatus'],
            'owner'             => $json['owner'],
            'valid_from'        => $this->safeDate($json['validFrom']),
            'valid_to'          => $this->safeDate($json['validTo']),
            'stop_from'         => $json['fromStop'],
            'stop_to'           => $json['toStop'],
            'zone_from'         => $json['fromZone'],
            'zone_to'           => $json['toZone'],
            'zones'             => $json['nrOfZones'],
            'zones_all'         => $json['allZones'],
            'distribution_type' => $json['distributionType'],
            'cs_ordered_by'     => $json['csOrderedBy'],
            'cs_comment'        => $json['csComment'],
            'cs_invoice_ref'    => $json['csInvoiceReference'],
        ]);
        foreach ($json['passengers'] as $pax) {
            $this->paxInserter->addRecord([
                'chunk_date'        => $chunkDate,
                'transaction_pax_id' => $pax['id'],
                'transaction_id'     => $transId,
                'product_id'         => $pax['productId'],
                'profile_id'         => $pax['profileId'],
                'profile'            => $pax['profile'],
                'count'              => $pax['count'],
            ]);
        }
    }

    protected function getTransactionsAsResponse(Carbon $date)
    {
        $dateStr = $date->format('d-m-Y');
        return Http::withToken(RuterAuth::getApiToken())->get($this->url($dateStr));
    }

    protected function url($date)
    {
        return sprintf(config('ragnarok_ruter.transactions_url'), $date, $date);
    }

    /**
     * Create a new Carbon object from unsafe date string
     *
     * @param string $dateStr

     * @return Carbon
     */
    protected function safeDate($dateStr)
    {
        return new Carbon($this->phpSafeIsoDateString($dateStr));
    }

    /**
     * Workaround for https://bugs.php.net/bug.php?id=64814
     *
     * @param string $dateStr
     *
     * @return string
     */
    protected function phpSafeIsoDateString($dateStr)
    {
        if (version_compare(PHP_VERSION, '8.1.0', '>=')) {
            return $dateStr;
        }
        return preg_replace_callback(
            '/^(?<main>\d{4}-\d{2}-\d{2}[ T](\d{2}:?){2}\d{2})(?<fraction>\.\d{7,})?(?<tz>\+\d{2}:?\d{2})?$/',
            fn ($matches) => empty($matches['fraction'])
                ? $matches[0]
                : $matches['main'] . substr($matches['fraction'], 0, 7) . $matches['tz'] ?? '',
            $dateStr
        );
    }
}
