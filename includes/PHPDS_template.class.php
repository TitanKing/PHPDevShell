<?php

interface iPHPDS_activableGUI
{
    public function construct();

    public function activate();
}

/**
 * Class responsible to deal with the visual representation of a page.
 *
 * Interact with various other components such as views, themes, ...
 *
 */
class PHPDS_template extends PHPDS_dependant
{
    /**
     * Contains script HTML data.
     *
     * @var string
     */
    public $HTML = '';
    /**
     * Contains script HOOK data.
     *
     * @var string
     */
    public $HOOK = '';
    /**
     * Use to manage the view class.
     *
     * @var object
     */
    public $view;
    /**
     * Adds content to head of page.
     * @var string
     */
    public $modifyHead = '';
    /**
     * Modify Output Text Logo
     * @var mixed
     */
    public $modifyOutputTextLogo = false;
    /**
     * Modify Output Logo
     * @var mixed
     */
    public $modifyOutputLogo = false;
    /**
     * Modify Output Heading
     * @var mixed
     */
    public $modifyOutputHeading = false;
    /**
     * Modify Output Time
     * @var mixed
     */
    public $modifyOutputTime = false;
    /**
     * Modify Output Login Link.
     * @var mixed
     */
    public $modifyOutputLoginLink = false;
    /**
     * Modify Output User.
     * @var mixed
     */
    public $modifyOutputUser = false;
    /**
     * Modify Output Role.
     * @var mixed
     */
    public $modifyOutputRole = false;
    /**
     * Modify Output Group.
     * @var mixed
     */
    public $modifyOutputGroup = false;
    /**
     * Modify Output Title.
     * @var mixed
     */
    public $modifyOutputTitle = false;
    /**
     * Modify Output Node.
     * @var mixed
     */
    public $modifyOutputMenu = false;
    /**
     * Modify Output Subnav.
     * @var mixed
     */
    public $modifyOutputSubnav = false;
    /**
     * Modify Output Footer.
     * @var mixed
     */
    public $modifyOutputFooter = false;
    /**
     * Modify Output Controller.
     * @var mixed
     */
    public $modifyOutputController = false;
    /**
     * Check if lightbox headers should be added for lightbox node.
     * @var type
     */
    public $lightbox = false;
    /**
     * Use this to have global available variables throughout scripts. For instance in hooks.
     *
     * @var array
     */
    public $global;
    /**
     * Sends a message to login form.
     * @var string
     */
    public $loginMessage;
    /**
     * Stores module methods.
     *
     * @var object
     */
    public $mod;
    /**
     * Content Distribution Network.
     * If you are running a very large site, you might want to consider running a dedicated light http server (httpdlight, nginx) that
     * only serves static content like images and static files, call it a CDN if you like.
     * By adding a host here 'http://192.34.22.33/project/cdn', all images etc, of PHPDevShell will be loaded from this address.
     * @var string
     */
    public $CDN;
    /**
     * Contains the best name of a controller being executed.
     * this->heading() will precede node name.
     *
     * @var string
     */
    public $heading;

    /**
     * Main template system constructor.
     */
    public function construct()
    {
        $configuration = $this->configuration;
        if (!empty($configuration['static_content_host'])) {
            $this->CDN = $configuration['static_content_host'];
        } else {
            $this->CDN = isset($configuration['absolute_url']) ? $configuration['absolute_url'] : '';
        }
    }

    /**
     * Will add any css path to the <head></head> tags of your document.
     *
     * @author jason <titan@phpdevshell.org>
     * @param string $cssRelativePath
     * @param string $media
     */
    public function addCssFileToHead($cssRelativePath = '', $media = '')
    {
        if (is_array($cssRelativePath)) {
            foreach ($cssRelativePath as $cssRelativePath_) {
                if (!empty($cssRelativePath_))
                    $this->modifyHead .= $this->mod->cssFileToHead($this->CDN . '/' . $cssRelativePath_, $media);
            }
        } else {
            if (!empty($cssRelativePath))
                $this->modifyHead .= $this->mod->cssFileToHead($this->CDN . '/' . $cssRelativePath, $media);
        }
    }

    /**
     * Will add any js path to the <head></head> tags of your document.
     *
     * @param string $jsRelativePath
     */
    public function addJsFileToHead($jsRelativePath = '')
    {
        if (is_array($jsRelativePath)) {
            foreach ($jsRelativePath as $jsRelativePath_) {
                if (!empty($jsRelativePath_))
                    $this->modifyHead .= $this->mod->jsFileToHead($this->CDN . '/' . $jsRelativePath_);
            }
        } else {
            if (!empty($jsRelativePath))
                $this->modifyHead .= $this->mod->jsFileToHead($this->CDN . '/' . $jsRelativePath);
        }
    }

    /**
     * Will add any content to the <head></head> tags of your document.
     *
     * @param string $giveHead
     */
    public function addToHead($giveHead = '')
    {
        $this->modifyHead .= $this->mod->addToHead($giveHead);
    }

    /**
     * Will add any js to the <head></head> tags of your document adding script tags.
     *
     * @param string $js
     */
    public function addJsToHead($js = '')
    {
        $this->modifyHead .= $this->mod->addJsToHead($js);
    }

    /**
     * Will add any css to the <head></head> tags of your document adding script tags.
     *
     * @param string $css
     */
    public function addCSSToHead($css = '')
    {
        $this->modifyHead .= $this->mod->addCssToHead($css);
    }

    /**
     * Activate a GUI plugin, i.e. give the plugin the opportunity to do whatever is needed so be usable from the Javascript code
     *
     * @param string $plugin     the name of the plugin
     * @param mixed  $parameters (optional) parameters if the plugin have ones
     *
     * @return iPHPDS_activableGUI the plugin
     */
    public function activatePlugin($plugin, $parameters = null)
    {
        $parameters = func_get_args();
        $path       = $this->classFactory->classFolder($plugin);

        $plugin = $this->factory(array('classname' => $plugin, 'factor' => 'singleton'), $path);
        if (is_a($plugin, 'iPHPDS_activableGUI')) {
            $plugin->activate($parameters);
        }

        return $plugin;
    }

    /**
     * Changes head output.
     * @param boolean $return
     * @return string
     */
    public function outputHead($return = false)
    {
        if (!empty($this->configuration['custom_css'])) {
            $this->addCssFileToHead($this->configuration['custom_css']);
        }

        // Check if we should return or print.
        if ($return == false) {
            // Simply output charset.
            print $this->modifyHead;
        } else {
            return $this->modifyHead;
        }
    }

    /**
     * Outputs current language identifier being used.
     *
     * @author Jason Schoeman
     */
    public function outputLanguage($return = false)
    {
        // Check if we should return or print.
        if ($return == false) {
            // Simply output charset.
            print $this->configuration['language'];
        } else {
            return $this->configuration['language'];
        }
    }

    /**
     * Add elegant loading to controllers.
     * @param boolean $return
     * @return string
     */
    public function outputLoader($return = false)
    {
        // Check if we should return or print.
        if ($return == false) {
            // Simply output charset.
            print $this->mod->loader();
        } else {
            return $this->mod->loader();
        }
    }

    /**
     * Outputs charset.
     *
     * @author Jason Schoeman
     */
    public function outputCharset($return = false)
    {
        // Check if we should return or print.
        if ($return == false) {
            // Simply output charset.
            print $this->configuration['charset'];
        } else {
            return $this->configuration['charset'];
        }
    }

    /**
     * Outputs the active scripts title.
     *
     * @author Jason Schoeman
     */
    public function outputTitle()
    {
        // Check if output should be modified.
        if ($this->modifyOutputTitle == false) {
            $navigation = $this->navigation->navigation;
            if (isset($navigation[$this->configuration['m']]['node_name'])) {
                print $this->mod->title($navigation[$this->configuration['m']]['node_name'], $this->configuration['scripts_name_version']);
            } else {
                print $this->core->haltController['message'];
            }
        } else {
            print $this->modifyOutputTitle;
        }
    }

    /**
     * This returns/prints the skin for inside theme usage.
     *
     * @param mixed default is print, can be set true, print, return.
     * @return string Skin.
     * @author Jason Schoeman
     */
    public function outputSkin($return = 'print')
    {
        // Create HTML.
        $html = $this->configuration['skin'];

        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            print $html;
        } else if ($return === 'return' || $return == true) {
            return $html;
        }
    }

    /**
     * This returns/prints the absolute url for inside theme usage.
     *
     * @param mixed default is print, can be set true, print, return.
     * @return string Absolute url.
     * @author Jason Schoeman
     */
    public function outputAbsoluteURL($return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            print $this->CDN;
        } else if ($return === 'return' || $return == true) {
            return $this->CDN;
        }
    }

    /**
     * This returns/prints the home url for inside theme usage.
     *
     * @param mixed default is print, can be set true, print, return.
     * @return string Absolute url without CDN.
     * @author Jason Schoeman
     */
    public function outputHomeURL($return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            print $this->configuration['absolute_url'];
        } else if ($return === 'return' || $return == true) {
            return $this->configuration['absolute_url'];
        }
    }

    /**
     * This returns/prints the meta keywords for inside theme usage.
     *
     * @param mixed default is print, can be set true, print, return.
     * @return string Meta Keywords.
     * @author Jason Schoeman
     */
    public function outputMetaKeywords($return = 'print')
    {
        // Create HTML.
        $html = $this->configuration['meta_keywords'];

        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            print $html;
        } else if ($return === 'return' || $return == true) {
            return $html;
        }
    }

    /**
     * This returns/prints the meta description for inside theme usage.
     *
     * @param mixed default is print, can be set true, print, return.
     * @return string Meta Description.
     * @author Jason Schoeman
     */
    public function outputMetaDescription($return = 'print')
    {
        // Create HTML.
        $html = $this->configuration['meta_description'];

        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            print $html;
        } else if ($return === 'return' || $return == true) {
            return $html;
        }
    }

    /**
     * Gets the desired logo and displays it. This method will try its best to deliver a logo, whatever the case.
     *
     * @author Jason Schoeman
     */
    public function outputLogo()
    {
        $configuration = $this->configuration;

        if ($this->modifyOutputLogo == false) {
            if (!empty($configuration['custom_logo'])) {
                // Give him his custom logo.
                $logo = $this->mod->logo($this->CDN . '/', $this->CDN . '/' . $configuration['custom_logo'], $configuration['scripts_name_version'], $configuration['scripts_name_version']);
            } else {
                // Ok so we have no set logo, does the developer want a custom logo?
                if (!empty($this->db->pluginLogo)) {
                    // Ok lets get the logo that the user wishes to display.
                    $logo = $this->mod->logo($this->CDN . '/', "{$this->CDN}/plugins/{$this->db->pluginLogo}/images/logo.png", $configuration['scripts_name_version'], $configuration['scripts_name_version']);
                } else if (!empty($configuration['scripts_name_version'])) {
                    $logo = $this->mod->logoText($configuration['scripts_name_version']);
                } else {
                    // Oops we have no logo, so lets just default to the orginal PHPDevShell logo.
                    $logo = $this->mod->logo($this->CDN . '/', "{$this->CDN}/plugins/AdminTools/images/logo.png", $configuration['scripts_name_version'], $configuration['scripts_name_version']);
                }
            }
            // Ok return the logo.
            print $logo;
        } else {
            print $this->modifyOutputLogo;
        }
    }

    /**
     * Acquire script identification image or logo.
     *
     * @param string $node_link
     * @param string $active_plugin
     * @param string $alias
     * @param int    $is_parent
     */
    public function scriptLogo($node_link, $active_plugin, $alias = null, $is_parent = null)
    {
        // Find last occurance.
        $filename_from = strrchr($node_link, '/');
        if (empty($filename_from)) $filename_from = $node_link;
        // Set image name.
        $image_name = ltrim($this->core->rightTrim($filename_from, '.php'), '/');
        // Create image url.
        $img_url_alias            = !empty($alias) ? "plugins/$active_plugin/images/$alias.png" : '';
        $img_url                  = "plugins/$active_plugin/images/$image_name.png";
        $image_url_plugin_default = "plugins/$active_plugin/images/default.png";
        $image_url_root_default   = "plugins/$active_plugin/images/default-root.png";
        // Lets check if image exists, if not, we need to set it to use default.
        if ($img_url_alias && file_exists($img_url_alias)) {
            return $this->CDN . '/' . $img_url_alias;
        } elseif (file_exists($img_url)) {
            return $this->CDN . '/' . $img_url;
        } elseif (file_exists($image_url_plugin_default) && !$is_parent) {
            return $this->CDN . '/' . $image_url_plugin_default;
        } elseif (file_exists($image_url_root_default) && $is_parent) {
            return $this->CDN . '/' . $image_url_root_default;
        } elseif (!file_exists($image_url_root_default) && $is_parent) {
            return $this->CDN . '/plugins/AdminTools/images/default-root.png';
        } else {
            return $this->CDN . '/plugins/AdminTools/images/default.png';
        }
    }

    /**
     * Sets template time.
     *
     * @author Jason Schoeman
     * @date 20120306 (greg) replace double equal with triple equal
     */
    public function outputTime()
    {
        // Check if output should be modified.
        if ($this->modifyOutputTime === false) {
            // Output active info.
            print $this->mod->formatTimeDate($this->configuration['time']);
        } else {
            print $this->modifyOutputTime;
        }
    }

    /**
     * Sets template login link.
     *
     * @author Jason Schoeman
     */
    public function outputLoginLink()
    {
        $navigation    = $this->navigation;
        $configuration = $this->configuration;

        // Check if output should be modified.
        if ($this->modifyOutputLoginLink == false) {
            if ($this->user->isLoggedIn()) {
                $login_information = $this->mod->logOutInfo($navigation->buildURL($configuration['loginandout'], 'logout=1'), $configuration['user_display_name']);
            } else {
                $inoutpage         = isset($navigation->navigation[$configuration['loginandout']]) ?
                    $navigation->navigation[$configuration['loginandout']]['node_name'] : ___('Login');
                $login_information = $this->mod->logInInfo($navigation->buildURL($configuration['loginandout']), $inoutpage);
            }
            // Output active info.
            print $login_information;
        } else {
            print $this->modifyOutputLoginLink;
        }
    }

    /**
     * Sets template role.
     *
     * @author Jason Schoeman
     */
    public function outputRole()
    {
        // Check if output should be modified.
        if ($this->modifyOutputRole == false) {
            // Set active role.
            $active_role = '';
            if ($this->user->isLoggedIn())
                $active_role = $this->mod->role(___('Role'), $this->configuration['user_role_name']);
            // Output active info.
            print $active_role;
        } else {
            print $this->modifyOutputRole;
        }
    }

    /**
     * Sets template group.
     *
     * @author Jason Schoeman
     */
    public function outputGroup()
    {
        // Check if output should be modified.
        if ($this->modifyOutputGroup == false) {
            // Set active role.
            $active_group = '';
            if ($this->user->isLoggedIn())
                $active_group = $this->mod->group(___('Group'), $this->configuration['user_group_name']);
            // Output active info.
            print $active_group;
        } else {
            print $this->modifyOutputGroup;
        }
    }

    /**
     * This returns/prints an image of the current script running.
     *
     * @param boolean Default is false, if set true, the heading will return instead of print.
     * @return string Returns image tag with image url.
     * @author Jason Schoeman
     */
    public function outputScriptIcon($return = false)
    {
        $navigation = $this->navigation->navigation;
        // Create script logo ////////////////////////////////////////////////////////////////////////////
        if (!empty($navigation[$this->configuration['m']]['node_id'])) {
            $script_logo_url = $this->scriptLogo($navigation[$this->configuration['m']]['node_link'], $navigation[$this->configuration['m']]['plugin']);
            //////////////////////////////////////////////////////////////////////////////////////////////////
            $node_name = $navigation[$this->configuration['m']]['node_name'];
            // Create HTML.
            $html = $this->mod->scriptIcon($script_logo_url, $node_name);
            // Return or print to browser.
            if ($return == false) {
                print $html;
            } else if ($return == true) {
                return $html;
            }
        } else {
            return false;
        }
    }

    /**
     * Returns "subnav" to the template system. Intended to be used by the engine.
     *
     * @author Jason Schoeman
     */
    public function outputSubnav()
    {
        // Check if output should be modified.
        if ($this->modifyOutputSubnav == false) {
            print $this->navigation->createSubnav();
        } else {
            print $this->modifyOutputSubnav;
        }
    }

    /**
     * Returns "nodes" to the template system. Intended to be used by the engine.
     *
     * @author Jason Schoeman
     */
    public function outputMenu()
    {
        // Check if output should be modified.
        if ($this->modifyOutputMenu == false) {
            print $this->navigation->createMenuStructure();
        } else {
            print $this->modifyOutputMenu;
        }
    }

    /**
     * Returns "output script" to the template system. Intended to be used by the engine.
     *
     * @author Jason Schoeman
     */
    public function outputScript()
    {
        $this->outputController();
    }

    /**
     * Returns "output script" to the template system. Intended to be used by the engine.
     *
     * @author Jason Schoeman
     */
    public function outputController()
    {
        if ($this->modifyOutputController == false) {
            print $this->core->data;
        } else {
            print $this->modifyOutputController;
        }
    }

    /**
     * Outputs the active scripts title/heading.
     *
     * @author Jason Schoeman
     */
    public function outputName()
    {
        // Check if output should be modified.
        if ($this->modifyOutputTitle == false) {
            $navigation = $this->navigation->navigation;
            if (!empty($this->heading)) {
                print $this->mod->activeName($this->heading);
            } else if (isset($navigation[$this->configuration['m']]['node_name'])) {
                print $this->mod->activeName($navigation[$this->configuration['m']]['node_name']);
            } else {
                print $this->mod->activeName($this->core->haltController['message']);
            }
        } else {
            print $this->modifyOutputTitle;
        }
    }

    /**
     * Allows to assign different names for the active controller.
     *
     * @version 1.0
     *
     * @param string This is the message that will be displayed as the controller name.
     * @return nothing
     * @author  Jason Schoeman
     */
    public function heading($heading)
    {
        $this->heading = $heading;
    }

    /**
     * This returns/prints a heading discription of the script being executed. Intended to be used by the developer.
     *
     * @version 2.0
     *
     * @date 20110309 (v1.1) (greg) changed to use the pieces repository
     * @date 20110309 (v1.2) (jason) good idea but it wont work as heading is not mandatory in controllers.
     * @date 20121212 (v1.3) (jason) rewrote how the heading works, needs to be present in theme if heading is required to be output in theme.
     *
     * @param string This is the message that will be displayed as the heading.
     * @return nothing
     * @author  Jason Schoeman
     */
    public function outputHeading()
    {
        if ($this->modifyOutputHeading == false) {
            $navigation = $this->navigation->navigation;

            if (!empty($this->title)) {
                print $this->mod->heading($this->title);
            } else if (isset($navigation[$this->configuration['m']]['node_name'])) {
                print $this->mod->activeName($navigation[$this->configuration['m']]['node_name']);
            }
        } else {
            print $this->modifyOutputHeading;
        }
    }

    /**
     * Sets template system logo or name.
     *
     * @author Jason Schoeman
     */
    public function outputTextLogo()
    {
        // Check if output should be modified.
        if ($this->modifyOutputTextLogo == false) {
            // Output active info.
            print $this->configuration['scripts_name_version'];
        } else {
            print $this->modifyOutputTextLogo;
        }
    }

    /**
     * Returns the last footer string to the template system. Intended to be used by the engine.
     *
     * @author Jason Schoeman
     */
    public function outputFooter()
    {
        // Check if output should be modified.
        if ($this->modifyOutputFooter == false) {
            print $this->configuration['footer_notes'];
        } else {
            print $this->modifyOutputFooter;
        }
    }

    /**
     * Will add code from configuration to theme closing body tag.
     *
     * @author Jason Schoeman
     */
    public function outputFooterJS()
    {
        print $this->configuration['footer_js'];
    }

    /**
     * This method is used to load a widget at into a certain location of your page.
     *
     * @author Jason Schoeman
     * @since  V 3.0.5
     */
    public function requestWidget($node_id_to_load, $element_id, $extend_url = '', $settings = '')
    {
        if (!empty($this->navigation->navigation["$node_id_to_load"])) {

            $widget_url = $this->navigation->buildURL($node_id_to_load, $extend_url, true);
            $text       = sprintf(___('Busy Loading <strong>%s</strong>...'), $this->navigation->navigation["$node_id_to_load"]['node_name']);

            // Widget ajax code...
            $JS = $this->mod->widget($widget_url, $element_id, $text, $settings);

            $this->addJsToHead($JS);

            return true;
        } else {
            return false;
        }
    }

    /**
     * This method is used to load ajax into a certain location of your page.
     *
     * @author Jason Schoeman
     * @since  V 3.0.5
     */
    public function requestAjax($node_id_to_load, $element_id, $extend_url = '', $settings = '')
    {
        if (!empty($this->navigation->navigation["$node_id_to_load"])) {

            $ajax_url = $this->navigation->buildURL($node_id_to_load, $extend_url, true);
            $text     = sprintf(___('Busy Loading <strong>%s</strong>...'), $this->navigation->navigation["$node_id_to_load"]['node_name']);

            // Ajax code...
            $JS = $this->mod->ajax($ajax_url, $element_id, $text, $settings);

            $this->addJsToHead($JS);

            return true;
        } else {
            return false;
        }
    }

    /**
     * This method is used to load a lightbox page.
     *
     * @author Jason Schoeman
     * @since  V 3.0.5
     */
    public function requestLightbox($node_id_to_load, $element_id, $extend_url = '', $settings = '')
    {
        if (!empty($this->navigation->navigation["$node_id_to_load"])) {

            $this->lightbox = true;

            $this->addJsFileToHead($this->mod->lightBoxScript());
            $this->addCssFileToHead($this->mod->lightBoxCss());

            $lightbox_url = $this->navigation->buildURL($node_id_to_load, $extend_url, true);

            // Jquery code...
            $JS = $this->mod->lightBox($element_id, $settings = '');

            $this->addJsToHead($JS);

            return $lightbox_url;
        } else {
            return false;
        }
    }

    /**
     * Pushes javascript to <head> for styling purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function styleButtons()
    {
        $this->addJSToHead($this->mod->styleButtons());
    }

    /**
     * Pushes javascript to <head> for validationg purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function validateForms()
    {
        $this->addJsFileToHead($this->mod->formsValidateJs());
        $this->addJSToHead($this->mod->formsValidate());
    }

    /**
     * Pushes javascript to <head> for styling purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function styleForms()
    {
        $this->addJSToHead($this->mod->styleForms());
    }

    /**
     * Pushes javascript to <head> for styling purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function styleFloatHeaders()
    {
        $this->addJsFileToHead($this->mod->styleFloatHeadersScript());
        $this->addJSToHead($this->mod->styleFloatHeaders());
    }

    /**
     * Pushes javascript to <head> for styling purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function styleTables()
    {
        $this->addJSToHead($this->mod->styleTables());
    }

    /**
     * Pushes javascript to <head> for styling purposes.
     *
     * @return nothing
     * @author Jason Schoeman
     */
    public function stylePagination()
    {
        $this->addJSToHead($this->mod->stylePagination());
    }

    /**
     * Pushes javascript to <select> for styling purposes.
     *
     * @return nothing
     * @author Don Schoeman
     */
    public function styleSelect()
    {
        $this->addJsFileToHead($this->mod->styleSelectJs());
        $this->addJsToHead($this->mod->styleSelectHeader());

    }

    /**
     * Calls a single jquery-ui effect plugin and includes it inside head.
     *
     * @version 1.1
     *
     * @date 20120606 (v1.1) (greg) added support for multiple times
     *
     * @param string Plugin name (multiple times)
     * @return nothing
     * @author  Jason Schoeman
     */
    public function jqueryEffect($plugin)
    {
        foreach (func_get_args() as $plugin) {
            $this->addJsFileToHead($this->mod->jqueryEffect($plugin));
        }
    }

    /**
     * Calls a single jquery-ui plugin and includes it inside head.
     *
     * @version 1.1
     *
     * @date 20120606 (v1.1) (greg) added support for multiple times
     *
     * @param string Plugin name (multiple times)
     * @return nothing
     * @author  Jason Schoeman
     */
    public function jqueryUI($plugin)
    {
        foreach (func_get_args() as $plugin) {
            $this->addJsFileToHead($this->mod->jqueryUI($plugin));
        }
    }

    /**
     * Ability to call and display notifications pushed to the notification system.
     *
     * @author  greg <greg@phpdevshell.org>
     * @version 1.1
     * @since   v3.0.5
     *
     * @date 20121210 (v1.2) (jason) added text support for clean messages
     * @date 20120308 (v1.1) (greg) added html and mod support
     * @date 20110706 (v1.0) (greg) added
     */
    public function outputNotifications()
    {
        $notifications = $this->notif->fetch();
        $mod           = $this->mod;

        if (!empty($notifications)) {
            $this->addJsFileToHead($mod->notificationsJs());
            foreach ($notifications as $notification) {
                if (is_array($notification)) {
                    switch ($notification[0]) {
                        case 'info':
                            $title = ___('Info');
                            break;
                        case 'warning':
                            $title = ___('Warning');
                            break;
                        case 'ok':
                            $title = ___('Ok');
                            break;
                        case 'critical':
                            $title = ___('Critical');
                            break;
                        case 'notice':
                            $title = ___('Notice');
                            break;
                        case 'busy':
                            $title = ___('Busy');
                            break;
                        case 'message':
                            $title = ___('Message');
                            break;
                        case 'note':
                            $title = ___('Note');
                            break;
                        default:
                            $title = ___('Info');
                            break;
                    }
                    $this->addJsToHead($mod->notifications($title, $notification[1], $notification[0]));
                } else {
                    $this->addJsToHead($mod->notifications(___('Info'), $notification));
                }
            }
        }
    }

    /**
     * This method will load given png icon from icon database,
     *
     * @param string  Icon name without extention.
     * @param Title   of given image.
     * @param int     The size folder to look within.
     * @param string  If an alternative class must be added to image.
     * @param string  File type.
     * @param boolean Default is false, if set true, the heading will return instead of print.
     */
    public function icon($name, $title = false, $size = 16, $class = 'class', $type = '.png', $return = true)
    {
        $navigation = $this->navigation->navigation;
        // Create icon dir.
        $script_url = $this->CDN . '/themes/' . $navigation[$this->configuration['m']]['template_folder'] . '/images/icons-' . $size . '/' . $name . $type;
        if (empty ($title))
            $title = '';
        // Create HTML.
        $html = $this->mod->icon($script_url, $class, $title);

        // Return or print to browser.
        if ($return == false) {
            print $html;
        } else if ($return == true) {
            return $html;
        }
    }

    /**
     * This returns/prints info of the script being executed. Intended to be used by the developer.
     *
     * @version 1.3
     *
     * @date 20110309 (v1.1) (greg) changed to use the pieces repository
     * @date 20110309 (v1.2) (jason) good idea but it wont work as info is not mandatory in controllers.
     * @date 20120308 (v1.3) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed as the info.
     * @return nothing
     * @author  Jason Schoeman
     */
    public function info($information, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('info', $information));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->info($information);
        }
    }

    /**
     * This returns/prints a warning message regarding the active script. Intended to be used by the developer.
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @param mixed  default is log, can be set true, print, return.
     * @return string Warning string.
     * @author Jason Schoeman
     */
    public function warning($warning, $return = 'print', $log = 'log')
    {
        if ($log === true || $log == 'log') {
            // Log types are : ////////////////
            // 1 = OK /////////////////////////
            // 2 = Warning ////////////////////
            // 3 = Critical ///////////////////
            // 4 = Log-in /////////////////////
            // 5 = Log-out ////////////////////
            ///////////////////////////////////
            $log_type = 2; ////////////////////
            // Log the event //////////////////
            $this->db->logArray[] = array('log_type' => $log_type, 'log_description' => $warning);
        }
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('warning', $warning));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->warning($warning);
        }
    }

    /**
     * This returns/prints a ok message regarding the active script. Intended to be used by the developer.
     *
     * @version 1.1
     *
     * @date 20120308 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @param mixed  default is log, can be set true, print, return.
     * @return string Ok string.
     * @author  Jason Schoeman
     */
    public function ok($ok, $return = 'print', $log = 'log')
    {
        if ($log === true || $log == 'log') {
            // Log types are : ////////////////
            // 1 = OK /////////////////////////
            // 2 = Warning ////////////////////
            // 3 = Critical ///////////////////
            // 4 = Log-in /////////////////////
            // 5 = Log-out ////////////////////
            ///////////////////////////////////
            $log_type = 1; ////////////////////
            // Log the event //////////////////
            $this->db->logArray[] = array('log_type' => $log_type, 'log_description' => $ok);
        }
        // Create HTML.

        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('ok', $ok));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->ok($ok);
        }
    }

    /**
     * This returns/prints a error message regarding the active script. Intended to be used by the developer where exceptions are caught.
     *
     * @version 1.1
     * @date 20120308 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @param mixed  default is log, can be set true, print, return.
     * @return string Error string.
     * @author  Jason Schoeman
     */
    public function error($error, $return = 'print', $log = 'log')
    {
        if ($log === true || $log == 'log') {
            // Log types are : ////////////////
            // 1 = OK /////////////////////////
            // 2 = Warning ////////////////////
            // 3 = Critical ///////////////////
            // 4 = Log-in /////////////////////
            // 5 = Log-out ////////////////////
            // 6 = Error //////////////////////
            ///////////////////////////////////
            $log_type = 6; ////////////////////
            // Log the event //////////////////
            $this->db->logArray[] = array('log_type' => $log_type, 'log_description' => $error);
        }
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('error', $error));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->error($error);
        }
    }

    /**
     * This returns/prints a critical message regarding the active script. Intended to be used by the developer.
     *
     * @version 1.1
     * @date 20120308 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @param mixed  default is log, can be set true, print, return.
     * @return string Critical string.
     * @author  Jason Schoeman
     */
    public function critical($critical, $return = 'print', $log = 'log', $mail = 'mailadmin')
    {
        $navigation = $this->navigation->navigation;
        if ($log === true || $log == 'log') {
            // Log types are : ////////////////
            // 1 = OK /////////////////////////
            // 2 = Warning ////////////////////
            // 3 = Critical ///////////////////
            // 4 = Log-in /////////////////////
            // 5 = Log-out ////////////////////
            ///////////////////////////////////
            $log_type = 3; ////////////////////
            // Log the event //////////////////
            $this->db->logArray[] = array('log_type' => $log_type, 'log_description' => $critical);
        }
        // Check if we need to email admin.
        if ($this->configuration['email_critical']) {
            // Subject.
            $subject = sprintf(___("CRITICAL ERROR NOTIFICATION %s"), $this->configuration['scripts_name_version']);
            // Message.
            $broke_script = $navigation[$this->configuration['m']]['node_name'];
            $broken_url   = $this->configuration['absolute_url'] . '/index.php?m=' . $this->configuration['m'];
            $message      = sprintf(___("Admin,")) . "\r\n\r\n";
            $message .= sprintf(___("THERE WAS A CRITICAL ERROR IN %s:"), $this->configuration['scripts_name_version']) . "\r\n\r\n" . $critical . "\r\n\r\n";
            $message .= sprintf(___("Click on url to access broken script called %s:"), $broke_script) . "\r\n" . $broken_url . "\r\n";
            $message .= sprintf(___("Script error occurred for user : %s"), $this->configuration['user_display_name']);

            if ($mail === true || $mail == 'mailadmin') {
                // Initiate email class.
                $email = $this->factory('mailer');
                // Ok we can now send the critical email message.
                $email->sendmail("{$this->configuration['setting_admin_email']}", $subject, $message);
            }
        }
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('critical', $critical));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->critical($critical);
        }
    }

    /**
     * This returns/prints a notice of the script being executed. Intended to be used by the developer.
     *
     * @version 1.1
     * @date 20120308 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @return string Notice string.
     * @author  Jason Schoeman
     */
    public function notice($notice, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('notice', $notice));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->notice($notice);
        }
    }

    /**
     * This returns/prints a busy of the script being executed. Intended to be used by the developer.
     *
     * @version 1.1
     * @date 20120308 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @return string Busy string.
     * @author  Jason Schoeman
     */
    public function busy($busy, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('busy', $busy));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->busy($busy);
        }
    }

    /**
     * This returns/prints a message of the script being executed. Intended to be used by the developer.
     *
     * @version 1.1
     * @date 20120312 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @return string Message string.
     * @author  Jason Schoeman
     */
    public function message($message, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('message', $message));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->message($message);
        }
    }

    /**
     * This returns/prints a note of the script being executed. Intended to be used by the developer.
     *
     * @version 1.1
     * @date 20120312 (v1.1) (greg) switched to notifications queue
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @return string Note string.
     * @author  Jason Schoeman
     */
    public function note($note, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            $this->notif->add(array('note', $note));
        } else if ($return === 'return' || $return == true) {
            return $this->mod->note($note);
        }
    }

    /**
     * This returns/prints a heading of the script being executed. Intended to be used by the developer.
     *
     * @param string This is the message that will be displayed.
     * @param mixed  default is print, can be set true, print, return.
     * @return string Heading string.
     * @author Jason Schoeman
     */
    public function scripthead($scripthead, $return = 'print')
    {
        // Return or print to browser.
        if ($return === 'print' || $return == false) {
            //print $html;
            $this->notif->add($scripthead);
        } else if ($return === 'return' || $return == true) {
            return $this->mod->scriptHead($scripthead);
        }
    }

    /**
     * This creates an the [i] when over with mouse a popup with a message appears, this can be placed anywhere.
     * Intended to be used by the developer.
     *
     * @param string  The message to diplay when mouse goes over the [i].
     * @param boolean Sets to print out confirm link instead of return.
     * @author Jason Schoeman
     */
    public function tip($text, $print = false)
    {
        // This is yet another IE Fix !
        $text_clean = preg_replace('/"/', '', $text);
        $info       = $this->mod->toolTip($text_clean);
        if ($print == false) {
            return $info;
        } else {
            print $info;
        }
    }

    /**
     * Login heading messages.
     *
     * @author Jason Schoeman
     */
    public function loginFormHeading($return = false)
    {
        $HTML    = '';
        $message = '';
        //if (! empty($this->loginMessage))
        //	$message = $this->notice(___($this->loginMessage), 'return');

        // Create headings for login.
        if (!empty($this->core->haltController)) {
            $HTML .= $this->heading(___('Authentication Required'), 'return');
        } else {
            // Get some default settings.
            $settings = $this->db->getSettings(array('login_message'));

            // Check if we have a login message to display.
            if (!empty($settings['login_message'])) {
                $login_message = $this->message(___($settings['login_message']), 'return');
            } else {
                $login_message = '';
            }

            $HTML .= $this->heading(___('Login'), 'return');
            $HTML .= $login_message;
            $HTML .= $message;
        }

        if ($return == false) {
            print $HTML;
        } else {
            return $HTML;
        }
    }

    /**
     * Executes the login.
     *
     * @author Jason Schoeman
     */
    public function loginForm($return = false)
    {
        $HTML = $this->factory('StandardLogin')->loginForm($return);

        if ($return == false) {
            print $HTML;
        } else {
            return $HTML;
        }
    }

    /**
     * Shows alternative logout information and preferences to existing logged in users.
     *
     * @author Jason Schoeman
     */
    public function outputLogin($return = false)
    {
        $configuration = $this->configuration;
        $nav           = $this->navigation;
        $mod           = $this->mod;
        $logouturl     = $nav->buildURL(null, 'logout=1');
        $logoutname    = ___('Log Out');

        if ($this->user->isLoggedIn()) {
            // Check if preferences page exists.
            $HTML = $mod->loggedInInfo(
                $configuration['user_display_name'],
                $logouturl, $logoutname,
                $mod->role(___('Role'), $configuration['user_role_name']),
                $mod->group(___('Group'), $configuration['user_group_name']),
                $nav->navigation);
        } else {
            $HTML = $this->factory('StandardLogin')->loginForm($return);
        }

        if ($return == false) {
            print $HTML;
        } else {
            return $HTML;
        }
    }

    /**
     * An alternative way to add more custom links to your page, these are direct links to existing node items.
     * Contains an array of available links.
     *
     * @author Jason Schoeman
     */
    public function outputAltNav($return = false)
    {
        $nav = $this->navigation->navigation;

        if ($return == false) {
            print $this->mod->altNav($nav);
        } else {
            return $this->mod->altNav($nav);
        }
    }

    /**
     * This provides a simple styled link depending if the user is logged in or not.
     *
     * @author Jason Schoeman
     */
    public function outputAltHome($return = false)
    {
        $home = $this->configuration[$this->user->isLoggedIn() ? 'front_page_id_in' : 'front_page_id'];
        $nav  = $this->navigation->navigation[$home];

        if ($return == false) {
            print $this->mod->altHome($nav);
        } else {
            return $this->mod->altHome($nav);
        }
    }

    /**
     * Get and return the supposed to run template.
     *
     * @return string if not found, return default.
     * @author Jason Schoeman
     */
    public function getTemplate()
    {
        $settings['default_template'] = '';

        // Check if the node has a defined template.
        if (!empty($this->navigation->navigation[$this->configuration['m']]['template_folder'])) {
            $settings['default_template'] = $this->navigation->navigation[$this->configuration['m']]['template_folder'];
        } else {
            // If not check if the gui system settings was set with a default template.
            $settings['default_template'] = $this->configuration['default_template'];
        }

        // Return the complete template.
        return $settings['default_template'];
    }

    /**
     * Gets the correct location of a tpl file, will return full path, can be a view.tpl or view.tpl.php files.
     *
     * @param string $load_view
     * @param string $plugin_override If another plugin is to be used in the directory.
     *
     * @return string
     */
    public function getTpl($load_view = '', $plugin_override = '')
    {
        return $this->core->getTpl($load_view, $plugin_override);
    }

    /**
     * Returns some debug info to the frontend, at the bottom of the page
     *
     */
    public function debugInfo()
    {
        if ($this->configuration['development']) {
            if (!empty($this->db->countQueries)) {
                $count_queries = $this->db->countQueries;
            } else {
                $count_queries = 0;
            }
            if ($this->configuration['queries_count']) {
                if (!empty($this->core->themeFile)) {
                    $memory_used = memory_get_peak_usage();
                    $time_spent  = intval((microtime(true) - $GLOBALS['start_time']) * 1000);
                    return $this->mod->debug($count_queries, number_format($memory_used / 1000000, 2, '.', ' '), $time_spent);
                }
            }
        }
    }

    /**
     * Prints some debug info to the frontend, at the bottom of the page
     *
     */
    public function outPutDebugInfo()
    {
        print $this->debugInfo();
    }

    /**
     * Convert all HTML entities to their applicable characters.
     *
     * @param string $string_to_decode
     * @return string
     */
    public function htmlEntityDecode($string_to_decode)
    {
        // Decode characters.
        return html_entity_decode($string_to_decode, ENT_QUOTES, $this->configuration['charset']);
    }

    /**
     * This creates a simple confirmation box to ask users input before performing a critical link click.
     *
     * @param string What is the question to be asked in the confirmation box.
     * @return string Javascript popup confirmation box.
     * @author Jason Schoeman
     */
    public function confirmLink($confirm_what)
    {
        $onclick = "onClick=\"return confirm('$confirm_what')\"";
        return eval('return $onclick;');
    }

    /**
     * This creates a simple confirmation box to ask users input before performing a critical submit.
     *
     * @param string What is the question to be asked in the confirmation box.
     * @return string Javascript popup confirmation box.
     * @author Jason Schoeman
     */
    public function confirmSubmit($confirm_what)
    {
        $onclick = "onSubmit=\"return confirm('$confirm_what')\"";
        return eval('return $onclick;');
    }

    /**
     * This shows a simple "alert" box which notifies the user about a specified condition.
     *
     * @param string The actual warning message.
     * @return string Javascript popup warning box.
     * @author Don Schoeman
     */
    public function alertSubmit($alert_msg)
    {
        $onclick = "onSubmit=\"alert('$alert_msg')\"";
        return eval('return $onclick;');
    }

    /**
     * This shows a simple "alert" box which notifies the user about a specified condition.
     *
     * @param string The actual warning message.
     * @return string Javascript popup warning box.
     * @author Don Schoeman
     */
    public function alertLink($alert_msg)
    {
        $onclick = "onClick=\"alert('$alert_msg')\"";
        return eval('return $onclick;');
    }
}

/**
 * Creates a language tooltip string and prints it out to the template.
 *
 * @param string $info_mark
 */
function tip($text)
{
    print _($text);
}

/**
 * Creates a language tooltip string inside a text domain and prints it out to the template.
 *
 * @param string $info_mark
 */
function dtip($text, $domain)
{
    print dgettext($domain, $text);
}
