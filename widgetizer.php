<?php

/* author: Sarah Stapleton-Gray
  Documentation is here: https://docs.google.com/document/d/1KUCAFdPJ-Nd_oiI8xOaKff7vBT0Iwq1qv7IxCee1qEU/edit

 */


require_once __DIR__ . '/vendor/autoload.php';

try {


//load dependencies
//sanitize _GET
    $sanitizedGet = (filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING));
    $_GET = (filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING));

//GET VARIABLES
//template name

    $tpl = (isset($_GET['tpl'])) ? trim($_GET['tpl']) : '';

// trap for missing tpl param
    if (!$tpl || (strpos($tpl, 'http') !== FALSE)) {

        throw new \Exception("A template name must be defined and be local.  Use tpl=<name of file>");
    } else {
        $template_name = $tpl;
    }

// variable to limit number of displayed items; default = 0 (show all, 100 is a safe bet to list a big list of feeds)

    $num = (isset($sanitizedGet['num'])) ? $sanitizedGet['num'] : 0;
// how many items to return
    if ($num == 0)
        $num = 100;

//cache can be changed from the query string
    $template_cache = ((isset($sanitizedGet['tcache'])) ? trim($sanitizedGet['tcache']) : 10) * 60;
// set template cache, in seconds
    $api_cache = ((isset($sanitizedGet['acache'])) ? trim($sanitizedGet['acache']) : 10) * 60;
// set api cache, in seconds

    $output = ((isset($sanitizedGet['out'])) ? $sanitizedGet['out'] : 'js');
// set how to output
    $source_domain = ((isset($sanitizedGet['source'])) ? trim($sanitizedGet['source']) : '');
// set domain name
    $qp = ((isset($sanitizedGet['qp'])) ? trim($sanitizedGet['qp']) : '');
// get query string parameter
    $rel = ((isset($sanitizedGet['rel'])) ? trim($sanitizedGet['rel']) : '');
// set whether links should be relative
    $siteID = ((isset($sanitizedGet['siteID'])) ? trim($sanitizedGet['siteID']) : '');
// set siteID
    $targ = ((isset($sanitizedGet['targ'])) ? trim($sanitizedGet['targ']) : '');
// set targetlink
    $main_title = ((isset($sanitizedGet['main_title'])) ? trim($sanitizedGet['main_title']) : '');
// set optional main title
    $defImg = ((isset($sanitizedGet['defImg'])) ? trim($sanitizedGet['defImg']) : '');
// set optional main title

    $section = ((isset($sanitizedGet['section'])) ? trim($sanitizedGet['section']) : '');
// set section
    $cache_feed = preg_replace("/[^A-Za-z0-9 ]/", '', $sanitizedGet['feed']);
    ; //get feed URL for caching
    $cacheID = $num . $cache_feed . $siteID . $section;
//set an ID for the cache
//get the feed
    $widget_feed = new Widgetizer\Feed;
    $widget_feed->setFeeds($sanitizedGet['feed']);
//set the feed (or array of feeds is possible)
    $widget_feed->setCache($api_cache);
//set the caching variables
    $widget_feed->setRelativeLink($rel);
//set the option to use relative links or not
    $widget_feed->setLinkSource($qp);
//set the source query string option
    $widget_feed->setTargetLink($targ);
//set the option to open in a new window or not
    $widget_feed->setBaseDomain($widget_feed->curPageURL());
    $widget_feed->setMainTitle($main_title);
//set the option to open in a new window or not

    $feed_results = $widget_feed->fetch();

//initialize Rain TPL
    // config
    $config = array(
        "tpl_dir" => "templates/",
        "cache_dir" => "library/cache/raintpl/",
        "debug" => false, // set to false to improve the speed
        "path_replace" => false,
    );


    $tpl = new Rain\Tpl();

    $tpl->configure($config);
    //   $tpl->assign($sanitizedGet);
//assign
    $tpl->assign('main_title', $main_title);

// if there's a valid template cache the method will return it

    $items_array = $widget_feed->get_array_items($num);
    $item_count = count($items_array);
    //print_r($items_array);
    $tpl->assign("item", $items_array);
    //assign the item level data to the items array

    $items_output = $tpl->draw($template_name, $return_string = true);

//if we want to output javascript:

    if ($output == "js") {
        printf('document.write(decodeURIComponent("%s"));', rawurlencode($items_output));
    } else {
        echo $items_output;
    }
} catch (\Exception $e) {
    $result = $e->getMessage();
    echo "Error message: " . $result;
}


