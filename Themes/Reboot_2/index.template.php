<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0.14
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = false;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	//<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];
	
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/custom.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/bootstrap.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1">';

	if (!empty($settings['primary_colour']))
		echo '
		<style>
			/* Grouped colours for changes. Left as one by one, for further grouping */
			/* First group / primary colour */
			h4.catbg, h4.catbg2, h3.catbg, h3.catbg2, .table_list tbody.header td.catbg, div.cat_bar /* Boards category */
			{
				background: ' . $settings['primary_colour'] . ';
			}
			.catbg, .catbg2, tr.catbg td, tr.catbg2 td, tr.catbg th, tr.catbg2 th, tr.catbg th.first_th, tr.catbg th.last_th
			{
				background: ' . $settings['primary_colour'] . ';
			}
			.topic-title
			{
				background: ' . $settings['primary_colour'] . ';
			}
			li.profile
			{
				background: ' . $settings['primary_colour'] . ';
			}
			.poster-content h4
			{
				background: ' . $settings['primary_colour'] . ';
			}
			.poster-content
			{
				border-left: 1px solid ' . $settings['primary_colour'] . ';
				border-right: 1px solid ' . $settings['primary_colour'] . ';
			}
		</style>
		';
		
	if (!empty($settings['secondary_colour']))
		echo '
		<style>
			/* Second group / secondary colour */
			div.title_bar, h4.titlebg, h3.titlebg
			{
				background: ' . $settings['secondary_colour'] . ';
			}
			.accordion-control, .header-bar
			{
				background: ' . $settings['secondary_colour'] . ';
			}
			.new_event
			{
				background: ' . $settings['secondary_colour'] . ';
			}
			.table_list tbody.content td.info a.subject
			{
				color: ' . $settings['secondary_colour'] . ';
			}
		</style>
		';
	
	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	
	echo '
	<div class="container-fluid">';
	
	// Show the menu here, according to the menu sub template.
	template_menu();

	// The main content should go here.
	
	if (!empty($settings['header_image']))
	{
		echo '<div class="row-fluid clearfix">
				<div class="col-md-8 col-sm-12 col-xs-12">';
		
					echo '<img class="img-responsive header-image" src="', $settings['images_url'] ,'/theme/', $settings['header_image'] ,'" alt="header image" />
				</div>';
			//echo '<div class="col-md-4 col-sm-12 col-xs-12">';
				if ($context['user']['is_logged']) // && empty($context['current_topic']))
				{
					//echo '<div class="row">';
							echo '<div class="clearfix visible-sm visible-xs"></div>';
							echo '<div class="col-md-3 col-sm-9 col-xs-9">';
							echo '<ul class="reset">
								<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
								<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
								<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

								// Is the forum in maintenance mode?
								if ($context['in_maintenance'] && $context['user']['is_admin'])
									echo '
											<li class="notice">', $txt['maintain_mode_on'], '</li>';

								// Are there any members waiting for approval?
								if (!empty($context['unapproved_members']))
									echo '
											<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

								if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
									echo '
											<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

								echo '
											<li>', $context['current_time'], '</li>
										</ul>';
							echo '</div>';
						echo '<div class="col-md-1 col-sm-2 col-xs-2">';
					
							if (!empty($context['user']['avatar'])) {
								echo '<p class="avatar">', str_replace("class=\"avatar\"", "class=\"img-responsive\"", $context['user']['avatar']['image']), '</p>';
							} else {
								echo '<p class="avatar"><img src="http://d1112petr9ji1a.cloudfront.net/assets/default/default-avatar-60x60@2x-f18d21ef83549dce8336ecb0328f8a3e.png" alt="default avatar" class="img-responsive" /></p>';
							}
						echo '</div>';
					
							
					//echo '</div>';
					echo '</div>'; // /col /row
				}

		//echo '</div>';
		echo '</div>'; // /col /row
	}
	else
	{
		if ($context['user']['is_logged']) // && empty($context['current_topic']))
		{
			//echo '<div class="row">';
							echo '<div class="col-md-3 col-sm-9 col-xs-9">';
							echo '<ul class="reset">
								<li class="greeting">', $txt['hello_member_ndt'], ' <span>', $context['user']['name'], '</span></li>
								<li><a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a></li>
								<li><a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a></li>';

								// Is the forum in maintenance mode?
								if ($context['in_maintenance'] && $context['user']['is_admin'])
									echo '
											<li class="notice">', $txt['maintain_mode_on'], '</li>';

								// Are there any members waiting for approval?
								if (!empty($context['unapproved_members']))
									echo '
											<li>', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' <a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], '</a> ', $txt['approve_members_waiting'], '</li>';

								if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
									echo '
											<li><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></li>';

								echo '
											<li>', $context['current_time'], '</li>
										</ul>';
							echo '</div>';
						echo '<div class="col-md-1 col-sm-2 col-xs-2 content-right">';
					
							if (!empty($context['user']['avatar'])) {
								echo '<p class="avatar">', str_replace("class=\"avatar\"", "class=\"img-responsive\"", $context['user']['avatar']['image']), '</p>';
							} else {
								echo '<p class="avatar"><img src="http://d1112petr9ji1a.cloudfront.net/assets/default/default-avatar-60x60@2x-f18d21ef83549dce8336ecb0328f8a3e.png" alt="default avatar" class="img-responsive" /></p>';
							}
						echo '</div>';
					
							
					//echo '</div>';
					echo '</div>'; // /col /row
		}
	}
	
	// Show a random news item? (or you could pick one from news_lines...)
	if (!empty($settings['enable_news']))
		echo '<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="title-bar-center">
							<div class="header-bar">
								', $txt['news'], '
							</div>
						</div>
						<div class="news-fader">
							<p>', $context['random_news_line'], '</p>
						</div>
					</div>
				</div>
			</div>';

	// Custom banners and shoutboxes should be placed here, before the linktree.

	// Show the navigation tree.
	theme_linktree();
}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
	</div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footer_section"><div class="frame">
		<ul class="reset">
			<li>Theme designed by <a href="http://studiocrimes.com"><img src="', $settings['images_url'] ,'/theme/studiocrimes.png" alt="studiocrimes" /></a></li>
			<li class="copyright">', theme_copyright(), '</li>
			<li><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
			<li class="last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
	</div>

	</div>';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '<div class="container-fluid">
		<ol class="breadcrumb">';
		
		foreach ($context['linktree'] as $link_num => $tree)
		{
			echo '
				<li', ($link_num == count($context['linktree']) - 1) ? ' class="active"' : '', '><a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a></li>';
		}
				
	echo '
		</ol></div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#theme-navbar" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="' . $scripturl . '">' . $context['forum_name'] . '</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="theme-navbar">';
			if ($context['user']['is_guest'])
			{
				echo '
					<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
					<form id="guest_form" class="navbar-form navbar-left" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
						<div class="form-group">
							<input type="text" name="user" class="form-control" aria-label="username" placeholder="username">
							<input type="password" name="passwrd" class="form-control" aria-label="password" placeholder="password">
							<input type="hidden" name="hash_passwrd" value="" /><input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
						</div>
						<button type="submit" class="btn btn-default">login</button>
					</form>';
			}
	echo '
				<ul class="nav navbar-nav navbar-right">';
					foreach ($context['menu_buttons'] as $act => $button)
					{
						if (empty($button['sub_buttons']))
						{
							echo '
								<li class="', $button['active_button'] ? 'active ' : '', '"><a href="', $button['href'], '">', $button['title'], '</a></li>';
						}
						
						if (!empty($button['sub_buttons']))
						{
							echo '
								<li class="dropdown ' , ($context['user']['unread_messages'] > 0 && ($act == 'pm')) ? 'new_event' : '' , '">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">', $button['title'], ' <span class="caret"></span></a>
									<ul class="dropdown-menu">';
								foreach ($button['sub_buttons'] as $childbutton)
								{
									echo '
										<li><a href="', $childbutton['href'], '">', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</a></li>';
								}
							echo '
									</ul>
								</li>';
						}
					}
	echo '
				</ul>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>';

}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="btn btn-default button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul class="nav nav-pills">',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>
