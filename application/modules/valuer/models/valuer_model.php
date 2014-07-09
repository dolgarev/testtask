<?php

class Valuer_model extends CI_Model
{
    public function __construct() {
        parent::__construct();

        $this->load->library('simple_html_dom');
    }

    public function get_rank($domain, $keyword) {
        $rank = FALSE;

        $html = $this->get_search_by_keyword($keyword);

        if (is_object($html)) {
            $links = $this->get_links($html);
            $domain = preg_replace('/^www\.(.+\.)/i', '$1', rtrim($domain, '/'));

            foreach ($links as $index => $link) {
                if (preg_replace('/^www\.(.+\.)/i', '$1', parse_url($link, PHP_URL_HOST)) === $domain) {
                    $rank = $index + 1;
                    break;
                }
            }
            $rank = (int)$rank;

            $this->db->set('added', 'NOW()', FALSE);
            $this->db->insert('results', array(
                'domain'    => $domain,
                'keyword'   => $keyword,
                'rank'      => $rank
            ));
        }

        return $rank;
    }

    public function get_search_by_keyword($keyword) {
        if (!is_string($keyword) || strlen($keyword) === 0) {
            throw new Exception('Invalid param KEYWORLD');
        }

        $proxies = $this->get_proxies();

        //Описание параметров запроса к Гуглю
        //[http://sivway.com/poleznoe/operatory-i-parametry-poiska-google.html]
        //[http://moz.com/ugc/the-ultimate-guide-to-the-google-search-parameters]
        $url = 'http://www.google.com/search?' . http_build_query(array(
            'q'         => $keyword,
            'btnG'      => 'Search',
            'gbv'       => 1,
            'num'       => 100,
            'pws'       => 0,
            'safe'      => 'on',
            'filter'    => 1
        ));

        $opts = array(
            'http' => array(
                'method' => 'GET',
                'header' => join("\r\n", array(
                    'Host: www.google.com',
                    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Referer: ' . $url,
                    'Connection: keep-alive',
                )),
                'timeout' => 60
            )
        );

        $retry = count($proxies);

        do {
            $html = file_get_html($url, FALSE, stream_context_create($opts));

            if ($html === FALSE && !empty($proxies)) {
                $index = rand(0, count($proxies) - 1);
                $proxy = $proxies[$index];
                unset($proxies[$index]);

                $opts['http']['proxy'] = "tcp://{$proxy['ip']}:{$proxy['port']}";
                $opts['http']['request_fulluri'] = TRUE;
            }
        }
        while ($html === FALSE && $retry--);

        return $html;
    }

    public function get_links(simple_html_dom $html) {
        return array_map(function($link) {
            return preg_match('/^\/url\?q=((https?|ftp):\/\/[^\/]+)/i', $link->href, $matches) ? $matches[1]: NULL;
        }, $html->find('li.g > h3.r > a[href^="/url?q="]'));
    }

    public function get_proxies() {
        libxml_use_internal_errors(TRUE);

        $proxies = array();
        $proxy_lists = simplexml_load_file('http://www.xroxy.com/proxyrss.xml');

        //[http://stackoverflow.com/questions/1186107/simple-xml-dealing-with-colons-in-nodes]
        if (is_object($proxy_lists)) {
            foreach ($proxy_lists->channel->item as $item) {
                $proxy_items = $item->children('http://www.proxyrss.com/content');

                foreach ($proxy_items->proxy as $proxy) {
                    array_push($proxies, (array)$proxy);
                }
            }
        }

        libxml_use_internal_errors(FALSE);

        return $proxies;
    }
}
