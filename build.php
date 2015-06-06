<?php
/*   Build a Widget
  build.php
  Uses options config file in xml
 */

use Widgetizer\Form;
require_once __DIR__ . '/vendor/autoload.php';

try {
    $builder = new Form();
    $builder->set_config_file('config/widgetizer_feeds_options.xml');
    $builder->load_config();

// check for status of submit buttons	

    $generate = (isset($_GET['info_generate'])) ? $_GET['info_generate'] : '';
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    if (!$type && $generate) {
        $type = $_GET['info_widget_type'];
    }// get the widget type from the form 

    if (!$type) {
        echo("A widget type must be defined.  Use type=<name of widget type>.  Example: <a href='" . htmlentities($_SERVER['PHP_SELF']) . "?type=rss'>" . htmlentities($_SERVER['PHP_SELF']) . "?type=rss</a>");
        //return FALSE;
        die;
    } else {
        $widget_type = $type;
    }
    $options_form = $builder->populate_form();

    if ($generate) {  // if the form has been submitted...
        // URLs for a preview or a generated feed link
        $options_string = $_GET['info_url_prefix'] . "?";
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 5) !== 'info_') {  //if the key wasn't marked as an informational field
                $value = preg_replace('/\s\s+/', '\+', $value); //replace spaces with +
                $options_string.="&" . $key . "=" . htmlentities($value);
            }
        }
    } else {  // if this is being shown for the first time
    }
} catch (\Exception $e) {
    $result[] = $e->getMessage();
    echo $result;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
    <head>
        <title>Build a <?php echo $widget_type; ?> widget</title>
            <script type="text/javascript" language="Javascript">
                <!--
                function query_str(form) {

                    // builds a proper query string by extracting Javascript form variables
                    // so we can open a preview in a new window
                    //options = encodeURIComponent(form.src.value);
                    options += '?targ=' + form.targ.value;
                    options += '&targ=' + form.targ.value;
                    options = options.text().replace(/ /g, ''); //strip spaces

                    return(options);
                }
                //-->
            </script>
            <style>
                hr {
                    color:#c00;
                    background-color:#c00;
                    width:60%;
                }
                h3 {
                    color:#c00;
                }
            </style>
    </head>
    <body>
        <div id="content" style="max-width: 1000px;   margin-left: auto ;  margin-right: auto ;">
            <h2 align="center">Build a <?php echo $widget_type; ?> widget and preview the results</h2>
            <br />
            <p><?php echo $builder->get_field('intro_text'); ?></p>
            Change the defaults and preview the output.
            <p>The form below will help you customize your widget. </p>


<?php if ($generate): ?>
                <hr />
               

                <p>This may take a moment because we may be getting it live rather than using cached data and output.  The data will show without a particular stylesheet.</p>
                <div id="previewdiv" title="Preview Widget">                  
                    <script language="JavaScript" src="<?php echo (htmlentities($options_string)); ?>" type="text/javascript"></script>
                </div>
                <hr />
                <h3>Get Your Code Here</h3>
                <p class="first">Below is the code you need to copy and paste to your own web page to include this widget. The NOSCRIPT tag provides a link to a HTML display of the widget for users who may not have JavaScript enabled. </p>
                <form>
                    <span class="caption"><strong>cut and paste javascript:</strong></span><br /><textarea name="t" rows="10" cols="70"  onFocus="this.select()">
&lt;script language="JavaScript" src="<?php echo (htmlentities($options_string)); ?>" type="text/javascript"&gt;&lt;/script&gt;

                    </textarea><br />
                    <p>Here is the <strong>URL only</strong>, if you want to view it in a browser or use it in a form that requires that:</p>
                    <textarea rows="3" cols="70"  onFocus="this.select()">
<?php echo trim((htmlentities($options_string))); ?>
                    </textarea>
                </form>


<?php endif ?>
            <hr>
                <h3>Customize your widget</h3>
                <form method="get" action="build.php"  name="builder">
                    Modify the following to build your custom widget code:
                    <form>
                        <ol>
                        <?php echo $builder->options_form; ?>
                        </ol>

                        <br />
                        <div align="center">
                        <!--<input type="button" name="preview" value="Preview Feed" onClick="pr=window.open('preview.php?src=' + query_str(document.builder), 'prev', 'scrollbars,resizable,left=20,screenX=20,top=40,screenY=40,height=580,width=700'); pr.focus();"
                        /> <br />    -->
                            <input type="submit" name="info_generate" value="Generate widget" style="font-size: 20px" />

                            <button onclick="location.href = '/apps/widgetizer/build.php?type=<?php echo $widget_type; ?>'" type="button" style="font-size: 20px">
                                Reset!</button>


                        </div>




                    </form>
                    </div>
                    <div style="align:center">  
                        <hr width="75%"></hr>
                        <p><a href="https://docs.google.com/document/d/1KUCAFdPJ-Nd_oiI8xOaKff7vBT0Iwq1qv7IxCee1qEU/edit">Widgetizer documentation</a></p>
                    </div>

                    </body>
                    </html>
