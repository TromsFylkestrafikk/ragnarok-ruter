<?php

namespace TromsFylkestrafikk\RagnarokRuter\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use TromsFylkestrafikk\RagnarokRuter\Facades\RuterAuth;
use TromsFylkestrafikk\RagnarokSink\Traits\LogPrintf;

/**
 * Retrieval of transactions done in the KK 1.0 cooperation
 */
class RuterTransactions
{
    use LogPrintf;

    public function __construct(protected $config)
    {
        $this->logPrintfInit('[Ruter Transactions]: ');
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

    /**
     * Import transactions to DB.
     *
     * @param array $transactions
     *
     * @return $this
     */
    public function import($transactions)
    {
        $rowCount = 0;
        foreach ($transactions as $row) {
            $rowCount++;
            $this->insertTransaction($row);
        }
        $this->debug('Imported %d transactions', count($transactions));
        return $this;
    }

    /**
     * Delete transactions from DB.
     *
     * @param Carbon $date
     *
     * @return $this
     */
    public function delete($date)
    {
        $this->debug("Purging imported data for %s", $date->format('Y-m-d'));
        DB::table('ruter_transactions')->where('order_date', $date)->delete();
        return $this;
    }

    /**
     * Insert a single transaction to DB.
     *
     * @param array $json The raw transaction data from Ruter
     *
     * @return int The created transaction ID
     */
    protected function insertTransaction($json)
    {
        $orderDate = $this->safeDate($json['orderDate']);
        $transaction_id = DB::table('ruter_transactions')->insertGetId([
            'order_id'          => $json['orderId'],
            'order_date'        => $orderDate->format('Y-m-d'),
            'order_time'        => $orderDate->format('H:i:s'),
            'order_status'      => $json['orderStatus'],
            'payer_app_id'      => $json['payerAppInstanceName'],
            'payer_platform'    => $json['payerAppPlatform'],
            'payer_version'     => $json['payerAppVersion'],
            'payer_phone_type'  => $json['payerTelephoneType'],
            'app_id'            => $json['appInstanceName'],
            'app_instance_id'   => $json['appInstanceId'],
            'payer_id'          => $json['payerId'],
            'payment_id'        => $json['paymentId'],
            'payment_method'    => $json['paymentMethod'],
            'payment_status'    => $json['paymentStatus'],
            'amount'            => $json['amount'],
            'vat'               => $json['vatAmount'],
            'vat_percentage'    => $json['vatPercentage'],
            'credit_amount'     => $json['creditAmount'],
            'transaction_type'  => $json['transType'],
            'ticket_number'     => $json['ticketNumber'],
            'ticket_type'       => $json['ticketType'],
            'ticket_type_id'    => $json['productTemplateId'],
            'ticket_status'     => $json['ticketStatus'],
            'owner'             => $json['owner'],
            'valid_from'        => $this->safeDate($json['validFrom'])->format("Y-m-d H:i:s"),
            'valid_to'          => $this->safeDate($json['validTo'])->format("Y-m-d H:i:s"),
            'stop_from'         => $json['fromStop'],
            'stop_to'           => $json['toStop'],
            'zone_from'         => $json['fromZone'],
            'zone_to'           => $json['toZone'],
            'zones'             => $json['nrOfZones'],
            'zones_all'         => $json['allZones'],
            'event_time'        => isset($json['eventTime']) ? $this->safeDate($json['eventTime'])->format("Y-m-d H:i:s") : null,
            'distribution_type' => $json['distributionType'],
            'cs_ordered_by'     => $json['csOrderedBy'],
            'cs_comment'        => $json['csComment'],
            'cs_invoice_ref'    => $json['csInvoiceReference'],
            'platform_version'  => $json['platformVersion'],
        ]);
        foreach ($json['passengers'] as $pax) {
            DB::table('ruter_passengers')->insert([
                'transaction_id'    => $transaction_id,
                'product_id'        => $pax['productId'],
                'profile_id'        => $pax['profileId'],
                'profile'           => $pax['profile'],
                'count'             => $pax['count']
            ]);
        }
        return $transaction_id;
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
