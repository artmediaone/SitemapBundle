<?php

namespace Amo\SitemapBundle\Service;

class Sitemap
{
    protected $container;
    
    public function __construct($container)
    {
        $this->container = $container;
    }
    public function generate()
    {
        $results = $this->scan($this->container->getParameter('amo_sitemap.url'));
        if(count($results) > 0)
        {
            $this->save($results);
        }
    }
    
    private function path($p)
    {
        $a = explode('/', $p);
        $length = strlen($a[count($a) - 1]);
        return (substr($p, 0, strlen($p) - $length));
    }
    
    private function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    
    private function scan($url)
    {
        $scanned = array();
        $results = array();
        array_push($scanned, $url);
        $html = $this->getUrl($url);
        $a1 = explode('<a', $html);
        foreach($a1 as $key => $val)
        {
            
            $parts = explode('>', $val);
            $a = $parts[0];
            $aparts = explode('href=', $a);
            if(isset($aparts[1]))
            {
                $hrefparts = explode(' ', $aparts[1]);
                $hrefparts2 = explode('#', $hrefparts[0]);
                $href = str_replace("\"", '', $hrefparts2[0]);
                if((substr($href, 0, 7) != "http://") && (substr($href, 0, 8) != 'https://') && (substr($href, 0, 6) != 'ftp://'))
                {
                    if(isset($href[0]) && $href[0] == '/') 
                    {
                        $href = "$scanned[0]$href";
                    }
                    else
                    {
                        $href = $this->path($url) . $href;
                    }
                }
                if(substr($href, 0, strlen($scanned[0])) == $scanned[0])
                {
                    $ignore = false;
                    if(isset($skip))
                    {
                        foreach($skip as $k => $v)
                        {
                            if(substr($href, 0, strlen($v)) == $v)
                            {
                                $ignore = true;
                            }
                        }
                    }
                    if(!$ignore && !in_array($href, $scanned))
                    {
                        $results[] = $href;
                        $this->scan($href);
                    }
                }
            }
        }
        return $results;
    }
    
    private function save($data)
    {
        $pf = fopen($this->container->getParameter('amo_sitemap.dir') . $this->container->getParameter('amo_sitemap.filename'), 'w');
        $header = '<?xml version="1.0" encoding="UTF-8"?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
        $new_line = "\n";
        $header .= $new_line;
        fwrite($pf, $header);
        foreach($data as $item)
        {
            $entry = '<url>'.$new_line;
            $entry .= '<loc>'.$item.'</loc>'.$new_line;
            $entry .= '<changefreq>'.$this->container->getParameter('amo_sitemap.freq').'</changefreq>'.$new_line;
            $entry .= '<priority>'.$this->container->getParameter('amo_sitemap.priority').'</priority>'.$new_line;
            $entry .= '</url>'.$new_line;
            fwrite($pf, $entry);
        }
        $end = '</urlset>';
        fwrite($pf, $end);
        fclose($pf);
    }
}