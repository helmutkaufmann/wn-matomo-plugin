<?php namespace Mercator\Matomo\ReportWidgets;

use Mercator\Matomo\Models\Settings;
use Backend\Classes\ReportWidgetBase;
use ReportContainer;
use Exception;
use Event;
use Yaml;

/**
 * Visitors Report Widget
 */
class Individual extends ReportWidgetBase
{

    protected static $val = 1;
    /**
     * @var string The default alias to use for this widget
     */
    protected $defaultAlias = 'MatomoIndividualReportWidget';

    /**
     * Matomo reports
     t: title of the reports
     m: module
     a: action
     e: extras
     r: number of rows
    */
    protected static $matomoReports;

     /**
      * Defines the widget's properties
      * @return array
      */
     public function defineProperties()
     {

         self::$matomoReports = Yaml::parseFile(plugins_path(). "/mercator/matomo/reports.yaml");

         $matomoReports=[];
         foreach(self::$matomoReports as $acronym => $details) {
             $matomoReports[$acronym]=$details['t'];
         };

         return [
 		        'title' => [
                 'title'    => 'Title',
                 'default'  => '',
                 'type'     => 'string',
                 'required' => false,
                 'description' => 'Titel used in the widget. Leave empty to display the default title of the respective widget.'
              ],

 	            'report' => [
                 	'title'       => 'Report',
                 	'type'        => 'dropdown',
                 	'default'     => 'widgetVisitFrequencygetEvolutionGraphforceView1viewDataTablegraphEvolution',
     		          'required'    => '1',
                 	'placeholder' => 'Select report',
                  'options' => $matomoReports,
                  'description' => 'Matomo report to be drawn.'
                           ],

             'view' => [
                 'title' => 'Representation',
                 'default' => 'default',
                 'options'     => [
                 			'default' => 'default',
                      'table' => 'Table',
                 			'tableAllColumns' => 'Table - All Columns',
                 			'tableGoals' => 'Table - Goals',
                      'cloud' => 'Word Cloud',
                      'graphPie' => 'Pie Chart',
                      'graphVerticalBar' => 'Vertical Bar Chart',
                      'graphEvolution' => 'Evolution'
             		],
                 'default' => 'default',
                 'required' => '1',
                 'type' => 'dropdown',
                 'description' => 'Format of the reported data. Set to "default" if no specific format should be used.'
             ],

             'period' => [
                 'title' => 'Period',
             		 'options'     => [
               			'last7' => 'Last 7 Days',
               			'last30' => 'Last 30 Days',
               			'last360' => 'Last 360 Days',
             		 ],
                 'default' => 'last30',
                 'required' => '1',
                 'type' => 'dropdown',
                 'description' => 'Period for which the report should be drawn: 7, 30 or 360 days in the past till today.'
             ],

             'rows' => [
                 'title' => 'Rows',
                 'default' => '10',
                 'options'     => [
               			'5' => '5',
               			'10' => '10',
               			'25' => '25',
               			'50' => '50',
                     '100' => '100',
                     '500' => '500',
                     'all' => 'all'
             		 ],
                 'default' => '10',
                 'required' => '0',
                 'type' => 'dropdown',
                 'description' => 'Number of individual data elements to be presented.'
             ],

         ];
     }

    /**
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * @return void
     */
    protected function loadAssets()
    {
      $this->addJs('/plugins/mercator/matomo/assets/javascript/iFrameResizer.js');
    }

    /**
     * Renders the widget's primary contents.
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {

        self::flushAssets();

        try {
            $this->prepareVars();
        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        /**
         * Settinbgs on the plugin level
         */

        // The following code should be revisited with PHP8
        switch (substr(Settings::get('server'), 0, 5)) {
          case "https":
            $this->vars['matomoServerHTTPS'] = "https";
            $this->vars['matomoServer'] = (parse_url(Settings::get('server'), PHP_URL_HOST) ? parse_url(Settings::get('server'), PHP_URL_HOST) : "Matomo server not defined");
            break;
          case "http:":
            $this->vars['matomoServerHTTPS'] = "http";
            $this->vars['matomoServer'] = (parse_url(Settings::get('server'), PHP_URL_HOST) ? parse_url(Settings::get('server'), PHP_URL_HOST) : "Matomo server not defined");
            break;
          default:
            $this->vars['matomoServerHTTPS'] = "https";
            $this->vars['matomoServer'] = (Settings::get('server') ? Settings::get('server') : "Matomo server not defined");

        }
        $this->vars['matomoAuthorization'] = Settings::get('authorization');
        $this->vars['matomoSite'] = Settings::get('site');
        $this->vars['matomoIdent'] = "matomo" . rand();

        /*
         * Get module, action, extra parameters of the widget
        */
        if (!array_key_exists($this->property("report"), self::$matomoReports)) {
          return $this->makePartial('removed');
        }


        $matomoReport=self::$matomoReports[$this->property("report")];

        if (isset($matomoReport['r']))
          $this->setProperty("rows", $matomoReport['r']);
        $this->vars['matomoTitle'] =
          ($this->property("title") != null ? $this->property("title") : "Matomo Analytics: " . $matomoReport['t']);

        $this->vars['matomoModule'] = $matomoReport['m'];
        $this->vars['matomoAction'] = $matomoReport['a'];
        $this->vars['matomoExtra'] = (isset($matomoReport['e']) ? "&" . $matomoReport['e'] : "");
        $this->vars['matomoView'] =
          (strcmp($this->property("view"), "default") ? "&viewDataTable=" . $this->property("view") : "");
        // $this->vars['matomoView'] = "";

        return $this->makePartial('individual');

    }

    /**
     * Prepares the report widget view data
     */
    public function prepareVars()
    {

    }

}
