<?php
/*
Plugin Name: Distributed Admin Theme
Description: Custom admin theming for Designed Online.
Version: 1.10
Author: Cameron Coop
Text Domain: distributed-admin-theme
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('login_init', function() {
    if (get_option('distributed_enable_sgs_token') == '1') {
        if (!isset($_GET['sgs-token']) || empty($_GET['sgs-token'])) {
            add_filter('login_message', '__return_empty_string');
            add_filter('login_form_middle', '__return_empty_string');
            add_action('login_form', function() {
                $tagline = esc_html(get_option('distributed_tagline', ''));
                echo '<style>
                    form#loginform {margin: 0;padding: 0;}
                    div#login {position: relative;display: grid;height: 100%;align-content: center;justify-content: center;align-items: center;padding: 0;}
                    #login_error{display:none;}
                    .login form label, .login form input, .user-pass-wrap {
                        display:none !important;visibility:hidden !important;
                    }
                </style>';
                
                echo '<div style="text-align:center;border-radius:8px;background:#5eb39c;">';
                echo '<p style="font-size:15px; color:#fff; max-width:600px; margin:auto;padding:5%;">';
                echo 'You can only access the admin area by using the authenticated URL.<br><br>';
                echo 'Please contact your <a style="color: #79ffdf !important;display: inline;">site administrator</a> if you need access but do not have the authorised link.';
                echo '</p>';
                echo '</div>';
                
                // ✅ Append the tagline if set
                if (!empty($tagline)) {
                    echo '<p style="font-size: 12px;color: #ffffff;margin-top: 20px;text-align: center;">' . $tagline . '</p>';
                }
                
                // ✅ Stop the form from rendering
                exit;
            });
        }
    }
});


// Change the login logo link URL
add_filter('login_headerurl', function() {
    return esc_url(get_option('distributed_logo_login_link', 'https://distributedigital.co.uk'));
});

// Change the login logo title attribute
add_filter('login_headertext', function() {
    return get_bloginfo('name');
});


add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'settings_page_distributed-admin-theme') wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('distributed-admin-css', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    wp_add_inline_style('distributed-admin-css', distributed_get_custom_styles());
    wp_enqueue_script('wp-color-picker');
});

add_action('login_enqueue_scripts', function() {
    wp_enqueue_style('distributed-admin-css', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    wp_add_inline_style('distributed-admin-css', distributed_get_custom_styles());
});

add_action('wp_enqueue_scripts', function() {
    if (is_user_logged_in()) {
        wp_enqueue_style('distributed-admin-css', plugin_dir_url(__FILE__) . 'css/admin-style.css');
        wp_add_inline_style('distributed-admin-css', distributed_get_custom_styles());
    }
});

add_action('wp_head', function() {
    if (is_user_logged_in()) {
        echo '<style>' . distributed_get_custom_styles() . '</style>';
    }
});

add_action('admin_menu', function() {
    add_options_page('Admin Theme Settings', 'Admin Theme', 'manage_options', 'distributed-admin-theme', 'distributed_admin_settings_page');
});

add_action('admin_init', function() {
    $fields = [
        'distributed_logo_admin', 'distributed_logo_admin_link', 'distributed_logo_login', 'distributed_logo_login_link',
        'distributed_primary_color', 'distributed_background_color', 'distributed_background_login_color',
        'distributed_form_background_login_color','distributed_form_headings_login_color',
        'distributed_button_color', 'distributed_menu_color', 'distributed_link_color',
        'distributed_active_color', 'distributed_tagline','distributed_enable_sgs_token'
    ];
    foreach ($fields as $field) register_setting('distributed_admin_settings', $field, ['sanitize_callback' => 'sanitize_text_field']);
});

function distributed_admin_settings_page() {
    include plugin_dir_path(__FILE__) . 'templates/settings-page.php';
}

function distributed_get_admin_logo() {
    $id = get_option('distributed_logo_admin');
    return $id ? wp_get_attachment_url($id) : 'https://distributedigital.co.uk/wp-content/uploads/2022/02/Fav-V2-64-white.png';
}

function distributed_get_login_logo() {
    $id = get_option('distributed_logo_login');
    return $id ? wp_get_attachment_url($id) : 'https://distributedigital.co.uk/wp-content/uploads/2022/02/DD-Logo-White.png';
}

function distributed_get_custom_styles() {
    $primary = get_option('distributed_primary_color', '#1f2933');
    $bg = get_option('distributed_background_color', '#ededed');
    $bg_login = get_option('distributed_background_login_color', '#1f2933');
    $form_bg_login = get_option('distributed_form_background_login_color', '#1f2933');
    $form_login_headings = get_option('distributed_form_headings_login_color', '#ffffff');
    $btn = get_option('distributed_button_color', '#17b897');
    $menu_color = get_option('distributed_menu_color', '#1f2933');
    $link_color = get_option('distributed_link_color', '#ffffff');
    $active_color = get_option('distributed_active_color', '#17b897');
    $tagline = esc_html(get_option('distributed_tagline', ''));

    $admin_logo = distributed_get_admin_logo();
    $login_logo = distributed_get_login_logo();

    ob_start(); ?>
    form#loginform {
    background: <?= esc_url($form_bg_login); ?> !important;
    color: <?= esc_url($form_login_headings); ?> !important;
    border: 0;
    }
    
    body.wp-admin { background-color: <?= esc_attr($bg); ?> !important; }
    .wp-core-ui .button-primary { background-color: <?= esc_attr($btn); ?> !important; border-color: <?= esc_attr($btn); ?> !important; }

    /* Admin Logo */
    #wp-admin-bar-wp-logo > .ab-item .ab-icon {
        background: url('<?= esc_url($admin_logo); ?>') no-repeat center !important;
        background-size: contain !important;
        color: transparent !important;
    }
    #wp-admin-bar-wp-logo .ab-icon:before {
        background: url('<?= esc_url($admin_logo); ?>') no-repeat center !important;
        background-size: contain !important;
        color: transparent !important;
        content: "" !important;
        width: 20px !important;
        height: 20px !important;
        display: block;
    }

    /* Login Page */
    body.login { background-color: <?= esc_attr($bg_login); ?> !important; }
    .login h1 a {
        background-image: url('<?= esc_url($login_logo); ?>') !important;
        background-size: contain !important;
        width: 100% !important;
        height: 84px !important;
    }

    /* Admin Sidebar Menu */
    #adminmenu, .wp-submenu, #adminmenuback, #adminmenuwrap {
        background-color: <?= esc_attr($menu_color); ?> !important;
    }
    #adminmenu .wp-has-current-submenu a.wp-has-current-submenu,
    #adminmenu a:hover {
        background-color: <?= esc_attr($active_color); ?> !important;
    }
    #adminmenu a, #adminmenu .wp-submenu a, #adminmenu .wp-submenu-head, #adminmenu li a {
        color: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu a:hover, #adminmenu .wp-submenu a:hover {
        color: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu .wp-submenu .wp-submenu-head {
        background-color: <?= esc_attr($menu_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu .wp-submenu a {
        background-color: <?= esc_attr($menu_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu .wp-submenu a:hover {
        background-color: <?= esc_attr($active_color); ?> !important;
    }
    #adminmenu .wp-submenu li.current a {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
        font-weight:normal !important;
    }
    .toplevel_page_elementor {
    background: #e4b0f9 !important;
    }
    /*.toplevel_page_elementor > .wp-menu-name {
    color: #28252b !important;
    }
    .wp-has-current-submenu .toplevel_page_elementor > .wp-menu-name {
    color: #fff !important;
    }
    .toplevel_page_elementor div.wp-menu-image:before {
    color: #232323 !important;
    }
    .toplevel_page_elementor #adminmenu .wp-menu-image:before, .toplevel_page_elementor #adminmenu .wp-menu-image svg {
    color: #ffffff !important;
    fill: #ffffff !important;
    }*/
    form#loginform {margin: 0;padding: 0;}
    div#login {position: relative;display: grid;height: 100%;align-content: center;justify-content: center;align-items: center;padding: 0;}

    .login form .input, .login input[type=password], .login input[type=text] {
    font-size: 16px;
    padding: 14px;
    text-align:center;
    }

    p.forgetmenot {
    display: none;
    }

    .privacy-policy-page-link {
    display: none;
    }

    input#wp-submit {
    width: 100%;
    }

    .login label {
    text-align: center;
    display: block !important;
    }

    /* Top Admin Bar - Backend & Frontend */
    body.admin-bar #wpadminbar {
        background-color: <?= esc_attr($primary); ?> !important;
    }
    div#login a {
    color: #fff !important;
    text-align: center !important;
    width: 100% !important;
    display: block;
    }
    form#language-switcher {
    display: none;
    }
    #wpadminbar .ab-top-menu>li.hover>.ab-item, #wpadminbar.nojq .quicklinks .ab-top-menu>li>.ab-item:focus, #wpadminbar:not(.mobile) .ab-top-menu>li:hover>.ab-item, #wpadminbar:not(.mobile) .ab-top-menu>li>.ab-item:focus{
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }
    /* Force parent menu items to be styled, including hover/open */
    body.admin-bar #wpadminbar .menupop > .ab-item {
        background-color: <?= esc_attr($menu_color); ?> !important;
    }
    #wp-admin-bar-elementor_edit_page .elementor-edit-link-title {
    line-height: 1.8em;
    }
    #wp-admin-bar-elementor_edit_page .elementor-edit-link-type {
    margin-bottom: 4px !important;
    margin-top: 4px !important;
    }
    #wpadminbar li:hover .ab-item:before {
        color: <?= esc_attr($link_color); ?> !important;
    }
    #wp-admin-bar-rank-math:hover .rank-math-icon svg {
        fill: <?= esc_attr($link_color); ?> !important;
    }
    #wpadminbar:not(.mobile)>#wp-toolbar a:focus span.ab-label, #wpadminbar:not(.mobile)>#wp-toolbar li:hover span.ab-label, #wpadminbar>#wp-toolbar li.hover span.ab-label {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    #wpadminbar li .ab-item:focus .ab-icon:before, #wpadminbar li a:focus .ab-icon:before, #wpadminbar li.hover .ab-icon:before, #wpadminbar li:hover .ab-icon:before {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    .wp-admin #wpadminbar #wp-admin-bar-site-name:hover >.ab-item:before {
        color: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu:hover > div.wp-menu-image.svg {
        display:none;
    }

    body.admin-bar #wpadminbar .menupop.hover > .ab-item,
    body.admin-bar #wpadminbar .menupop:hover > .ab-item {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    body.admin-bar #wpadminbar .quicklinks .ab-item,
    body.admin-bar #wpadminbar .ab-item:focus,
    body.admin-bar #wpadminbar .ab-item:hover {
        color: <?= esc_attr($link_color); ?> !important;
    }

    /* Admin Bar Dropdowns */
    body.admin-bar #wpadminbar .ab-sub-wrapper {
        background-color: <?= esc_attr($menu_color); ?> !important;
    }
    body.admin-bar #wpadminbar .ab-sub-wrapper .ab-item {
        color: <?= esc_attr($link_color); ?> !important;
    }
    body.admin-bar #wpadminbar .ab-sub-wrapper .ab-item:hover {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    /* Icon color adjustments */
    #adminmenu .wp-menu-image:before,
    #adminmenu .wp-menu-image svg {
        color: <?= esc_attr($link_color); ?> !important;
        fill: <?= esc_attr($link_color); ?> !important;
    }
    #adminmenu li.menu-top:hover .wp-menu-image:before,
    #adminmenu li.menu-top:hover .wp-menu-image svg,
    #adminmenu li.wp-has-current-submenu .wp-menu-image:before,
    #adminmenu li.wp-has-current-submenu .wp-menu-image svg {
        color: <?= esc_attr($link_color); ?> !important;
        fill: <?= esc_attr($link_color); ?> !important;
    }
    /* Specifically target the site name in the admin bar */
    body.admin-bar #wpadminbar #wp-admin-bar-site-name > .ab-item {
        background-color: <?= esc_attr($primary); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    body.admin-bar #wpadminbar #wp-admin-bar-site-name > .ab-item:hover {
        background-color: <?= esc_attr($active_color); ?> !important;
        color: <?= esc_attr($link_color); ?> !important;
    }

    button.button.button-secondary.wp-hide-pw.hide-if-no-js {
    align-self: anchor-center;
    padding: 0;
    position: absolute;
    top: -15px;
    }   
    /*
    #wpadminbar, #wpadminbar .ab-item, #wpadminbar .ab-sub-wrapper, #wpadminbar .ab-submenu .ab-item {
    transition: all 0.25s ease-in-out !important;
    }
    */




    <?php if ($tagline): ?>
    #backtoblog:after {
        content: "<?= $tagline; ?>";
        display: block;
        text-align: center;
        color: #fff;
        margin-top: 20px;
        font-size: 13px;
    }
    <?php endif;
    return ob_get_clean();
}

function distributed_render_login_preview() {
    $login_logo = distributed_get_login_logo();
    $bg_login = get_option('distributed_background_login_color', '#ffffff');
    $btn = get_option('distributed_button_color', '#00BCC8');
    $tagline = esc_html(get_option('distributed_tagline', 'Your tagline preview here.'));
    ob_start(); ?>
    <div style="padding:20px;background:<?= esc_attr($bg_login); ?>;color:#fff;width:300px;">
        <div style="text-align:center;">
            <img src="<?= esc_url($login_logo); ?>" style="max-width:100%;height:auto;margin-bottom:20px;">
        </div>
        <form>
            <input type="text" placeholder="" style="width:100%;margin-bottom:10px;padding:10px;">
            <input type="test" placeholder="" style="width:100%;margin-bottom:10px;padding:10px;">
            <button type="button" style="width:100%;padding:10px;background:<?= esc_attr($btn); ?>;border:none;color:#fff;">Login</button>
        </form>
        <p style="margin-top:15px;text-align:center;"><?= $tagline; ?></p>
    </div>
    <?php
    return ob_get_clean();
}

// === GitHub updater with changelog support ===

add_filter( 'pre_set_site_transient_update_plugins', function ( $transient ) {
    if ( empty( $transient->checked ) ) {
        return $transient;
    }

    $plugin_file = plugin_basename( __FILE__ ); // distributed-admin-theme/distributed-admin-theme.php
    $installed_data = get_file_data( __FILE__, [ 'Version' => 'Version' ] );
    $installed_version = isset( $installed_data['Version'] ) ? $installed_data['Version'] : '0';

    // GitHub repo info
    $owner = 'CameronCoopZA';
    $repo  = 'admin-theme-'; // your GitHub repo name

    $api_url = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";

    $response = wp_remote_get( $api_url, [
        'headers' => [
            'Accept'     => 'application/vnd.github+json',
            'User-Agent' => 'WordPress-Updater/1.0',
        ],
        'timeout' => 10,
    ] );

    if ( is_wp_error( $response ) ) {
        return $transient;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $code ) {
        return $transient;
    }

    $body = wp_remote_retrieve_body( $response );
    $release = json_decode( $body, true );
    if ( empty( $release ) || empty( $release['tag_name'] ) ) {
        return $transient;
    }

    $latest_tag = ltrim( $release['tag_name'], 'v' );
    if ( version_compare( $installed_version, $latest_tag, '>=' ) ) {
        return $transient; // already up to date
    }

    // Use GitHub's zipball for the tag
    $download_url = $release['zipball_url'];

    $plugin_slug = dirname( plugin_basename( __FILE__ ) );
    $plugin_key = plugin_basename( __FILE__ );

    $transient->response[ $plugin_key ] = (object) [
        'slug'        => $plugin_slug,
        'new_version' => $latest_tag,
        'url'         => $release['html_url'],
        'package'     => $download_url,
    ];

    // Store release body for later display (cache it temporarily)
    set_transient( 'distributed_admin_theme_last_release_body', $release['body'], HOUR_IN_SECONDS );

    return $transient;
} );

// Provide plugin info (including changelog) when the "View details" popup is clicked
add_filter( 'plugins_api', function ( $res, $action, $args ) {
    if ( 'plugin_information' !== $action ) {
        return $res;
    }

    if ( false === strpos( $args->slug, 'distributed-admin-theme' ) ) {
        return $res;
    }

    // Attempt to get cached release body
    $changelog = get_transient( 'distributed_admin_theme_last_release_body' );
    if ( false === $changelog ) {
        // Fallback: fetch fresh
        $owner = 'CameronCoopZA';
        $repo  = 'admin-theme-';
        $api_url = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
        $response = wp_remote_get( $api_url, [
            'headers' => [
                'Accept'     => 'application/vnd.github+json',
                'User-Agent' => 'WordPress-Updater/1.0',
            ],
            'timeout' => 10,
        ] );
        if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            $release = json_decode( $body, true );
            if ( ! empty( $release['body'] ) ) {
                $changelog = $release['body'];
                set_transient( 'distributed_admin_theme_last_release_body', $changelog, HOUR_IN_SECONDS );
            }
        }
    }

    // Build a minimal response object so WP shows version/changelog
    $res = new stdClass();
    $res->name = 'Distributed Admin Theme';
    $res->slug = 'distributed-admin-theme';
    $res->version = ''; // WP ignores this when showing update info
    $res->author = '<a href="https://designedonline.co.za">Designed Online</a>';
    $res->homepage = 'https://github.com/CameronCoopZA/admin-theme-/';
    $res->short_description = 'Custom admin theming for Designed Online.';
    $res->sections = [
        'changelog' => wp_kses_post( wpautop( $changelog ) ),
    ];
    $res->download_link = ''; // WP uses package from the update response

    return $res;
}, 10, 3 );
