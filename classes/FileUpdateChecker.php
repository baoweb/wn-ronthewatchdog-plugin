<?php


namespace Baoweb\RonWatchdog\Classes;


use October\Rain\Support\Facades\Config;

class FileUpdateChecker
{
    protected $hashAlgorithm;

    public function __construct()
    {
        $this->hashAlgorithm = Config::get('baoweb.ronwatchdog::hash_algorithm', 'md5');
    }

    public function getHtaccessHash()
    {
        return $this->hashFie(base_path('.htaccess'));
    }

    public function getIndexHash()
    {
        return $this->hashFie(base_path('index.php'));
    }

    public function getAlgorithm() {
        return $this->hashAlgorithm;
    }

    protected function hashFie($path) {
        if($this->hashAlgorithm == 'md5') {
            return md5_file($path);
        }

        return 'false';
    }
}
