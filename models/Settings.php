<?php namespace  Mercator\Matomo\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'mercator_matomo_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

}
