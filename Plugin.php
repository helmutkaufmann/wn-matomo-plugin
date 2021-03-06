<?php
/**
 *  Matomo Analytics - Powerful web analytics platform allowing website users to own their data and respect their clients' privacy.
 *
 * @package  Winter
 * @author   Helmut Kaufmann
 */

namespace Mercator\Matomo;

use Backend;
use BackendAuth;
use Event;
use System\Classes\PluginBase;
use Mercator\Matomo\Models\Settings;
use Mercator\Matomo\Components\MatomoTrackerAPI;
use Yaml;

/**
 * Matomo Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Matomo',
            'description' => "Powerful web analytics platform allowing website users to own their data and respect their clients' privacy.",
            'author'      => 'Helmut Kaufmann',
            'icon'        => 'icon-line-chart'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot() {

        $settings = Settings::instance();

        //
        // Compile list of available Matomo reports
        // and make them available as settings
        //
        $availableReports = Yaml::parseFile(plugins_path(). "/mercator/matomo/reports.yaml");
        $matomoReports="";
        foreach($availableReports as $acronym => $details) {
            $matomoReports .= "<p class='help-block'><b>" . $details['t'] . "</b><br> ";
            $matomoReports .= (array_key_exists("d", $details) && ($details["d"] != "") ? $details['d'] : "<em>Sorry, no description available.</em>");
            $matomoReports .= "</p>";
            // $matomoReports .= ("<p>" . $details['t'] . "</b><br>". (array_key_exists("d", $details) ? $details['d'] : "") . "</p>");
        };
        $matomoReports .= "";
        $settings->mercator_matomo_reports = $matomoReports;

        //
        // Provide Matomo settings for use in template
        //
        switch (substr(Settings::get('server'), 0, 5)) {
          case "https":
            $settings->matomoServerHTTPS = "https";
            $settings->matomoServer = (parse_url(Settings::get('server'), PHP_URL_HOST) ? parse_url(Settings::get('server'), PHP_URL_HOST) : "Matomo server not defined");
            break;
          case "http:":
            $settings->matomoServerHTTPS = "http";
            $settings->matomoServer = (parse_url(Settings::get('server'), PHP_URL_HOST) ? parse_url(Settings::get('server'), PHP_URL_HOST) : "Matomo server not defined");
            break;
          default:
            $settings->matomoServerHTTPS = "https";
            $settings->matomoServer = (Settings::get('server') ? Settings::get('server') : "Matomo server not defined");

        }
        $settings->matomoAuthorization = Settings::get('authorization');
        $settings->matomoSite = Settings::get('site');

        //
        // Provide teh current list of tracking errors
        // The CoreHomeAdmin report does currently not work, so just provide an error message for teh time being
        //
        //
        $settings->errors = "Be back in the future - this feature has not yet been implemented.";

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents() {

        //
        // Only expose the Matomo Tracking API if it is needed
        //
        if (Settings::get("phpTracking", false))
          return [
              'Mercator\Matomo\Components\Matomo' => 'Matomo',
              'Mercator\Matomo\Components\MatomoTrackerAPI' => 'MatomoTrackingAPI',
          ];
        else
          return [
            'Mercator\Matomo\Components\Matomo' => 'Matomo',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
     public function registerPermissions() {
         return ['mercator.matomo.configuration' => ['tab' => 'Matomo Analytics', 'label' => 'Manage configuration', ],
                 'mercator.matomo.dashboard' => ['tab' => 'Matomo Analytics', 'label' => 'Manage dashbaord', ]
       ];
     }

     public function registerSettings() {
         return ['settings' => ['label' => 'Matomo', 'description' => "Powerful web analytics platform allowing website users to own their data and respect their clients' privacy.",
                 'category' => 'mercator', 'icon' => 'icon-line-chart', 'class' => 'Mercator\Matomo\Models\Settings', 'order' => 500, 'keywords' => 'Helmut Kaufmann Mercator Matomo Analytics',
                 'homepage' => 'https://github.com/helmutkaufmann/wn-matomo-plugin', 'permissions' => ['mercator.matomo.configuration']]];
     }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation() {
        return [];
    }

    public function registerReportWidgets() {

      $user = BackendAuth::getUser();
      $settings = Settings::instance();

      if ($user->hasAccess('mercator.matomo.dashboard') && $settings::get('dashboard') && $settings::get('matomoUsed'))
        return [ 'Mercator\Matomo\ReportWidgets\Individual' => [
                  'label' => 'Matomo Widget',
                  'context' => 'dashboard'
               ],
               'Mercator\Matomo\ReportWidgets\Dashboard' => [
                  'label' => 'Matomo Dashboard',
                  'context' => 'dashboard'
              ]];
      else
        return [];
    }
}
