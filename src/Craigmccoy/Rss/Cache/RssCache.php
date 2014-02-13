<?php

namespace Craigmccoy\Rss\Cache;

use Illuminate\Support\Facades\Cache;

use SimplePie;
use SimplePie_Cache_Base;

class RssCache implements SimplePie_Cache_Base {

    protected $name;

    public function __construct($location, $name, $type) {
        $this->name = $name . ':' . $type;
    }

    public function save($data) {
        if ($data instanceof SimplePie) {
            $data = $data->data;
        }
        Cache::put($this->name, serialize($data), 3600);

        return TRUE;
    }

    public function load() {
        if (Cache::has($this->name)) {
            return unserialize(Cache::get($this->name));
        }

        return FALSE;
    }

    public function mtime() {
        if (Cache::has('mtime:' . $this->name)) {
            return Cache::get('mtime:' . $this->name);
        }

        return FALSE;
    }

    public function touch() {
        if (Cache::has('mtime:' . $this->name)) {
            $time = time();
            Cache::put('mtime:' . $this->name, $time);
            return $time;
        }

        return FALSE;
    }

    public function unlink() {
        if (Cache::has($this->name)) {
            return Cache::forget($this->name);
        }

        return FALSE;
    }

}
