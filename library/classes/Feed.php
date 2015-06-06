<?php

namespace Widgetizer;
/**
 * Feed Class for Widget
 * Requires SimplePie
 * @author SStapleton-Gray
 */
class Feed {

    private $feeds;
    public $cache = 60;
    // in minutes
    public $qp = "";
    // optional query parameter for URL
    /**
     *  Holds fetched feed(s) results
     * @var Object
     */
    public $feed_data;

    public function setFeeds($urls) {
        //remove spaces
        $urls = preg_replace('/\s+/', '', $urls);
        if (strpos($urls,"||")) { 
            //there are multiple feeds, separated by the double pipe
            //so put them into an array
            $urls_array = explode("||", $urls);
            $feeds=$urls_array;
        }
        else {$feeds=$urls;}
        $this->feeds = $feeds;
        //could be an array or a single feed
    }

    public function setCache($cache) {
        $this->cache = $cache;
    }

    public function setRelativeLink($rel) {
        $this->relativeLink = $rel;
    }

    public function setLinkSource($qp) {
        $this->sourceLink = $qp;
    }

    public function setTargetLink($targ) {
        if($targ=="") {$targ="n";}
        $this->targetLink = $targ;
    }

    public function setQueryString($link) {
        //check if link already has a query string

        if (parse_url($link, PHP_URL_QUERY)) {
            $link = $link . "&" . $this->sourceLink;
        } else {
            $link = $link . "?" . $this->sourceLink;
        }
        $this->link = $link;
    }

    public function modify_url($link, $mod) {
        /* $url = modify_url($link, array('p' => 4, 'show' => 'column'));
         */
        $url = $link;
        $querystring = parse_url($url, PHP_URL_QUERY);
        $query = explode("&", $querystring);

        if (!$querystring) {
            $queryStart = "?";
        } else {
            $queryStart = "&";
        }
        // modify/delete data
        foreach ($query as $q) {
            list($key, $value) = explode("=", $q);
            if (array_key_exists($key, $mod)) {
                if ($mod[$key]) {
                    $url = preg_replace('/' . $key . '=' . $value . '/', $key . '=' . $mod[$key], $url);
                } else {
                    $url = preg_replace('/&?' . $key . '=' . $value . '/', '', $url);
                }
            }
        }
        // add new data
        foreach ($mod as $key => $value) {
            if ($value && !preg_match('/' . $key . '=/', $url)) {
                $url .= $queryStart . $key . '=' . $value;
            }
        }
        $this->link = $url;
    }

    public function curPageURL() {
        $pageURL = 'http';
       /* if (isset($_SERVER["HTTPS"])) {
            if($_SERVER["HTTPS"]  == "on") {
            $pageURL .= "s";
            }
            }
        */
        $pageURL .= "://";
        
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    function setBaseDomain($full_url) {
        /**
          This function gets the base domain from a URL, which can be used for configuration.
         * */
        $urlParts = parse_url($full_url);
        //add case for localhost
       // print_r($urlParts);
        $host=$urlParts['host'];
        if($host=="localhost") {
           
             $this->base_domain = $host;
        }
        else {
            $hostParts = explode('.', $urlParts['host']);
            $hostParts = array_reverse($hostParts);
            // $host = $hostParts[1] . '.' . $hostParts[0]; with final extension
            if (($hostParts[1])!== null) {
                $host = $hostParts[1];
                $this->base_domain = $host;
            }
        }

        }
    //}

    function setMainTitle($main_title) {
        /**
          This function sets a main title, which can be used in the templates
         * */
        $this->main_title = $main_title;
        //}
    }

    function fetch() {

        //include_once('library/simplepie.inc'); //include SimplePie 1.2
        //require_once ('library/simplepie_1.3.1.compiled.php');
        //include SimplePie 1.3
        try {
            $feed_data = new \SimplePie();

            // Set which feed to process.
            $feed_data->set_feed_url($this->feeds);
            $feed_data->set_cache_location('library/cache');
            // Run SimplePie.
            $feed_data->init();

            // This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
            $feed_data->handle_content_type();
            $this->feed_data = $feed_data;
            return $feed_data;
        } catch (\Exception $e) {
            throw new \Exception('Error with the feed', 0, $e);
        }
    }

    /**
     * Makes date relative
     */
    function doRelativeDate($posted_date) {
        /**
          This function returns either a relative date or a formatted date depending
          on the difference between the current datetime and the datetime passed.
          $posted_date should be in the following format: YYYYMMDDHHMMSS

          Relative dates look something like this:
          3 weeks, 4 days ago
          Formatted dates look like this:
          on 02/18/2004

          The function includes 'ago' or 'on' and assumes you'll properly add a word
          like 'Posted ' before the function output.

          By Garrett Murray, http://graveyard.maniacalrage.net/etc/relative/
         * */
        $in_seconds = strtotime(substr($posted_date, 0, 8) . ' ' . substr($posted_date, 8, 2) . ':' . substr($posted_date, 10, 2) . ':' . substr($posted_date, 12, 2));
        $diff = time() - $in_seconds;
        $months = floor($diff / 2592000);
        $diff -= $months * 2419200;
        $weeks = floor($diff / 604800);
        $diff -= $weeks * 604800;
        $days = floor($diff / 86400);
        $diff -= $days * 86400;
        $hours = floor($diff / 3600);
        $diff -= $hours * 3600;
        $minutes = floor($diff / 60);
        $diff -= $minutes * 60;
        $seconds = $diff;
        $relative_date = "";
        if ($months > 0) {
            // over a month old, just show date (mm/dd/yyyy format)
            return 'on ' . substr($posted_date, 4, 2) . '/' . substr($posted_date, 6, 2) . '/' . substr($posted_date, 0, 4);
        } else {
            if ($weeks > 0) {
                // weeks and days
                $relative_date .= ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '');
                $relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
            } elseif ($days > 0) {
                // days and hours
                $relative_date .= ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '');
                $relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
            } elseif ($hours > 0) {
                // hours and minutes
                $relative_date .= ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '');
                $relative_date .= $minutes > 0 ? ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
            } elseif ($minutes > 0) {
                // minutes only
                $relative_date .= ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
            } else {
                // seconds only
                $relative_date .= ($relative_date ? ', ' : '') . $seconds . ' second' . ($seconds > 1 ? 's' : '');
            }
        }
        // show relative date and add proper verbiage
        return $relative_date . ' ago';
    }

    /**
     * Returns array of items
     */
    function get_array_items($num) {
        $items_array = array();
        $feed_data = $this->feed_data;
        foreach ($feed_data->get_items(0, $num) as $item) {
            $enclosure = $item->get_enclosure();
            if ($enclosure) {
                $item_image_url = $enclosure->get_link();
                $item_image_height = $enclosure->get_height();
                $item_image_width = $enclosure->get_width();
            }
            $link = $item->get_link();

            //change query string:
            if ($this->sourceLink !== "") {
                $this->modify_url($link, array('source' => $this->sourceLink));

                $link = $this->link;
            }
            if ($this->relativeLink == "yes") {// make links relative if appropriate
                $urlparts = parse_url($link);
                if (($urlparts['fragment']!==null) && $urlparts['fragment'] !== "") {
                    $urlfragment = "#" . $urlparts['fragment'];
                } else {
                    $urlFragment = "";
                }
                $link = $urlparts['path'] . '?' . $urlparts['query'] . $urlFragment;
            }

            //  }

            array_push($items_array, array('url' => $link, 'title' => htmlspecialchars_decode($item->get_title()), 'new_window' => $this->targetLink, 'date' => $item->get_date(), 'relative_date' => $this->doRelativeDate($item->get_date('YmdHis')), 'description' => $item->get_description(), 'author' => $item->get_author(), 'guid' => $item->get_id(), 'image_url' => $item_image_url, 'image_height' => $item_image_height, 'image_width' => $item_image_width, 'base_domain' => $this->base_domain, 'main_title' => $this->main_title));
        }//end foreach loop
        return $items_array;
    }

}

?>
