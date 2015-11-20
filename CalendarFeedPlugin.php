<?php
class CalendarFeedPlugin extends Omeka_Plugin_AbstractPlugin
{

	protected $_hooks = array(
		'install',
		'uninstall',
		'config_form',
		'config',
		'public_footer',
	);


	protected $_options = array(
		'cf_displayOnMobile'=>0,
		'cf_notify'=>1,
		'cf_rssfeed'=>null,
	);


	/*
    ** Plugin options
    */

	public function hookConfigForm()
	{
		require dirname(__FILE__) . '/config_form.php';
	}

	public function hookConfig()
	{
		set_option('cf_displayOnMobile', (bool)(int)$_POST['cf_displayOnMobile']);
		set_option('cf_notify', (bool)(int)$_POST['cf_notify']);
		set_option('cf_rssfeed', $_POST['cf_rssfeed']);
	}

	/*
	** Public display
	*/

	public function hookPublicFooter()
	{

		echo cf_calendar_scripts();

	}

	/**
	 * Install the plugin.
	 */
	public function hookInstall()
	{
		$this->_installOptions();

	}

	/**
	 * Uninstall the plugin.
	 */
	public function hookUninstall()
	{
		$this->_uninstallOptions();

	}
}

function cf_calendar_scripts($option=null)
{	
	$feed=get_option('cf_rssfeed');
	$show_mobile=get_option('cf_displayOnMobile');
	$breakpoint='768';

	$html  = '<script async defer>console.log("'.$feed.'")</script>';

	if(!$show_mobile){
		//TODO: swap out the boolean option for a user-configurable min breakpoint where 0 equals none
		$html.='<style type="text/css">@media all and (max-width:'.$breakpoint.'px){
			#notify#calendar{display:none !important;}}</style>';
	}
	return $html;

}