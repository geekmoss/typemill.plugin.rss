<?php

namespace Plugins\rss;

use \Typemill\Models\WriteCache;

class rssController
{
    public function __call($name, $arguments) 
    {
        $writeCache = new WriteCache();
        $rssXml     = $writeCache->getCache('cache', $name . '.rss');

        header('Content-Type: application/rss+xml; charset=utf-8');
        die(trim($rssXml));
    }
}