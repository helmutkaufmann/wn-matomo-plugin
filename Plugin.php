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

        $availableReports = Yaml::parseFile(plugins_path(). "/mercator/matomo/reports.yaml");
        $matomoReports="";
        foreach($availableReports as $acronym => $details) {
            $matomoReports .= "<p class='help-block'><b>" . $details['t'] . "</b><br> ";
            $matomoReports .= (array_key_exists("d", $details) && ($details["d"] != "") ? $details['d'] : "<em>Sorry, no description available.</em>");
            $matomoReports .= "</p>";
            // $matomoReports .= ("<p>" . $details['t'] . "</b><br>". (array_key_exists("d", $details) ? $details['d'] : "") . "</p>");
        };
        $matomoReports .= "";
        $settings = Settings::instance();
        $settings->mercator_matomo_reports = $matomoReports;
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents() {

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

    public function onLoad() {
      $this->addJs('/plugins/mercator/matomo/assets/javascript/jResizer.js');
    }

}
