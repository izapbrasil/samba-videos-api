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
use SambaVideos\Exception\InvalidOperationException;

/**
 * AbstractAnalytics
 *
 * @author  Valdeci Jr <valdeci.junior@izap.com.br>
 * @version 1.0
 */
abstract class AbstractAnalytics extends AbstractResource
{
    /**
     * @inheritDoc
     */
    public function create(array $params, array $body = [])
    {
        throw new InvalidOperationException('This operation is not available for this resource', 405);
    }

    /**
     * @inheritDoc
     */
    public function update($identifier, array $params, array $body = [])
    {
        throw new InvalidOperationException('This operation is not available for this resource', 405);
    }

    /**
     * @inheritDoc
     */
    public function delete($identifier, array $params = [])
    {
        throw new InvalidOperationException('This operation is not available for this resource', 405);
    }
}
