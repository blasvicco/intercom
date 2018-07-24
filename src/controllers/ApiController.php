<?php
/**
 * Intercom plugin for Craft CMS 3.x
 *
 * Interface for Intercom message post
 *
 * @link      https://github.com/blasvicco
 * @copyright Copyright (c) 2018 Blas Vicco
 */

namespace blasvicco\intercom\controllers;

use blasvicco\intercom\Intercom;

use Craft;
use craft\web\Controller;
use Intercom\IntercomClient;

use \DateTime;
use \DateInterval;

/**
 * @author    Blas Vicco
 * @package   Intercom
 * @since     1.0.0
 */
class ApiController extends Controller {

  const ERROR           = -1;
  const SUCCESS         = 0;
  const TOKEN_SPLITTER  = 'TdE9669!';

  private $lastError = NULL;

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['post', 'token'];

    // Public Methods
    // =========================================================================

    /**
     * @return json with response message
     */
    public function actionPost() {
      $response = [
        'status' => $this::ERROR,
        'msg'    => Craft::t('intercom', 'Not valid post')
      ];
      $ticket = Craft::$app->getRequest()->getParam('ticket');
      $ticket['page'] = $ticket['page'] ?? 'Test Page';
      if ($this->isValidTicket($ticket) && $this->emailFromUser($ticket)) {
        $response = [
          'status' => $this::SUCCESS,
          'msg'    => Craft::t('intercom', 'Post sent successfully')
        ];
      } else {
        $response['exception'] = "\n" . $this->lastError;
      }
      return $this->asJson($response);
    }

    /**
     * @return json with token value
     */
    public function actionToken() {
      $key    = openssl_random_pseudo_bytes(32);
      $iv     = openssl_random_pseudo_bytes(16);
      $phrase = base64_encode(openssl_random_pseudo_bytes(32));
      Craft::$app->getSession()->set('intercom_key',  base64_encode($key));
      Craft::$app->getSession()->set('intercom_iv',  base64_encode($iv));
      Craft::$app->getSession()->set('intercom_phrase', $phrase);
      return $this->asJson([
        'token' => base64_encode(openssl_encrypt($phrase . $this::TOKEN_SPLITTER . date('YmdHis'), 'AES-256-CBC', $key, 0, $iv))
      ]);
    }

    // Private Methods
    // =========================================================================

    /**
     * @return bool ticket data is valid
     */
    private function isValidTicket($ticket) : bool {
      return isset($ticket) && $this->isValidToken($ticket['extra'])
        && filter_var($ticket['email'], FILTER_VALIDATE_EMAIL)
        && isset($ticket['name']) && isset($ticket['details']);
    }

    /**
     * @return bool token is valid
     */
    private function isValidToken($token) : bool {
      $key    = Craft::$app->getSession()->get('intercom_key');
      $iv     = Craft::$app->getSession()->get('intercom_iv');
      $phrase = Craft::$app->getSession()->get('intercom_phrase');

      $token = openssl_decrypt(base64_decode($token), 'AES-256-CBC', base64_decode($key), 0, base64_decode($iv));
      $token = explode($this::TOKEN_SPLITTER, $token);

      $date = new DateTime("now");
      $date->sub(new DateInterval('P0DT0H5M0S'));
      $tokenDateTime = DateTime::createFromFormat('YmdHis', $token[1] ?? NULL);

      return $token[0] == $phrase && ($date < $tokenDateTime) && $tokenDateTime < (new DateTime("now"));
    }

  /**
   * @return bool message was send
   * Routes an email through intercom to the help team directly
   */
  private function emailFromUser($ticket) : bool {
    $settings = Intercom::$plugin->getSettings();
    if (empty($settings['oauth'])) {
      $this->lastError = Craft::t('intercom', 'Missing configuration file');
      return FALSE;
    }


    $client   = new IntercomClient($settings['oauth'], null);
    try {
      $user = $client->users->getUsers(["email" => $ticket['email']]);
    } catch(\GuzzleHttp\Exception\ClientException $e) {
      if ($e->getResponse()->getStatusCode() == 404) {
        //user for that email doesn't exist, create an intercom user for them
        try {
          $user = $client->users->create([
            'email' => $ticket['email'],
            'name'  => $ticket['name']
          ]);
        } catch (\Exception $err) {
          $this->lastError = $err;
          return FALSE;
        }
      }
    }

    $email = [
      'from' => [
        'type'  => 'user',
        'id'    => $user->id,
        'email' => $ticket['email'],
      ],
      'to' => [
        'type'  => 'admin',
        'email' => $settings['appId'] . '@incoming.intercom.io',
      ],
      'body' => str_replace([
          '{{ PAGE }}',
          '{{ EMAIL }}',
          '{{ NAME }}',
          '{{ DETAILS }}',
        ],
        [
          $ticket['page'],
          $ticket['email'],
          $ticket['name'],
          $ticket['details'],
        ], $settings['body']
      ),
    ];

    try {
      $client->messages->create($email);
    } catch (\Exception $e) {
      $this->lastError = $e;
      return FALSE;
    }

    return TRUE;
  }
}
