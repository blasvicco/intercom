<?php
/**
 * Intercom plugin for Craft CMS 3.x
 *
 * Interface for Intercom message post
 *
 * @link      https://github.com/blasvicco
 * @copyright Copyright (c) 2018 Blas Vicco
 */

namespace blasvicco\intercom;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use blasvicco\intercom\models\Settings;

/**
 * Class Intercom
 *
 * @author    Blas Vicco
 * @package   Intercom
 * @since     1.0.4
 *
 */
class Intercom extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Intercom
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['intercom/api/post']  = 'intercom/api/post';
                $event->rules['intercom/api/token'] = 'intercom/api/token';
            }
        );

        Craft::info(
            Craft::t(
                'intercom',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
    /**
     * @inheritdoc
     */
    protected function createSettingsModel() {
        return new Settings();
    }
}
