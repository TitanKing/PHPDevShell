<?php

class PHPDS_navigation extends PHPDS_dependant
{
	const node_standard = 1;
	const node_plain_link = 2;
	const node_jumpto_link = 3;
	const node_external_file = 4;
	const node_external_link = 5;
	const node_placeholder = 6;
	const node_iframe = 7;
	const node_cron = 8;
	const node_widget = 9;
	const node_styled_ajax = 10;
	const node_lightbox = 11;
	const node_ajax_raw = 12;


	/**
	 * @var array
	 */
	protected $breadcrumbArray = null;
	/**
	 * @var array of arrays, for each menu which have children, an array of the children IDs
	 */
	public $child = null;
	/**
	 * Holds all menu item information.
	 *
	 * @var array
	 */
	public $navigation;
	/**
	 * Holds all menu item information.
	 *
	 * @var array
	 */
	public $navAlias;

	/**
	 * This methods loads the menu structure, this according to permission and conditions.
	 *
	 * @return string
	 * @author Jason Schoeman
	 */
	public function extractMenu ()
	{
		$db = $this->db;
		$all_user_roles = $this->user->getRoles($this->configuration['user_id']);
		if ($db->cacheEmpty('navigation')) {
			if (empty($this->navigation)) $this->navigation = array();
			if (empty($this->child)) $this->child = array();
			if (empty($this->navAlias)) $this->navAlias = array();
			$db->invokeQuery('NAVIGATION_extractMenuQuery', $all_user_roles);

			$db->cacheWrite('navigation', $this->navigation);
			$db->cacheWrite('child_navigation', $this->child);
			$db->cacheWrite('nav_alias', $this->navAlias);
		} else {
			$this->navigation = $db->cacheRead('navigation');
			$this->child = $db->cacheRead('child_navigation');
			$this->navAlias = $db->cacheRead('nav_alias');
		}
		return $this;
	}

	/**
	 * Determines what the menu item should be named.
	 *
	 * @param string $replacement_name
	 * @param string $menu_link
	 * @param int $menu_id
	 * @return string
	 */
	public function determineMenuName ($replacement_name = '', $menu_link = '', $menu_id = false, $plugin='')
	{
		if (! empty($replacement_name)) {
			return __("$replacement_name", "$plugin");
		} else {
			return $menu_link;
		}
	}

	/**
	 * Returns true if menu should show.
	 *
	 * @param integer $hide_type
	 * @param integer $menu_id
	 * @param integer $active_id
	 */
	public function showMenu ($hide_type, $menu_id = null, $active_id = null)
	{
		if (! empty($menu_id) && ($hide_type == 4) && $active_id == $menu_id) {
			return true;
		} else {
			if ($hide_type == 0 || $hide_type == 2) {
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Compiles menu items in order.
	 *
	 * @return string
	 * @author Jason Schoeman
	 */
	public function createMenuStructure ()
	{
		$menu = false;
		$configuration = $this->configuration;
		$nav = $this->navigation;
		$mod = $this->template->mod;

		if (! empty($nav)) {
			// Start the main loop, the main loop handles the top level menus.
			// When child menus are found the callFamily function is used to render those menus. The callFamily function may or may not go recursive at that point.
			foreach ($nav as $m) {
				if ($this->showMenu($m['hide'], $m['menu_id'], $configuration['m']) && ((string) $nav[$m['menu_id']]['parent_menu_id'] == '0')) {
					($m['menu_id'] == $configuration['m']) ? $url_active = 'active' : $url_active = 'inactive';
					if ($m['is_parent'] == 1) {
						$call_family = $this->callFamily($m['menu_id']);
						if (! empty($call_family)) {
							$call_family = $mod->menuUlParent($call_family);
							$p_type = 'grand-parent';
						} else {
							$p_type = $url_active;
						}
						$menu .= $mod->menuLiParent($call_family, $mod->menuA($m, 'nav-grand'), $p_type, $m);
					} else {
						$menu .= $mod->menuLiChild($mod->menuA($m, 'first-child'), $url_active, $m);
					}
				}
			}
			if (empty($menu)) {
				$menu = $mod->menuLiChild($mod->menuA($nav[$configuration['m']]), 'active');
			}
		}
		return $menu;
	}

	/**
	 * Assists write_menu in calling menu children.
	 *
	 * @param int $menu_id
	 */
	public function callFamily ($menu_id = false)
	{
		$menu = '';
		$configuration = $this->configuration;
		$nav = $this->navigation;
		$mod = $this->template->mod;

		if (! empty($this->child[$menu_id])) {
			$child = $this->child[$menu_id];
			foreach ($child as $m) {
				if ($this->showMenu($nav[$m]['hide'], $m, $configuration['m'])) {
					($m == $configuration['m']) ? $url_active = 'active' : $url_active = 'inactive';
					if ($nav[$m]['is_parent'] == 1) {
						$call_family = $this->callFamily($m);
						if (! empty($call_family)) {
							$call_family = $mod->menuUlChild($call_family);
							$p_type = 'parent';
						} else {
							$p_type = $url_active;
						}
						$menu .= $mod->subMenuLiParent($call_family, $mod->menuA($nav[$m], 'nav-parent'), $p_type, $nav[$m]);
					} else {
						$menu .= $mod->subMenuLiChild($mod->menuA($nav[$m], 'child'), $url_active, $nav[$m]);
					}
				}
			}
		}
		return $menu;
	}

	/**
	 * This method compiles the history tree seen, this is the tree that the user sees expand when going deeper into menu levels.
	 * On the default template this is the navigation link string top left above the menus.
	 *
	 * @return string
	 */
	public function createSubnav ()
	{
		$menu = '';
		$configuration = $this->configuration;
		$nav = $this->navigation;
		$mod = $this->template->mod;

		if (empty($nav[$configuration['m']]['is_parent'])) {
			$parentid = (! empty($nav[$configuration['m']]['parent_menu_id'])) ? $nav[$configuration['m']]['parent_menu_id'] : '0';
		} else {
			$parentid = $configuration['m'];
		}

		if (! empty($this->child[$parentid])) {
			$child = $this->child[$parentid];
			foreach ($child as $m) {
				if ($this->showMenu($nav[$m]['hide'], $m, $configuration['m'])) {
					($m == $configuration['m']) ? $url_active = 'active' : $url_active = 'inactive';
					$menu .= $mod->subNavMenuLi($mod->menuASubNav($nav[$m]), $url_active, $nav[$m]);
				}
			}
		}

		return $menu;
	}

	/**
	 * Method assists method generate_history_tree in getting breadcrumb links.
	 *
	 * @param integer
	 * @return array
	 */
	private function callbackParentItem ($menu_id_)
	{
		$nav = $this->navigation;
		if (! empty($nav[$menu_id_]['parent_menu_id'])) {
			$recall_parent_menu_id = $nav[$menu_id_]['parent_menu_id'];
		} else {
			$recall_parent_menu_id = '0';
		}
		$this->breadcrumbArray[] = $menu_id_;
		if ($recall_parent_menu_id) {
			$this->callbackParentItem($recall_parent_menu_id);
		}
	}

	/**
	 * Simply returns current menu id.
	 *
	 * @return int
	 */
	public function currentMenuID()
	{
		return $this->configuration['m'];
	}

	/**
	 * Returns the complete current menu structure
	 *
	 * @version 1.0
	 * @author greg <greg@phpdevshell.org>
	 * @date 20120608 (1.0) (greg) added
	 *
	 * @return array
	 */
	public function currentMenu()
	{
		return $this->navigation[$this->currentMenuID()];
	}

	/**
	 * Will try and locate the full path of a filename of a given menu id, if it is a link, the original filename will be returned.
	 *
	 * @param int $menu_id
	 * @param string $plugin
	 * @return string
	 */
	public function menuFile ($menu_id=false, $plugin=false)
	{
		if (empty($menu_id)) $menu_id = $this->configuration['m'];
		$absolute_path = $this->configuration['absolute_path'];
		list($plugin, $menu_link) = $this->menuPath($menu_id, $plugin);
		if (file_exists($absolute_path . 'plugins/' . $plugin . '/controllers/' . $menu_link)) {
			return $absolute_path . 'plugins/' . $plugin . '/controllers/' . $menu_link;
		} else if (file_exists($absolute_path . 'plugins/' . $plugin . '/' . $menu_link)) {
			return $absolute_path . 'plugins/' . $plugin . '/' . $menu_link;
		} else {
			return false;
		}
	}

	/**
	 * Will locate the menus item full path.
	 *
	 * @param int $menu_id
	 * @param string $plugin
	 * @return array
	 */
	public function menuPath ($menu_id=false, $plugin=false)
	{
		$configuration = $this->configuration;
		$navigation = $this->navigation;
		if (empty($configuration['m']))
			$configuration['m'] = 0;
		if (empty($menu_id)) $menu_id =  $configuration['m'];
		if (empty($navigation[$menu_id]['extend'])) {
			if (!empty($navigation[$menu_id])) {
				$menu_link = $navigation[$menu_id]['menu_link'];
				if (empty($plugin))
					$plugin = $navigation[$menu_id]['plugin'];
			}
		} else {
			$extend = $navigation[$menu_id]['extend'];
			$menu_link = $navigation[$extend]['menu_link'];
			if (empty($plugin))
				$plugin = $navigation[$extend]['plugin'];
		}
		if (empty($plugin))
			$plugin = 'AdminTools';
		if (empty($menu_link))
			$menu_link = '';
		return array($plugin, $menu_link);
	}

	/**
	 * Will return the url for a certain menu item when path is provided.
	 * @param string $item_path The string to the path of the menu item, 'user/control-panel.php'
	 * @param string $plugin_name The plugin name to look for it under, if empty, active plugin will be used.
	 * @param string $extend_url Will extend url with some get values.
	 * @return string Will return complete and cleaned sef url if available else normal url will be returned.
	 */
	public function buildURLFromPath ($item_path, $plugin_name = '', $extend_url = '')
	{
		if (empty($plugin_name))
			$plugin_name = $this->core->activePlugin();
		$lookup = array('plugin'=>$plugin_name, 'menu_link'=>$item_path);
		$menu_id = PU_ArraySearch($lookup, $this->navigation);
		if (! empty($menu_id)) {
			return $this->buildURL($menu_id, $extend_url);
		} else {
			return $this->pageNotFound();
		}
	}

	/**
	 * Returns the correct string for use in href when creating a link for a menu id. Will return sef url if possible.
	 * Will return self url when no menu id is given. No starting & or ? is needed, this gets auto determined!
	 * If left empty it will return current active menu.
	 *
	 * @param mixed The menu id or menu file location to create a url from.
	 * @param string extend_url
	 * @param boolean strip_trail Will strip unwanted empty operators at the end.
	 * @return string
	 * @author Jason Schoeman
	 */
	public function buildURL ($menu_id = null, $extend_url = '', $strip_trail = true)
	{
		if (empty($menu_id)) $menu_id = $this->configuration['m'];
		if (! empty($this->configuration['sef_url'])) {
			if (empty($this->navigation["$menu_id"]['alias'])) {
				$alias = $this->db->invokeQuery('NAVIGATION_findAliasQuery', $menu_id);
			} else {
				$alias = $this->navigation["$menu_id"]['alias'];
			}
			if (! empty($extend_url)) {
				$extend_url = "?$extend_url";
			} else if ($strip_trail) {
				$extend_url = '';
			} else {
				$extend_url = '?';
			}
			$url_append = empty($this->configuration['url_append']) ? '' : $this->configuration['url_append'];
			$url = $alias . $url_append . "$extend_url";
		} else {
			if (! empty($extend_url)) {
				$extend_url = "&$extend_url";
			} else {
				$extend_url = '';
			}
			$url = 'index.php?m=' . "$menu_id" . "$extend_url";
		}
		if (! empty($url)) {
			return $this->configuration['absolute_url'] . "/$url";
		} else {
			return false;
		}
	}

	/**
	 * Parses the REQUEST_URI to get the page id
	 *
	 * @version 1.1
	 *
	 * @date 20120312 (v1.1) (greg) added support for given parameter
	 * @date 20101007 (v1.0.2) (greg) moved from PHPDS to PHPDS_navigation ; little cleanup
	 * @date 20100109
	 *
	 * @author Ross Kuyper
	 */
	public function parseRequestString($uri = '')
	{
		if (empty($uri) && !empty($_SERVER['REQUEST_URI'])) {
			$uri = $_SERVER['REQUEST_URI'];
		}

		$configuration = $this->configuration;

		if ($this->user->isLoggedIn()) {
			$configuration['m'] = $configuration['front_page_id_in'];
		} else {
			$configuration['m'] = $configuration['front_page_id'];
		}
		if (! empty($_GET['m'])) {
			$configuration['m'] = $_GET['m'];
			$get_menu_id = $_GET['m'];
		} else {
			$get_menu_id = null;
		}
		if(! empty($configuration['sef_url'])) {

			if(! empty($uri)) {

				//list($req) = explode('?', $uri);

				$basepath = parse_url($configuration['absolute_url'], PHP_URL_PATH);
				$req = parse_url($uri, PHP_URL_PATH);

				$req = trim(str_replace($basepath, '', $req), '/');

				$uriarray = explode('/', $req);

				$alias = array_shift($uriarray);

				foreach ($uriarray as $get) {
					if (empty($key)) {
						$key = $get;
					} else {
						$getarray[$key] = $get;
						$key = '';
					}
				}

				if (! empty($getarray)) {
					if(! empty($_GET)) $_GET = array_merge($getarray, $_GET);
					else $_GET = $getarray;
				}

				if (! empty($alias) && ($alias != 'index.php')) {
					if (! empty($configuration['url_append']) ) $alias = str_replace($configuration['url_append'], '', $alias);

					if (isset($this->navAlias[$alias])) {
						$configuration['m'] = $this->navAlias[$alias];
						return true;
					} else {
						return $this->urlAccessError($alias, $get_menu_id);
					}
				} else {
					// This is used when sef url is on but normal url is used.
					if (! empty($get_menu_id)) {
						if (empty($this->navigation["$get_menu_id"])) {
							return $this->urlAccessError(null, $get_menu_id);
						} else {
							return true;
						}
					} else {
						if (empty($this->navigation["{$configuration['m']}"])) {
							return $this->urlAccessError(null, $configuration['m']);
						} else {
							return true;
						}
					}
				}
			}
		} else {
			if (! empty($get_menu_id)) {
				if (empty($this->navigation["$get_menu_id"])) {
					return $this->urlAccessError(null, $get_menu_id);
				} else {
					return true;
				}
			} else {
				if (empty($this->navigation["{$configuration['m']}"])) {
					return $this->urlAccessError(null, $configuration['m']);
				} else {
					return true;
				}
			}
		}
	}

	/**
	 * Checks url access error type and sets it.
	 *
	 * @param string
	 * @param string
	 * @author Jason Schoeman
	 */
	public function urlAccessError ($alias = null, $get_menu_id = null)
	{
		$required_menu_id = $this->db->invokeQuery('NAVIGATION_findMenuQuery', $alias, $get_menu_id);

		if (empty($required_menu_id)) {
			$this->core->haltController = array('type'=>'404','message'=>___('Page not found'));
			return false;
		} else {
			if ($this->user->isLoggedIn()) {
				$this->core->haltController = array('type'=>'403','message'=>___('Page found, but you don\'t have the required permission to access this page.'));
				return false;
			} else {
				$this->core->haltController = array('type'=>'auth','message'=>___('Authentication Required'));
				$this->configuration['m'] = $required_menu_id;
				return false;
			}
		}
	}

	/**
	 * This function support output_script by looking deeper into menu structure to find last linked menu item that is not linked to another.
	 *
	 * @param integer $extendedMenuId
	 * @return integer
	 */
	public function extendMenuLoop ($extended_menu_id)
	{
		$navigation = $this->navigation;

		// Assign extention value.
		$extend_more = $navigation[$extended_menu_id]['extend'];
		// Check if we should look higher up for a working menu id and prevent endless looping.
		if (! empty($extend_more) && ($extended_menu_id != $navigation[$extend_more]['extend'])) {
			$this->extendMenuLoop($extend_more);
		} else {
			// Final check, to see if we had an endless loop that still has an extention.
			if (! empty($navigation[$extended_menu_id]['extend'])) {
				if (! empty($navigation[$extended_menu_id]['parent_menu_id'])) {
					// Lets look even higher up now that we jumped the endless loop.
					$this->extendMenuLoop($navigation[$extended_menu_id]['parent_menu_id']);
				} else {
					// We now have no other choice but to show default home page.
					return '0';
				}
			} else {
				return $extended_menu_id;
			}
		}
	}

	/**
	 * This method saves the current URL with the option to add more $this->security->get variables like ("&variable1=1&variable2=2")
	 * This is mostly used for when additional $this->security->get variables are required! Usefull when using forms.
	 *
	 * @param string Add more $this->security->get variables like ("&variable1=1&variable2=2")
	 * @return string
	 * @author Jason Schoeman
	 */
	public function selfUrl ($extra_get_variables = '')
	{
		return $this->buildURL(false, $extra_get_variables, true);
	}

	/**
	 * Will convert any given plugin script location to its correct url.
	 *
	 * @param $file_path The full file path, "DummyPlugin/sample/sample1.php"
	 * @param $extend_url Should the url be extended with $_GET vars, 'e=12'
	 * @param $strip_trail Will strip unwanted empty operators at the end.
	 * @return string
	 */
	public function purl ($file_path, $extend_url = '', $strip_trail = true)
	{
		$menu_id = $this->createMenuId($file_path);
		return $this->buildURL($menu_id, $extend_url, $strip_trail);
	}

	/**
	 * Simply converts a url to a clean SEF url if SEF is enabled.
	 *
	 * @param int $menu_id
	 * @param string $extend_url 'test1=foo1&test2=foo2&test3=foobar'
	 * @param boolean $strip_trail should extending ? be removed.
	 *
	 * @return string
	 */
	public function sefURL ($menu_id = null, $extend_url = '', $strip_trail = true)
	{
		$url = $this->buildURL($menu_id, $extend_url, $strip_trail);

		if (! empty($this->configuration['sef_url'])) {
			return preg_replace(array('/\?/', '/\&/', '/\=/'), '/', $url);
		} else {
			return $url;
		}
	}

	/**
	 * Convert plugin file location to unsigned CRC32 value. This is unique and allows one to locate a menu item from location as well.
	 *
	 * @param string The plugin folder the file is in.
	 * @return integer
	 * @author Jason Schoeman
	 */
	public function createMenuId ($path)
	{
		return sprintf('%u', crc32(str_ireplace('/', '', $path)));
	}

	/**
	 * Redirects to new url.
	 *
	 * @param string URL to redirect to.
	 * @param integer Time in seconds before redirecting.
	 * @author Jason Schoeman
	 */
	public function redirect ($url = false, $time = 0)
	{
		if ($url == false) {
			$redirect_url = $this->template->mod->menuRedirect($this->buildURL($this->configuration['m']), $time);
		} else {
			$redirect_url = $this->template->mod->menuRedirect($url, $time);
		}
		print $redirect_url;
	}

	/**
	 * Returns the url of the 404 page selected by the admin.
	 *
	 * @return string
	 */
	public function pageNotFound ()
	{
		$menu_id = $this->db->getSettings(array('404_error_page'), 'AdminTools');
		return $this->buildURL($menu_id['404_error_page']);
	}
}