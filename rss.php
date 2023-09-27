<?php

namespace Plugins\rss;

use \Typemill\Plugin;
use \Typemill\Models\WriteMeta;
use \Typemill\Models\WriteCache;
use \Typemill\Settings;

class rss extends Plugin
{

    # subscribe to the events
    public static function getSubscribedEvents()
    {
        return array(
			'onPagePublished'		=> 'onPagePublished',
			'onPageUnpublished'		=> 'onPageUnpublished',
			'onPageSorted'			=> 'onPageSorted',
			'onPageDeleted'			=> 'onPageDeleted',
            'onPageReady'           => 'onPageReady'
        );
    }

	# at any of theses events, delete the old rss cache files
	public function onPagePublished($item)
	{
		$this->updateRssXmls();
	}
	public function onPageUnpublished($item)
	{
		$this->updateRssXmls();
	}
	public function onPageSorted($inputParams)
	{
		$this->updateRssXmls();
	}
	public function onPageDeleted($item)
	{
		$this->updateRssXmls();
	}

    public function onPageReady($pagedata)
    {
        // TODO: build and get rss per folder

//        $data = $pagedata->getData();
//
//        if(isset($data['item']->folderContent) && is_array($data['item']->folderContent) && method_exists($this, 'addMeta'))
//        {
//            $this->addMeta('rss', '<link rel="alternate" type="application/rss+xml" title="' . $data['title'] . '" href="' . $data['item']->urlAbs . '/rss">');
//        }
    }

    public static function addNewRoutes()
    {
        $routes = [];
        
        $writeCache = new WriteCache();
        // TODO: build and get rss per folder
//        $navigation = $writeCache->getCache('cache', 'navigation.txt');
//
//        foreach($navigation as $pageData){
//            if(isset($pageData->folderContent) && is_array($pageData->folderContent)){
//                $routes[] = [
//                    'httpMethod'    => 'get',
//                    'route'         => $pageData->urlRelWoF . '/rss',
//                    'class'         => 'Plugins\rss\rssController:' . $pageData->slug
//                ];
//            }
//        }
		
		$routes[] = [
			'httpMethod'    => 'get', 
			'route'         => '/rss', 
			'class'         => 'Plugins\rss\rssController:all'
		];
       
        return $routes;
    }

    private function updateRssXmls()
    {
        $writeCache     = new WriteCache();
        $settingsArray  = Settings::loadSettings();
        $settings       = $settingsArray['settings'];
        $structure = $writeCache->getCache('cache', 'structure.txt');

        $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER))->withUserInfo('');

        $rssXml = rssBuilder::build(
            $settings['plugins']['rss']['mainrsstitle'],
            $settings['plugins']['rss']['mainrssdescription'],
            $uri->getBaseUrl(),
            $structure
        );

		$writeCache->updateCache('cache', 'all.rss', false, $rssXml);
    }
}
