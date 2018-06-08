<?php
/*
 * Copyright 2017 iZap
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace SambaVideos;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SambaVideos\Exception\RequestException;
use Unirest\Exception;
use Unirest\Request;
use Unirest\Response;

/**
 * AbstractResource
 *
 * @author  Valdeci Jr <valdeci.junior@izap.com.br>
 * @version 1.0
 */
abstract class AbstractResource
{
    const BASE_URL = 'http://api.sambavideos.sambatech.com/v1';

    /** @var string */
    protected $access_token;

    /** @var Response */
    protected $response;

    /** @var LoggerInterface */
    protected $logger;

    /** @var int */
    protected $logger_level;

    /** @var string */
    protected $uri;


    /**
     * AbstractResource constructor.
     *
     * @param string $access_token
     * @param string $uri
     */
    public function __construct($access_token, $uri = null)
    {
        $this->access_token = $access_token;

        if (!empty($uri)) {
            $this->uri = $uri;
        }

        Request::jsonOpts(true);
    }

    /**
     * @param string $access_token
     * @param string $uri
     *
     * @return static
     */
    public static function instance($access_token, $uri = null)
    {
        return new static($access_token, $uri);
    }

    /**
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function search(array $params = [])
    {
        try {
            $this->response = Request::get($this->buildUrl($params));

            if ($this->response->code != 200) {
                throw $this->createRequestException('Fail to get resource');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param mixed $identifier
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function get($identifier, array $params = [])
    {
        try {
            $this->response = Request::get($this->buildUrl($params, $identifier));

            if ($this->response->code != 200) {
                throw $this->createRequestException('Fail to get resource');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param array $body
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function create(array $body, array $params = [])
    {
        try {
            $body = Request\Body::Json($body);
            $headers = ['Content-Type' => 'application/json'];

            $this->response = Request::post($this->buildUrl($params), $headers, $body);

            if (!in_array($this->response->code, [200, 201, 204])) {
                throw $this->createRequestException('Fail to create resource');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param mixed $identifier
     * @param array $body
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function update($identifier, array $body, array $params = [])
    {
        try {
            $body = Request\Body::Json($body);
            $headers = ['Content-Type' => 'application/json'];

            $this->response = Request::put($this->buildUrl($params, $identifier), $headers, $body);

            if (!in_array($this->response->code, [200, 204])) {
                throw $this->createRequestException('Fail to update resource');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param mixed $identifier
     * @param array $params
     *
     * @return bool
     * @throws RequestException
     */
    public function delete($identifier, array $params = [])
    {
        try {
            $this->response = Request::delete($this->buildUrl($params, $identifier));

            if (!in_array($this->response->code, [200, 204])) {
                throw $this->createRequestException('Fail to delete resource');
            }

            return true;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param array $params
     * @param mixed $identifier
     *
     * @return string
     */
    protected function buildUrl(array $params, $identifier = null)
    {
        $params = ['access_token' => $this->access_token] + $params;
        $url = self::BASE_URL.$this->uri;

        if (!empty($identifier)) {
            $url .= "/{$identifier}";
        }

        if (!empty($params)) {
            $url .= '?'.http_build_query($params);
        }

        return $url;
    }

    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     *
     * @return RequestException
     */
    protected function createRequestException($message, $code = null, $previous = null)
    {
        $response = $this->getResponse();

        if (isset($response->body['exception']['message'])) {
            $message .= ": {$response->body['exception']['message']}";
        }

        $code = isset($response->code) ? $response->code : $code;
        $body = isset($response->body) ? $response->body : [];
        $headers = isset($response->headers) ? $response->headers : [];

        $this->getLogger()->log(
            $this->getLoggerLevel(),
            $message,
            [
                'response' => [
                    'code' => $code,
                    'body' => $body,
                    'headers' => $headers,
                ],
            ]
        );

        return new RequestException($message, $code, $previous);
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (empty($this->logger)) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return AbstractResource
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLoggerLevel()
    {
        if (empty($this->logger_level)) {
            $this->logger_level = 'error';
        }

        return $this->logger_level;
    }

    /**
     * @param mixed $logger_level
     *
     * @return AbstractResource
     */
    public function setLoggerLevel($logger_level)
    {
        $this->logger_level = $logger_level;

        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
}
