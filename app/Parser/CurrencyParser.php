<?php

namespace App\Parser;
use PHPHtmlParser\Dom;
use Illuminate\Support\Facades\DB;

class CurrencyParser
{
    private $url = 'https://nationalbank.kz/ru/exchangerates/ezhednevnye-oficialnye-rynochnye-kursy-valyut';
    private $dirCacheFile;

    private function setCacheDirFiles() {
        $this->dirCacheFile = dirname(dirname(__DIR__)) . '/public/cache/parser/';
    }

    public function getContent() {
        $this->setCacheDirFiles();

        if($this->cacheCheckStatus($this->url)) {
            return $this->cacheGetData($this->url);
        }

        $headers = array(
            'cache-control: max-age=0',
            'upgrade-insecure-requests: 1',
            'user-agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
            'sec-fetch-user: ?1',
            'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'x-compress: null',
            'sec-fetch-site: none',
            'sec-fetch-mode: navigate',
            'accept-encoding: deflate, br',
            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        );

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $resultQuery = curl_exec($ch);

        if ($resultQuery == false) {
            if ($errno = curl_errno($ch)) {
                $message = curl_strerror($errno);
                $dataReturn = "cURL error ({$errno}):\n {$message}";
            }
            else {
                $dataReturn = $html;
            }
        }
        else {
            $dataReturn = $resultQuery;
        }

        curl_close($ch);

        $this->cacheSetData($this->url, $dataReturn);

        return $dataReturn;
    }

    public function parseCurrencies()
    {
        $content = $this->getContent();
        $dom = new Dom;
        $dom->loadStr($content);
        $table = $dom->find('tbody')->find('tr');

        $data = [];

        foreach ($table as $key => $row) {
            $info = $row->find('td');
            $data[] = ['name' => $info[1]->text,
                        'short_name' => $info[2]->text,
                        'value' => $info[3]->text,
                        'date' =>  date("Y-m-d H:i:s")
            ];
        }

        $this->writeCurrenciesRecords($data);
    }

    private function writeCurrenciesRecords($data)
    {
      // var_dump($data[0]);die();

      // $data = ['name' => 'dfg', 'short_name' => 'fgfg', 'value' => 111, 'date' => '2022-22-22'];
        DB::table('currencies_records')->insertOrIgnore($data);
    }

    public function cacheCheckStatus($urlData) {
        $urlCacheFile = md5($urlData);
        $urlFileCache = $this->dirCacheFile . $urlCacheFile . ".txt";

        /* проверяем существование файла кэша */
        if(file_exists($urlFileCache)) {
            $dataUpdateFile = filemtime($urlFileCache);
            if((time() - $dataUpdateFile) > 86400) {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return false;
        }
    }
    public function cacheSetData($urlData, $dataSave) {
        $urlCacheFile = md5($urlData);
        /* путь до файла с кэшем */
        $urlFileCache = $this->dirCacheFile . $urlCacheFile . ".txt";

        $fh = fopen($urlFileCache, 'w');
        fwrite($fh, $dataSave);
        fclose($fh);

        return $dataSave;
    }
    public function cacheGetData($urlData) {

        $urlCacheFile = md5($urlData);
        /* путь до файла с кэшем */
        $urlFileCache = $this->dirCacheFile . $urlCacheFile . ".txt";

        /* проверяем существование файла кэша и его актуальность */
        if($this->cacheCheckStatus($urlData)) {
            $dataCachFile = file_get_contents($urlFileCache);
            echo 'from cache';
            return $dataCachFile;
        }
        else {
            return false;
        }
    }
}
