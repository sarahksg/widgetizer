<?php
namespace Widgetizer;
/**
 * Description of Form
 * Class to Build Forms from XML configuration file.
 * Forms used to generate widgets.
 * @author SStapleton-Gray
 */
 class Form {

    //public $config_file = "config/widgetizer_feeds_options.xml"; //set configuration file
    public $options_xml = "";
    //xml will be stored here
    public $intro_text = "";
    //text at top of form
    /**
     * Holds form options
     *
     */
    public $options_form = "";

    function set_config_file($config_file) {
        $this -> config_file = $config_file;
    }

    function get_config_file() {
        return $this -> config_file;
    }

    //=============load_config function==================
    function load_config() {
        $config_file = $this -> config_file;

        //check if config file exists

        try {
            if (file_exists($config_file)) {
                $options_xml = @simplexml_load_file($config_file);
            } else {
                echo('Failed to open configuration file ' . $config_file);
                return FALSE;
            }
            //check if XML is well formed -- if it isn't, it won't have loaded
            if ($options_xml == "") {
                echo "Configuration file " . $config_file . " has an XML error.";
                return FALSE;
            } else {

                $this -> options_xml = $options_xml;

            }
        } catch (Exception $e) {

            echo "Message: " . $e -> getMessage() . "<br />";
            echo "File: " . $e -> getFile() . "<br />";
            echo "Line: " . $e -> getLine();
        }
    }

    //=============get_field function for xpath data==================
    function get_field($fieldname) {
        //get data using xpath from config file
        global $widget_type;

        $field_data = $this -> options_xml -> xpath("widget[@type='$widget_type']//$fieldname");

        $field_text = $field_data[0][0];
        // get string from results
        return $field_text;
    }

    //=============get_url function==================
    function get_url() {

        $options_url = $this -> get_field('url');
        $server_port = "";

        if (strpos($options_url, 'http') !== FALSE) {//check if URL in config file has http
            $widget_url = $options_url;
            //if so, use it
        } else {
            //use the current server if the URL is not fully qualified
            if ($_SERVER['SERVER_PORT'] != 80) {
                $server_port = ":" . $_SERVER['SERVER_PORT'];
            }//check for a non-standard URL port
            $widget_url = 'http://' . $_SERVER['SERVER_NAME'] . $server_port . $options_url;
        }
        return $widget_url;
    }

    //=============get_map function==================
    function get_map() {
        //generate URL to show lat/long map
        if (($_GET['lat']) && ($_GET['long'])) {

            $latitude = $_GET['lat'];
            $longitude = $_GET['long'];
            $map_url = "http://maps.google.com/maps?q=" . $latitude . ",+" . $longitude . "+(Your widget center)&iwloc=A&hl=en";
            $map_link = <<<MAP
	    <p><a href="$map_url" target="_new">Click to verify the latitude and longitude on a map.<a/></p>
MAP;
        } else {
            $map_link = "";
        }
        return $map_link;
    }

    //end function
    //=============populate_form function==================
    function populate_form() {

        global $widget_type, $generate;
        //get the global values

        $options_section = $this -> options_xml -> xpath("widget[@type='$widget_type']/options");
        $p_cnt = count($options_section[0] -> option);
        global $options_form, $options_array;
        for ($i = 0; $i < $p_cnt; $i++) {
            $option = $options_section[0] -> option[$i];
            $short_name = trim($option -> short_name);
            $option_name = $option -> name;

            $form_value = trim($option -> default);
            $form_option = "";
            //reset value
            $option_length = strlen($form_value);
            // get the length of the data to set field width

            //check if it is supposed to display a drop-down menu
            if ($generate) {

                if (isset($_GET["$short_name"])) {$form_value = trim($_GET["$short_name"]);
                }

            }
            if ($option -> form_type == "selection") {
                $form_option .= '<select name="' . $short_name . '">';

                foreach ($option->selections->selection as $option_text) {
                    $selected_txt = "";
                     //select the default value or select the chosen value if already generated
                    if ($form_value == $option_text -> value) {
                        $selected_txt = " selected ";
                    } elseif ($option_text -> default == "yes") {
                        $selected_txt = " selected ";
                    }
                    $form_option .= '<option' . $selected_txt . ' value="' . $option_text -> value . '">' . $option_text -> name . '</option>';
                }
                $form_option .= "</select>";
            } else {
                $form_option .= '<input name="' . $short_name . '" value="' . $form_value . '" type="text" style="text-align:left;" onFocus="this.select()" size="' . $option_length . '">';
            }

            $this -> options_form .= <<<EOT
			<h4><li><label for "$short_name">$option_name</label></li></h4>
			 $option->instructions
			 $form_option
EOT;
        }

        $this -> intro_text = $this -> get_field('intro_text');

        $web_url = $this -> get_url();
        //get beginning part of URL from config file or current server
        $this -> options_form .= '<input name="info_url_prefix" value="' . $web_url . '" type="hidden" >';
        $this -> options_form .= '<input name="info_widget_type" value="' . $widget_type . '" type="hidden" >';

    }

    //end function
}
?>
