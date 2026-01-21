<?php

function makeApiRequest($endpoint) {
    $url = API_BASE_URL . $endpoint;
    
    error_log("Making API request to: " . $url);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'okhttp/4.8.0');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('cURL Error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON Error: ' . json_last_error_msg());
        error_log('Raw response: ' . substr($response, 0, 1000));
        return null;
    }
    
    if (is_array($data)) {
        error_log("API returned " . count($data) . " items");
    } else {
        error_log("API returned non-array data");
    }
    
    return $data;
}


function getFeaturedContent() {
    $content = getCategoryContent('rating', null, 1);
    return $content && !empty($content) ? $content[0] : null;
}


function getSeriesByCategory($categoryLabel, $classificationFilter = null, $limit = 10) {
    // Use the provided API endpoint for Ramadan series
    $endpoint = "/serie/by/filtres/0/created/2/" . API_KEY . "/" . API_TOKEN . "/";
    $data = makeApiRequest($endpoint);
    
    if (!$data || !is_array($data)) {
        error_log("No data returned for category: " . $categoryLabel);
        return [];
    }
    
    // Filter series by category label and classification
    $filteredSeries = array_filter($data, function($item) use ($categoryLabel, $classificationFilter) {
        $hasCategory = false;
        if (isset($item['genres']) && is_array($item['genres'])) {
            foreach ($item['genres'] as $genre) {
                if (isset($genre['title']) && $genre['title'] === $categoryLabel) {
                    $hasCategory = true;
                    break;
                }
            }
        }
        
        $hasClassification = true;
        if ($classificationFilter && isset($item['classification'])) {
            $hasClassification = strpos($item['classification'], $classificationFilter) !== false;
        }
        
        return $hasCategory && $hasClassification;
    });
    
    $filteredSeries = array_values($filteredSeries);
    error_log("Found " . count($filteredSeries) . " items for category: " . $categoryLabel . " with classification filter: " . ($classificationFilter ?: "none"));
    
    return array_slice($filteredSeries, 0, $limit);
}


function getRamadanSeries($limit = 10) {
    return getSeriesByCategory('مسلسلات رمضان 2025', 'مصر', $limit);
}


function getCategoryContent($sort = 'created', $type = null, $limit = 20) {
    $typeParam = $type ?: 'serie'; // Default to serie if null
    $endpoint = "/{$typeParam}/by/filtres/0/{$sort}/1/" . API_KEY . "/" . API_TOKEN . "/";
    
    error_log("Fetching category content: endpoint=" . $endpoint);
    $data = makeApiRequest($endpoint);
    
    if (!$data || !is_array($data)) {
        error_log("No data returned for category: sort=" . $sort . ", type=" . ($type ?: "all"));
        return [];
    }
    
    // Filter by type if specified
    if ($type) {
        $data = array_filter($data, function($item) use ($type) {
            return isset($item['type']) && $item['type'] === $type;
        });
        $data = array_values($data);
    }
    
    error_log("Found " . count($data) . " items for category: sort=" . $sort . ", type=" . ($type ?: "all"));
    return array_slice($data, 0, $limit);
}


function searchContent($query) {
    $encodedQuery = rawurlencode($query);
    $endpoint = "/search/{$encodedQuery}/0/" . API_KEY . "/" . API_TOKEN . "/";
    return makeApiRequest($endpoint);
}

/**
 * ahmed abdel monem TG @M_N_3_M
 */
function getMovieDetails($id) {
    $endpoint = "/movie/by/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    $movie = makeApiRequest($endpoint);
    
    if ($movie) {
        return $movie;
    }
    
    $movies = getCategoryContent('created', 'movie', 50);
    
    foreach ($movies as $movie) {
        if ($movie['id'] == $id) {
            return $movie;
        }
    }
    
    return null;
}


function getMovieSources($id) {
    $endpoint = "/movie/source/by/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    return makeApiRequest($endpoint);
}


function getSeriesDetails($id) {
    // Try direct API endpoint first with updated page parameter
    $endpoint = "/serie/by/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    $series = makeApiRequest($endpoint);
    
    if ($series && !empty($series['title']) && $series['title'] !== "Series #{$id}") {
        error_log("Series details found for ID: " . $id . " - " . $series['title']);
        return $series;
    }
    
    error_log("Series details not found via direct API, trying fallback for ID: " . $id);
    
    // Fallback: Fetch from category content with increased limit and page 2
    $allSeries = getCategoryContent('created', 'serie', 100); // Try page 1 first
    
    foreach ($allSeries as $seriesItem) {
        if ($seriesItem['id'] == $id) {
            error_log("Series details found in category results for ID: " . $id . " - " . $seriesItem['title']);
            return $seriesItem;
        }
    }
    
    // Try page 2 explicitly since Ramadan series might be there
    $endpointPage2 = "/serie/by/filtres/0/created/2/" . API_KEY . "/" . API_TOKEN . "/";
    $seriesPage2 = makeApiRequest($endpointPage2);
    
    if ($seriesPage2 && is_array($seriesPage2)) {
        foreach ($seriesPage2 as $seriesItem) {
            if ($seriesItem['id'] == $id) {
                error_log("Series details found in page 2 results for ID: " . $id . " - " . $seriesItem['title']);
                return $seriesItem;
            }
        }
    }
    
    // If still not found, return a minimal series object
    error_log("Series not found in any results, returning minimal object for ID: " . $id);
    return [
        'id' => $id,
        'title' => "Series #{$id}",
        'description' => "Details not available at this time.",
        'image' => '/placeholder.svg',
        'year' => 'N/A',
        'type' => 'serie'
    ];
}

/**
 * Get series seasons and episodes
 * 
 * @param int $id Series ID
 * @return array|null Series seasons or null
 */
function getSeriesSeasons($id) {
    $endpoint = "/season/by/serie/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    $seasons = makeApiRequest($endpoint);
    
    if (!$seasons || !is_array($seasons)) {
        error_log("No seasons found for series ID: " . $id);
        return [];
    }
    
    error_log("Found " . count($seasons) . " seasons for series ID: " . $id);
    return $seasons;
}

/**
 * Get episode details
 * 
 * @param int $id Episode ID
 * @return array|null Episode details or null
 */
function getEpisodeDetails($id) {
    $endpoint = "/episode/by/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    $episode = makeApiRequest($endpoint);
    
    if ($episode) {
        if (isset($episode['season_id'])) {
            $seasonEndpoint = "/season/by/{$episode['season_id']}/" . API_KEY . "/" . API_TOKEN . "/";
            $season = makeApiRequest($seasonEndpoint);
            if ($season && isset($season['serie_id'])) {
                $episode['seriesId'] = $season['serie_id'];
            }
        }
        return $episode;
    }
    
    return [
        'id' => $id,
        'title' => "Episode {$id}",
        'description' => "Episode description",
        'seriesId' => 0,
    ];
}

/**
 * Get episode sources
 * 
 * @param int $id Episode ID
 * @return array|null Episode sources or null
 */
function getEpisodeSources($id) {
    $endpoint = "/episode/source/by/{$id}/" . API_KEY . "/" . API_TOKEN . "/";
    return makeApiRequest($endpoint);
}

/**
 * Get download link from sources
 * 
 * @param array $sources Array of source objects
 * @return string|null Download URL or null if not found
 */
function getDownloadLink($sources) {
    if (!is_array($sources)) {
        return null;
    }
    
    foreach ($sources as $source) {
        if (isset($source['url']) && strpos($source['url'], 'cybervynx.com/e/') !== false) {
            return str_replace('/e/', '/f/', $source['url']);
        }
    }
    
    return null;
}
?>