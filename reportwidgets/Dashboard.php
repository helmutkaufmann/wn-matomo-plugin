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

        $this->vars['matomoServer'] = Settings::instance()->attributes['server'];
        $this->vars['matomoServerHTTPS'] = (Settings::instance()->attributes['serverHTTPS'] ? "https" : "http");
        $this->vars['matomoAuthorization'] = Settings::instance()->attributes['authorization'];
        $this->vars['matomoSite'] = Settings::instance()->attributes['site'];
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
