<?php namespace Mercator\Matomo\ReportWidgets;

use Mercator\Matomo\Models\Settings;
use Backend\Classes\ReportWidgetBase;
use Exception;

/**
 * Visitors Report Widget
 */
class Dashboard extends ReportWidgetBase
{
    /**
     * @var string The default alias to use for this widget
     */
    protected $defaultAlias = 'DashboardReportWidget';

    /**
     * Defines the widget's properties
     * @return array
     */
    public function defineProperties()
    {
        return [
		        'title' => [
                'title'    => 'Title',
                'default'  => 'Matomo Dashboard',
                'type'     => 'string',
                'required' => '1',
                'description' => 'The name of the dashboard.'
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
                 'description' => 'The reporting period (last 7, 30 or 260 days).',
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
    }

    /**
     * Renders the widget's primary contents.
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        try {
            $this->prepareVars();
        } catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

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
         * Get title of the widget
        */
        $this->vars['matomoTitle'] =
          ($this->property("title") != null ? $this->property("title") : "Matomo Dashboard");
        $this->vars['matomoPeriod'] = $this->property("period");


        return $this->makePartial('Dashboard');
    }

    /**
     * Prepares the report widget view data
     */
    public function prepareVars()
    {
    }

}
