<?php


namespace Baoweb\RonWatchdog\Classes;


interface VersionReaderInterface
{
    public function getVersionNumber();

    public function getPlatform();
}
