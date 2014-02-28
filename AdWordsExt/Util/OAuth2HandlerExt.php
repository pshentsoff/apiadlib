<?php
/**
 * @file        OAuth2HandlerExt.php
 * @description
 *
 * PHP Version  5.3.13
 *
 * @package 
 * @category
 * @plugin URI
 * @copyright   2014, Vadim Pshentsov. All Rights Reserved.
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @author      Vadim Pshentsov <pshentsoff@gmail.com> 
 * @link        http://pshentsoff.ru Author's homepage
 * @link        http://blog.pshentsoff.ru Author's blog
 *
 * @created     28.02.14
 */

require_once dirname(__FILE__) . '/../../Common/Util/SimpleOAuth2Handler.php';

class OAuth2HandlerExt extends SimpleOAuth2Handler {

    public function __construct($server = NULL, $scope = NULL, $curlUtils = NULL) {
        parent::__construct($server, $scope, $curlUtils);
    }

    /**
     * Function almost fully repeat parent function except one moment - bug in merging $params arrays. In parent variant
     * merging replace sent values with default. This override fix this bug.
     *
     * @param array $credentials
     * @param null $redirectUri
     * @param null $offline
     * @param array $params
     * @return string
     * @throws OAuth2Exception
     */
    public function GetAuthorizationUrl(array $credentials,
                                        $redirectUri = NULL, $offline = NULL, array $params = NULL) {
        if (empty($credentials['client_id'])) {
            throw new OAuth2Exception('client_id required.');
        }
        $params = is_null($params) ? array() : $params;
        $redirectUri = is_null($redirectUri) ?
            self::DEFAULT_REDIRECT_URI : $redirectUri;

        $params = array_merge(array(
            'response_type' => 'code',
            'client_id' => $credentials['client_id'],
            'redirect_uri' => $redirectUri,
            'scope' => $this->scope,
            'access_type' => $offline ? 'offline' : 'online'
        ), $params);
        return $this->GetAuthorizeEndpoint($params);
    }

    public function GetScope() {
        return $this->scope;
    }

    public function SetScope($scope) {
        $this->scope = $scope;
    }
}