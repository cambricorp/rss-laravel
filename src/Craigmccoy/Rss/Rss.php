<?php

namespace Craigmccoy\Rss;

use Craigmccoy\Rss\Exception\RssException;
use Illuminate\Support\Facades\Config;

use SimplePie;
use SimplePie_Cache;

class Rss {

    private $feeds;
    private $init = FALSE;

    public function __construct() {
        $this->feeds = new SimplePie();
        $this->feeds->enable_cache(false);

        if (Config::get('rss::cache.enabled')) {
            SimplePie_Cache::register('rss', 'Craigmccoy\Rss\Cache\RssCache');

            $this->feeds->enable_cache();
            $this->feeds->set_cache_duration(Config::get('rss::cache.lifetime'));

            $this->feeds->set_cache_location('rss');
        }

        $urls = Config::get('rss::feeds');
        if (empty($urls)) {
            throw new RssException('A least one RSS feed is required.');
        }

        $this->feeds->set_feed_url($urls);
    }

	public function items($start = 0, $limit = 0) {
        if (!$this->init) {
            $this->feeds->init();
            $this->init = TRUE;
        }

        return $this->feeds->get_items($start, $limit);
	}

}

