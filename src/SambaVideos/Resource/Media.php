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

namespace SambaVideos\Resource;

use SambaVideos\AbstractResource;
use SambaVideos\Exception\RequestException;
use Unirest\Exception;
use Unirest\Request;

/**
 * Media
 *
 * @author  Valdeci Jr <valdeci.junior@izap.com.br>
 * @version 1.0
 */
class Media extends AbstractResource
{
    /** @var string */
    protected $uri = '/medias';


    /**
     * @param int   $identifier
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function createThumbnail($identifier, array $params = [])
    {
        try {
            $body = ['qualifier' => 'THUMBNAIL'];

            $body = Request\Body::Json($body);
            $headers = ['Content-Type' => 'application/json'];

            $this->response = Request::post($this->buildUrl($params, $identifier), $headers, $body);

            if (!in_array($this->response->code, [200, 201, 204])) {
                throw $this->createRequestException('Fail to create thumbnail');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param int   $identifier
     * @param array $body
     * @param array $params
     *
     * @return array
     * @throws RequestException
     */
    public function createCaption($identifier, array $body, array $params = [])
    {
        try {
            $body = ['qualifier' => 'CAPTION'] + $body;

            $body = Request\Body::Json($body);
            $headers = ['Content-Type' => 'application/json'];

            $this->response = Request::post($this->buildUrl($params, $identifier), $headers, $body);

            if (!in_array($this->response->code, [200, 201, 204])) {
                throw $this->createRequestException('Fail to create caption');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }

    /**
     * @param string $uploadUrl
     * @param string $filename
     * @param string $mimetype
     * @param string $postName
     *
     * @return mixed
     * @throws RequestException
     */
    public function upload($uploadUrl, $filename, $mimetype = '', $postName = '')
    {
        try {
            $headers = ['Accept' => 'application/json'];

            $body = [
                'file' => Request\Body::File($filename, $mimetype, $postName),
            ];

            $this->response = Request::post($uploadUrl, $headers, $body);

            if (!in_array($this->response->code, [200, 201, 204])) {
                throw $this->createRequestException('Fail to upload file');
            }

            return $this->response->body;
        } catch (Exception $e) {
            throw $this->createRequestException($e->getMessage(), Request::getInfo(CURLINFO_HTTP_CODE), $e);
        }
    }
}
