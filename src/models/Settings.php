<?php
/**
 * Intercom plugin for Craft CMS 3.x
 *
 * Interface for Intercom message post
 *
 * @link      https://github.com/blasvicco
 * @copyright Copyright (c) 2018 Blas Vicco
 */

namespace blasvicco\intercom\models;

use blasvicco\intercom\Intercom;

use Craft;
use craft\base\Model;

/**
 * @author    Blas Vicco
 * @package   Intercom
 * @since     1.0.4
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $oauth = null;

    /**
     * @var string
     */
    public $appId = null;

    /**
     * @var string
     */
    public $body = null;

    /**
     * @var string
     */
    public $redirect = null;

    /**
     * @var bool
     */
    public $requireToken = TRUE;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['oauth', 'appId', 'body', 'redirect'], 'string'],
            [['requireToken'], 'bool'],
            [['oauth', 'appId', 'body'], 'required']
        ];
    }
}
