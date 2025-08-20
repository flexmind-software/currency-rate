<?php

namespace FlexMindSoftware\CurrencyRate\Drivers;

use DateInterval;
use DateTime;
use FlexMindSoftware\CurrencyRate\Contracts\CurrencyInterface;
use FlexMindSoftware\CurrencyRate\Enums\CurrencyCode;
use FlexMindSoftware\CurrencyRate\Models\RateTrait;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class CroatiaDriver extends BaseDriver implements CurrencyInterface
{
    use RateTrait;

    /**
     * @const string
     */
    public const URI = 'https://www.hnb.hr/en/core-functions/monetary-policy/exchange-rate-list/exchange-rate-list';
    /**
     * @const string
     */
    public const DRIVER_NAME = 'croatia';
    /**
     * @var string
     */
    public CurrencyCode $currency = CurrencyCode::HRK;

    /**
     * @var array
     */
    protected array $json;

    /**
     * @return self
     */
    public function grabExchangeRates(): self
    {
        $url = static::URI . '?' . http_build_query($this->queryString());
        $respond = Http::asForm()->post($url, $this->postParams());
        if ($respond->ok()) {
            $zippedContent = $respond->body();

            $dir = sys_get_temp_dir() . '/croatia/';
            $tmp = tempnam($dir, md5(uniqid(microtime(true))));

            // Write the zipped content inside
            file_put_contents($tmp, $zippedContent);

            // Uncompress and read the ZIP archive
            $zip = new ZipArchive();
            if (true === $zip->open($tmp) && true === $zip->extractTo($dir)) {
                $file = head(glob($dir . '/*.json'));
                $this->json = json_decode(file_get_contents($file), true);
                $this->parseResponse();

                unlink($file);
            }

            // Delete the temporary file
            unlink($tmp);
        }

        return $this;
    }

    /**
     * @return array
     */
    private function queryString(): array
    {
        return [
            'p_p_id' => 'tecajnalista_WAR_hnbtecajnalistaportlet',
            'p_p_lifecycle' => 2,
            'p_p_state' => 'normal',
            'p_p_mode' => 'view',
            'p_p_resource_id' => 'downloadDataURL',
            'p_p_cacheability' => 'cacheLevelPage',
        ];
    }

    /**
     * @return array
     */
    private function postParams(): array
    {
        $lastDate = $this->lastDate ?? $this->date;

        $to = $lastDate->format('d.m.Y');
        $from = $lastDate->add(DateInterval::createFromDateString('1 day'))->format('d.m.Y');

        return [
            '_tecajnalista_WAR_hnbtecajnalistaportlet_pageNum' => null,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_dateFromMin' => null,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_dateToMax' => null,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_yearMin' => null,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_yearMax' => null,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_vrstaReport' => 1,
            'year' => -1,
            'yearLast' => -1,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_month' => -1,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_datumVrsta' => 3,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_dateOn' => $this->date->format('d.m.Y'),
            '_tecajnalista_WAR_hnbtecajnalistaportlet_dateFrom' => $to,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_dateTo' => $from,
            'izborValuta' => -1,
            '_izborValuta' => 1,
            '_tecajnalista_WAR_hnbtecajnalistaportlet_vrstaTecaja' => 'srednji',
            '_tecajnalista_WAR_hnbtecajnalistaportlet_fileTypeForDownload' => 'JSON',
        ];
    }

    private function parseResponse()
    {
        foreach ($this->json as $item) {
            $this->data[] = [
                'no' => $item['Exchange rate list number'],
                'code' => $item['Currency'],
                'date' => DateTime::createFromFormat('d.m.Y', $item['Date'])->format('Y-m-d'),
                'driver' => static::DRIVER_NAME,
                'multiplier' => floatval($item['Unit']),
                'rate' => $this->stringToFloat($item['Middle rate']),
            ];
        }
    }

    public function fullName(): string
    {
        return 'Hrvatska Narodna Banka';
    }

    public function homeUrl(): string
    {
        return 'https://www.hnb.hr/home';
    }

    public function infoAboutFrequency(): string
    {
        return __('currency-rate::description.croatia.frequency');
    }
}
