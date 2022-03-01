<?php namespace Mercator\Matomo\Components;

use Mercator\Matomo\Models\Settings;
use Cms\Classes\ComponentBase;
use Backend\Facades\BackendAuth;
use Winter\User\Facades\Auth;


class Matomo extends ComponentBase
{
    public function componentDetails()
    {

        return [
            'name'        => 'Matomo ',
            'description' => "Code for Matomo website tracking.",
            'author'      => 'Helmut Kaufmann',
            'icon'        => 'icon-line-chart'
        ];
    }

    public function defineProperties()
    {
      return [];
    }

    public function onRun() {

      $this->page['matomoServer'] = Settings::get("server", "");
      $this->page['matomoSite'] = Settings::get("site", "");
      $this->page['matomoExcluded'] = Settings::get("excluded", false);

      if (BackendAuth::getUser() && !Settings::get('backendTracking')) {
    		$this->page['matomoTracking'] = false;
      }
      else {
        $this->page['matomoTracking'] = true;
      }

    }

}

?>
