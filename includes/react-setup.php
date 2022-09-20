<?php

// code for loading react resources on react-page
add_action('init', function () {
    add_filter('script_loader_tag', function ($tag, $handle) {
        if (!preg_match('/^taste-react-/', $handle)) {
            return $tag;
        }

        return str_replace(' src', ' async defer src', $tag);
    }, 10, 2);

    add_action('wp_enqueue_scripts', function () {
        global $post;

        if (!property_exists($post, 'post_name') || !$post->post_name) {
            return;
        }
        $page_name = $post->post_name;

        if ('venue-react-portal.php' === basename(get_page_template())) {
            $manifest_file =TASTE_REACT_PORTAL_PLUGIN_BUILD.'asset-manifest.json';
            if (file_exists($manifest_file)) {
                $asset_manifest = json_decode(file_get_contents($manifest_file), true)['files'];
// var_dump($asset_manifest);
// echo '<h1>', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL . $asset_manifest['main.css'], "</h1>"; 
                if (isset($asset_manifest['main.css'])) {
                    wp_enqueue_style('taste-react-venue-portal-css', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL.$asset_manifest['main.css'], null);
                }

                wp_enqueue_script('taste-react-venue-portal-main-js', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL.$asset_manifest['main.js'], ['taste-react-venue-portal-runtime'], null, true);

                foreach ($asset_manifest as $key => $value) {
                    if ('main.js' === $key) {
                        wp_enqueue_script('taste-react-venue-portal-runtime', TASTE_REACT_PORTAL_PLUGIN_BUILD_URL.$value, [], null, true);
                    }
                    if (preg_match('@static/js/(.*)\.chunk\.js@', $key, $matches)) {
                        if ($matches && is_array($matches) && 2 === count($matches)) {
                            $name = 'taste-react-'.preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
                            wp_enqueue_script($name, TASTE_REACT_PORTAL_PLUGIN_BUILD_URL.$value, ['taste-react-venue-portal-main-js'], null, true);
                        }
                    }

                    if (preg_match('@static/css/(.*)\.chunk\.css@', $key, $matches)) {
                        if ($matches && is_array($matches) && 2 == count($matches)) {
                            $name = 'taste-react-'.preg_replace('/[^A-Za-z0-9_]/', '-', $matches[1]);
                            wp_enqueue_style($name, TASTE_REACT_PORTAL_PLUGIN_BUILD_URL.$value, ['taste-react-venue-portal-css'], null);
                        }
                    }
                }
            }
        }
        // die;
    });
});
