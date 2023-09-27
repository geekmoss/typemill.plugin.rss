<?php

namespace Plugins\rss;

class rssBuilder {
    private \DOMDocument $dom;
    private \DOMElement $channel;

    public function __construct(string $chTitle, string $chDesc, string $chLink)
    {
        $this->dom = $this->getDOM();
        $this->channel = $this->makeChannel(
            $chTitle,
            $chDesc,
            $chLink,
        );
    }

    public static function build(string $rootTitle, string $rootDesc, string $rootLink, array $arr): string {
        // TODO: Remove workaround with description (rn using title)
        $builder = new rssBuilder($rootTitle, $rootDesc, $rootLink);

        // Per folder
        foreach ($arr as $channel) {
            if ($channel->status !== 'published') { continue; }

            if ($channel->elementType === 'file') {
                $builder->addItem(
                    $channel->name,
                    $channel->name,
                    $channel->urlAbs,
                );
            } else {
                $builder->addFolderToChannel($channel->folderContent);
            }
        }

        return $builder->dom->saveXML();
    }

    private function addFolderToChannel(array $arr): void {
        foreach ($arr as $item) {
            if ($item->elementType === 'folder' && $item->status === 'published') {
                $this->addFolderToChannel($item->folderContent);
            }
            elseif ($item->elementType === 'file' && $item->status === 'published') {
                $this->addItem(
                    $item->name,
                    $item->name,
                    $item->urlAbs,
                );
            }
        }
    }

    private function getDOM(): \DOMDocument {
        return new \DOMDocument('1.0', 'utf-8');
    }

    private function makeChannel(string $title, string $desc, string $link): \DOMElement {
        $e = $this->dom->createElement('channel');

        $e->appendChild($this->dom->createElement('title', $title));
        $e->appendChild($this->dom->createElement('description', $desc));
        $e->appendChild($this->dom->createElement('link', $link));

        $rss = $this->dom->createElement('rss');
        $rss->setAttribute('version', '2.0');
        $rss->setAttribute( 'xmlns:atom', 'http://www.w3.org/2005/Atom');
        $rss->appendChild($e);
        $this->dom->appendChild($rss);

        return $e;
    }

    private function addItem(string $title, string $desc, string $link): \DOMElement {
        $item = $this->dom->createElement('item');

        $item->appendChild($this->dom->createElement('title', $title));
        $item->appendChild($this->dom->createElement('description', $desc));
        $item->appendChild($this->dom->createElement('link', $link));
        $item->appendChild($this->dom->createElement('guid', $link));

        $this->channel->appendChild($item);

        return $item;
    }
}