<?php

namespace Craigmccoy\Rss\Facades;

use Illuminate\Support\Facades\Facade;

class Rss extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'rss';
	}

}

