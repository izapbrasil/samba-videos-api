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
require_once '../vendor/autoload.php';

$access_token = 'ACCESS_TOKEN';
$media_id = 'MEDIA_ID';

try {
    // Projects
    $project = \SambaVideos\Resource\Project::instance($access_token);
    $projects = $project->search();
    $project_id = $projects[0]['id'];
    print_r($projects);

    // Categories
    $category = \SambaVideos\Resource\Category::instance($access_token);
    $categories = $category->search(['pid' => $project_id]);
    print_r($categories);

    // Medias
    $media = \SambaVideos\Resource\Media::instance($access_token);
    $medias = $media->search(['pid' => $project_id]);
    print_r($medias);

    // Upload Media
    $media = \SambaVideos\Resource\Media::instance($access_token);
    $response = $media->create(['qualifier' => 'VIDEO'], ['pid' => $project_id]);
    print_r($response);

    $response = $media->upload($response['uploadUrl'], 'video.mp4');
    print_r($response);


    // Upload Thumbnail
    $media = \SambaVideos\Resource\Media::instance($access_token);
    $response = $media->createThumbnail($media_id, ['pid' => $project_id]);
    print_r($response);

    $response = $media->upload($response['uploadUrl'], 'thumb.png');
    print_r($response);
} catch (Exception $e) {
    var_dump($e);
}

