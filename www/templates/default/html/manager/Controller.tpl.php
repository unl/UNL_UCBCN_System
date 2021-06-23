<?php
use UNL\Templates\Templates;

$page = Templates::factory('AppLocal', Templates::VERSION_5_3);

if (file_exists(\UNL\UCBCN\Util::getWWWRoot() . '/wdn/templates_5.3')) {
    $page->setLocalIncludePath(\UNL\UCBCN\Util::getWWWRoot());
}

$title = '';
$site_title = 'UNL Events';
if (!$context->getCalendar()) {
    $title .= 'UNL Events';
} else {
    $title .= 'UNL';
    if ($context->getCalendar()->id != UNL\UCBCN\Frontend\Controller::$default_calendar_id) {
        $title .= ' | '.$context->getCalendar()->name.' ';
    }
    $title .= ' | Manager | Events';
    $site_title = $context->getCalendar()->name . ' Events Manager';
}
$view_class = str_replace('\\', '_', strtolower($context->options['model']));

//Document titles
$page->doctitle = '<title>' . $title . '</title>';
$page->titlegraphic = '<a class="dcf-txt-h5" href="/">' . $site_title . '</a>';
$page->setParam('class', 'hide-wdn_navigation_wrapper');
$page->affiliation = '';

// Add WDN Deprecated Styles
$page->head .= '<link rel="preload" href="/wdn/templates_5.3/css/deprecated.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"> <noscript><link rel="stylesheet" href="/wdn/templates_5.3/css/deprecated.css"></noscript>';

//css
$page->addStyleSheet($base_frontend_url.'templates/default/html/css/events.css?v='.UNL\UCBCN\Frontend\Controller::$version);
$page->addStyleSheet($base_frontend_url.'templates/default/html/css/manager.css?v='.UNL\UCBCN\Frontend\Controller::$version);
$page->addStyleSheet($base_frontend_url.'templates/default/html/js/vendor/select2/css/select2.min.css?v='.UNL\UCBCN\Frontend\Controller::$version);

// no menu items, so hide mobile menu
$page->addStyleDeclaration("#dcf-mobile-toggle-menu {display: none!important}");

//javascript
$page->addScriptDeclaration('var frontend_url = "'.$base_frontend_url.'";');
$page->addScriptDeclaration('var manager_url = "'.$base_manager_url.'";');
$page->addScriptDeclaration("WDN.initializePlugin('notice');");
$page->addScriptDeclaration("
require(['jquery'], function ($) {
    $('#breadcrumbs > ul > li > a').last().parent().addClass('last-link');
});");
$page->addScript($base_frontend_url .'templates/default/html/js/manager.min.js?v='.UNL\UCBCN\Frontend\Controller::$version);

//other
$savvy->addGlobal('page', $page);

//Render output
$template = null;
if ($context->output->getRawObject() instanceof Exception) {
    $template = 'Exception.tpl.php';
}

$page->maincontentarea = '
<div class="dcf-bleed view-' . $view_class . ' events-manager">
    <div class="dcf-wrapper">';
if ($_SERVER['SERVER_NAME'] == 'events-dev.unl.edu') {
    $page->maincontentarea .=
        '<div class="wdn_notice">
            <div class="close">
            <a href="#" title="Close this notice">Close this notice</a>
            </div>
            <div class="message">
            <h4>UNL Events Test</h4>
            <div class="message-content">
            This is the test server for UNL Events. Events created and published here will not affect the main UNL calendar or your site\'s sub-calendar. Please send all feedback to <a href="mailto:dxg@listserv.unl.edu">the dev team</a> at dxg@listserv.unl.edu.</div>
            </div>
        </div>';
}

$page->maincontentarea .= '
        <section class="dcf-grid dcf-col-gap-vw dcf-pb-8">
            <div class="dcf-col-100% dcf-col-75%-start@md">
';

if (($notice = $context->getNotice()) != NULL) {
    $class = '';
    switch ($notice['level']) {
        case 'success':
            $class = 'affirm';
            break;
        case 'failure':
            $class = 'negate';
            break;
        case 'alert':
            $class = 'alert';
            break;
    }
    $page->maincontentarea .= '
                <div id="notice" class="wdn_notice ' . $class . '">
                    <div class="close">
                    <a href="#" title="Close this notice">Close this notice</a>
                    </div>
                    <div class="message">
                    <h4>' . $notice['header'] . '</h4>
                    <div class="message-content">' . html_entity_decode($notice['messageHTML']) . '</div>
                    </div>
                </div>
    ';
} else {
    $page->maincontentarea .= '
                <div id="notice" class="wdn_notice" style="display: none;">
                    <div class="close">
                    <a href="#" title="Close this notice">Close this notice</a>
                    </div>
                    <div class="message">
                    <h4>Message Header</h4>
                    <div class="message-content">Message Content</div>
                    </div>
                </div>
    ';
}
$page->maincontentarea .= $savvy->render($context->output, $template) . '
            <br>
            </div>
            <div class="dcf-col-100% dcf-col-25%-end@md">
                <nav class="calendars-list">
                    ' . $savvy->render($context, 'navigation.tpl.php') . '
                </nav>
            </div>
        </section>
    </div>
</div>
';

$page->contactinfo = $savvy->render($context, 'html/localfooter.tpl.php');


//echo everything
echo $page;
