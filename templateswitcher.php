<?php
/**
 * @copyright   A.Novikov, 2017
 * @license     MIT
*/

// no direct access
defined( '_JEXEC' ) or die;

class PlgSystemTemplateSwitcher extends JPlugin {
    /**
     * Switches template after turning on by input, or sets input for saving current template if template name is/was stored in cookies
     * Also cleans some major caches: after changing template some components saved previous layout if cache is enabled on site and if previous template has layout override of some components
     *
     * @return  void
     */
    function onAfterInitialise() {
        $app = JFactory::getApplication();
        $input = $app->input;
        $template_input = $input->getCmd( 'template', '' );
        $template_cookie_var_name = 'current_template_name';
        if ( $template_input !== '' ) {
            // Clean cache "on template changed" only
            JFactory::getCache('com_content')->clean();
            JFactory::getCache('com_contact')->clean();
            JFactory::getCache('com_search')->clean();
            JFactory::getCache('mod_articles_category')->clean();
            JFactory::getCache('mod_articles_news')->clean();
            JFactory::getCache('mod_breadcrumbs')->clean();
            JFactory::getCache('mod_custom')->clean();
            JFactory::getCache('mod_feed')->clean();
            JFactory::getCache('mod_menu')->clean();
            JFactory::getCache('mod_random_image_extended')->clean();
            JFactory::getCache('mod_search')->clean();
            JFactory::getCache('mod_templateselector')->clean();
            // Store temlate name in cookies (for 365 days ~= 1 year)
            setcookie($template_cookie_var_name, $template_input, time()+(3600 * 24 * 365), '/');
        } elseif (isset($_COOKIE[$template_cookie_var_name])) {
            $input->set( 'template', $_COOKIE[$template_cookie_var_name] );
        }
    }
}

?>
