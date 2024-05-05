<?php
/**
 * @copyright   A.Novikov, 2017
 * @license     MIT
*/

// no direct access
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Factory;
use Joomla\Event\Priority;
use Joomla\Input\Input;

defined( '_JEXEC' ) or die;

class PlgSystemTemplateSwitcher extends CMSPlugin implements SubscriberInterface {
    /**
     * Application object.
     * Needed for compatibility with Joomla 4 < 4.2
     * Ultimately, we should use $this->getApplication() in Joomla 6
     *
     * @var    \Joomla\CMS\Application\CMSApplication
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param   object  &$subject  The object to observe.
     * @param   array   $config    An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config) {
        parent::__construct($subject, $config);

        if (!$this->app) {
            $this->app = Factory::getApplication();
        }
    }

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   4.0.0
     */
    public static function getSubscribedEvents(): array
    {
        return [
          //'onBeforeCompileHead' => ['disableNoconflict', Priority::HIGH],
          'onAfterInitialise' => ['onAfterInitialiseTask', Priority::HIGH],
        ];
    }

    public function disableNoconflict($event) {
        /*file_put_contents("D:/testt.txt", "test");
        throw new Exception("404");
        $doc = $this->app->getDocument();
        $wa = $doc->getWebAssetManager();
        throw new Exception("".var_dump($doc->_scripts,true));
        $wa->disableScript('jquery-noconflict');*/
    }

    /**
     * Switches template after turning on by input, or sets input for saving current template if template name is/was stored in cookies
     * Also cleans some major caches: after changing template some components saved previous layout if cache is enabled on site and if previous template has layout override of some components
     *
     * @return  void
     */
    function onAfterInitialiseTask() {
//        $app = Factory::getApplication();
        $app = $this->app;
//        $app = $this->getApplication();
        $input = $app->input;
        $template_input = $input->getCmd( 'template', '' );
        $template_cookie_var_name = 'current_template_name';
        if ( $template_input !== '' ) {
            // Clean cache "on template changed" only
            Factory::getCache('com_content')->clean();
            Factory::getCache('com_contact')->clean();
            Factory::getCache('com_search')->clean();
            Factory::getCache('mod_articles_category')->clean();
            Factory::getCache('mod_articles_news')->clean();
            Factory::getCache('mod_breadcrumbs')->clean();
            Factory::getCache('mod_custom')->clean();
            Factory::getCache('mod_feed')->clean();
            Factory::getCache('mod_menu')->clean();
            Factory::getCache('mod_random_image_extended')->clean();
            Factory::getCache('mod_finder')->clean();
            Factory::getCache('mod_templateselector')->clean();
            // Store temlate name in cookies (for 365 days ~= 1 year)
            setcookie($template_cookie_var_name, $template_input, time()+(3600 * 24 * 365), '/');
        } elseif (isset($_COOKIE[$template_cookie_var_name])) {
            $input->set( 'template', $_COOKIE[$template_cookie_var_name] );
        }
    }
}

?>
