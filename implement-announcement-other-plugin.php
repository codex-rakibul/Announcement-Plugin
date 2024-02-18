<?php
// Announcement Implementation
add_action('wp_ajax_php_func_action', 'php_func_action'); // Add AJAX action for logged-in users
add_action('wp_ajax_nopriv_php_func_action', 'php_func_action'); // Add AJAX action for non-logged-in users

function get_announcement_api_data() {
    // Attempt to retrieve data from the transient
    $api_data = get_transient('announcement_api_data');

    // Remove the transient to force fetching fresh data
    delete_transient('announcement_api_data');

    // Fetch data from the external API
    $api_url = 'http://localhost/donik-wp/wp-json/custom-announcement-api/v1/all_announcement_post/';
    $response = wp_remote_get($api_url);

    if (is_wp_error($response)) {
        // Handle error if the API request fails
        return array();
    }

    // Parse and process the API response
    $api_data = json_decode(wp_remote_retrieve_body($response), true);

    // Set transient with a 1-day expiration (24 hours * 60 minutes * 60 seconds)
    set_transient('announcement_api_data', $api_data, 24 * 60 * 60);

    return $api_data;
}

$announcement_data = get_announcement_api_data();

function my_custom_admin_notice($announcement_post) {
    ?>
    <div class="notice announcement_notice announcement_notice_id<?php echo $announcement_post['ID'] ?> is-dismissible">
        <?php
        if ($announcement_post['show_logo']) {
            ?>
            <div class="announcement_logo">
                <img src="<?php echo esc_url($announcement_post['post_plugin_logo']) ?>" alt="logo">
            </div>
            <?php
        }
        ?>
        <div class="announcement_content">
            <?php
            if ($announcement_post['show_notice_title']) {
                ?>
                <h3><?php echo esc_html($announcement_post['post_title']); ?></h3>
                <?php
            }
            if ($announcement_post['show_notice_content']) {
                ?>
                <span><?php echo $announcement_post['announcement_notice']; ?></span>
                <?php
            }
            ?>
            <p class="submit">
                <?php
                if ($announcement_post['show_primary_button']) {
                    ?>
                    <a href="<?php echo $announcement_post['button_url']; ?>"
                       class="button-primary button-large"><?php echo $announcement_post['button_text']; ?></a>
                    <?php
                }
                if ($announcement_post['show_cancel_button']) {
                    ?>
                    <button id="cancelButton" onclick="click_me(this)" data-post-id="<?php echo $announcement_post['ID']; ?>"
                            class="cancel-button button-large button-secondary">
                        <?php echo $announcement_post['cancel_button_text']; ?>
                    </button>
                    <?php
                }
                ?>
            </p>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dismissButtons = document.querySelectorAll('.cancel-button');

            dismissButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the postId from the button's data attribute
                    var postId = button.getAttribute('data-post-id');

                    // Check if the postId matches the announcement ID
                    if (postId) {
                        var notice = document.querySelector('.announcement_notice_id' + postId);
                        if (notice) {
                            notice.style.display = 'none'; // Hide the notice
                        }
                    }
                });
            });
        });

        function click_me(button) {
            // Retrieve existing data from cookies
            const existingPostIds = getCookie('post_id') || []; // Get existing post IDs or initialize an empty array

            // Get the new post_id
            const newPostId = button.getAttribute('data-post-id');

            // Ensure existingPostIds is an array
            const updatedPostIds = Array.isArray(existingPostIds) ? existingPostIds : [];

            // Add the new post_id to the array
            updatedPostIds.push(newPostId);

            // Set the updated array in the 'post_id' cookie with a 1-day expiration
            setCookie('post_id', JSON.stringify(updatedPostIds), 1);

            // Simulate AJAX request
            jQuery.ajax({
                url: php_func_action.ajax_url,
                type: 'POST',
                data: {
                    action: 'php_func_action',
                    id: updatedPostIds,
                },
                success: function (response) {
                    // Handle success
                },
                error: function () {
                    console.log('Error retrieving product details.');
                }
            });
        }
        
        function setCookie(name, value, days) {
            var expires = '';
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = '; expires=' + date.toUTCString();
            }
            document.cookie = name + '=' + value + expires + '; path=/';
        }

        function getCookie(name) {
            const cookies = document.cookie.split('; ');

            for (const cookie of cookies) {
                const [cookieName, cookieValue] = cookie.split('=');

                if (cookieName === name) {
                    try {
                        return JSON.parse(cookieValue); // Parse the JSON string into an array
                    } catch (error) {
                        return null; // Handle JSON parsing error
                    }
                }
            }

            return null; // Cookie not found
        }
    </script>
    <?php
}

function php_func_action() {
    $single_post_id = $_POST['id'];
    setcookie("my_cookie", json_encode($single_post_id), time() + 60 * 60 * 24, "/"); 
    wp_enqueue_script('remove-localstorage-data', get_template_directory_uri() . './admin/js/remove-data.js', array('jquery'), '1.0', true);
    wp_die();
}


$all_cookie_values = [];
$cookieCodeExecuted = false;

// Hook the function to the 'admin_notices' action for each announcement
foreach ($announcement_data as $index => $announcement) {
    // Check if the cookie code has already been executed
    if (!$cookieCodeExecuted && isset($_COOKIE["my_cookie"])) {
        $cookieValue = $_COOKIE["my_cookie"];
        $all_cookie_values[] = $cookieValue;
        // Set the flag to true to ensure the code won't run again in subsequent iterations
        $cookieCodeExecuted = true;
    }

    $check_id = strval($announcement['ID']);

    // Decode the JSON string only if $all_cookie_values is not empty
    $array = !empty($all_cookie_values) ? json_decode($all_cookie_values[0], true) : [];

    if (!in_array($check_id, $array)) {
        if ($announcement['available_offering'] && $announcement['post_title'] !== "Switch Toolkit") {
            add_action('admin_notices', function () use ($announcement) {
                my_custom_admin_notice($announcement);
            });
        }
    }
}

