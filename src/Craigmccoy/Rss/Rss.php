<?php

namespace Craigmccoy\Rss;

use Craigmccoy\Rss\Exception\RssException;
use Illuminate\Support\Facades\Config;

use SimplePie;
use SimplePie_Cache;

class Rss
{

    private $simplepie;

    public function __construct()
    {
        $this->simplepie = new SimplePie();
        $this->simplepie->enable_cache(false);

        if (Config::get('rss::cache.enabled')) {
            SimplePie_Cache::register('rss', 'Craigmccoy\Rss\Cache\RssCache');

            $this->simplepie->enable_cache();
            $this->simplepie->set_cache_duration(Config::get('rss::cache.lifetime'));

            $this->simplepie->set_cache_location('rss');
        }

        $urls = Config::get('rss::feeds');
        if (empty($urls)) {
            throw new RssException('A least one RSS feed is required.');
        }

        $this->simplepie->set_feed_url($urls);
        $this->simplepie->init();
    }

    public function items($start = 0, $limit = 0)
    {
        $dateFormat = Config::get('rss::date.format');

        $simplePieItems = $this->simplepie->get_items($start, $limit);
        $items = array();

        if (is_array($simplePieItems)) {
            foreach ($simplePieItems as $sItem) {
                $item = array();

                $iCategories = $sItem->get_categories();
                $item['categories'] = array();

                if (is_array($iCategories)) {
                    foreach ($iCategories as $category) {
                        $tmp = array(
                            'term' => $category->get_term(),
                            'scheme' => $category->get_scheme(),
                            'label' => $category->get_label()
                        );

                        $item['categories'][] = self::filterNull($tmp);
                    }
                }

                $iAuthors = $sItem->get_authors();
                $item['authors'] = array();

                if (is_array($iAuthors)) {
                    foreach ($iAuthors as $author) {
                        $tmp = array(
                            'name' => $author->get_name(),
                            'link' => $author->get_link(),
                            'email' => $author->get_email()
                        );

                        $item['authors'][] = self::filterNull($tmp);
                    }
                }

                $iContributors = $sItem->get_contributors();
                $item['contributors'] = array();

                if (is_array($iContributors)) {
                    foreach ($iContributors as $contributor) {
                        $tmp = array(
                            'name' => $contributor->get_name(),
                            'link' => $contributor->get_link(),
                            'email' => $contributor->get_email()
                        );

                        $item['contributors'][] = self::filterNull($tmp);
                    }
                }

                $iEnclosures = $sItem->get_enclosures();
                $item['enclosures'] = array();

                if (is_array($iEnclosures)) {
                    foreach ($iEnclosures as $enclosure) {
                        $link = $enclosure->get_link();
                        if (empty($link)) {
                            continue;
                        }

                        $eCaptions = $enclosure->get_captions();
                        $captions = array();

                        if (is_array($eCaptions)) {
                            foreach ($eCaptions as $caption) {
                                $tmp = array(
                                    'endtime' => $caption->get_endtime(),
                                    'language' => $caption->get_language(),
                                    'starttime' => $caption->get_starttime(),
                                    'text' => $caption->get_text(),
                                    'type' => $caption->get_type()
                                );

                                $captions[] = self::filterNull($tmp);
                            }
                        }

                        $eCategories = $enclosure->get_categories();
                        $categories = array();

                        if (is_array($eCategories)) {
                            foreach ($eCategories as $category) {
                                $tmp = array(
                                    'term' => $category->get_term(),
                                    'scheme' => $category->get_scheme(),
                                    'label' => $category->get_label()
                                );

                                $categories[] = self::filterNull($tmp);
                            }
                        }

                        $eCopyright = $enclosure->get_copyright();
                        $copyright = NULL;

                        if (!empty($eCopyright)) {
                            $copyright = array(
                                'url' => $eCopyright->get_url(),
                                'attribution' => $eCopyright->get_attribution()
                            );

                            $copyright = self::filterNull($copyright);
                        }

                        $eCredits = $enclosure->get_credits();
                        $credits = array();

                        if (is_array($eCredits)) {
                            foreach ($eCredits as $credit) {
                                $tmp = array(
                                    'role' => $credit->get_role(),
                                    'scheme' => $credit->get_scheme(),
                                    'name' => $credit->get_name()
                                );

                                $credits[] = self::filterNull($tmp);
                            }
                        }

                        $eRatings = $enclosure->get_ratings();
                        $ratings = array();

                        if (is_array($eRatings)) {
                            foreach ($eRatings as $rating) {
                                $tmp = array(
                                    'scheme' => $rating->get_scheme(),
                                    'value' => $rating->get_value()
                                );

                                $ratings[] = self::filterNull($tmp);
                            }
                        }

                        $eRestrictions = $enclosure->get_restrictions();
                        $restrictions = array();

                        if (is_array($eRestrictions)) {
                            foreach ($eRestrictions as $restriction) {
                                $tmp = array(
                                    'relationship' => $restriction->get_relationship(),
                                    'type' => $restriction->get_type(),
                                    'value' => $restriction->get_value()
                                );

                                $restrictions[] = self::filterNull($tmp);
                            }
                        }

                        $tmp = array(
                            'bitrate' => $enclosure->get_bitrate(),
                            'captions' => $captions,
                            'categories' => $eCategories,
                            'channels' => $enclosure->get_channels(),
                            'copyright' => $copyright,
                            'credits' => $credits,
                            'description' => $enclosure->get_description(),
                            'duration' => $enclosure->get_duration(),
                            'expression' => $enclosure->get_expression(),
                            'extension' => $enclosure->get_extension(),
                            'framerate' => $enclosure->get_framerate(),
                            'handler' => $enclosure->get_handler(),
                            'hashes' => $enclosure->get_hashes(),
                            'height' => $enclosure->get_height(),
                            'language' => $enclosure->get_language(),
                            'keywords' => $enclosure->get_keywords(),
                            'length' => $enclosure->get_length(),
                            'link' => $link,
                            'medium' => $enclosure->get_medium(),
                            'player' => $enclosure->get_player(),
                            'ratings' => $ratings,
                            'restrictions' => $restrictions,
                            'sampling_rate' => $enclosure->get_sampling_rate(),
                            'size' => $enclosure->get_size(),
                            'thumbnails' => $enclosure->get_thumbnails(),
                            'title' => $enclosure->get_title(),
                            'type' => $enclosure->get_type(),
                            'width' => $enclosure->get_width(),
                            'real_type' => $enclosure->get_real_type()
                        );

                        $item['enclosures'][] = self::filterNull($tmp);
                    }
                }

                $item += array(
                    'base' => $sItem->get_base(),
                    'id' => $sItem->get_id(),
                    'title' => $sItem->get_title(),
                    'description' => $sItem->get_description(),
                    'content' => $sItem->get_content(),
                    'copyright' => $sItem->get_copyright(),
                    'date' => $sItem->get_date($dateFormat),
                    'updated_date' => $sItem->get_updated_date($dateFormat),
                    'permalink' => $sItem->get_permalink(),
                    'links' => $sItem->get_links(),
                    'latitude' => $sItem->get_latitude(),
                    'longitude' => $sItem->get_longitude()
                );

                $items[] = self::filterNull($item);
            }
        }

        return $items;
    }

    private static function filterNull($array)
    {
        foreach ($array as $index => $value) {
            if (is_null($value)) {
                unset($array[$index]);
            }
        }
        return $array;
    }
}

