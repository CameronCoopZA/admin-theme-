<?php wp_enqueue_style('wp-color-picker'); ?>
<?php wp_enqueue_script('wp-color-picker'); ?>

<style>
.admin-theme-settings-container { display: flex; gap: 40px; align-items: flex-start; }
.iris-picker.iris-border { position: absolute; }
.admin-theme-settings-left { flex: 1; }
.admin-theme-settings-right { flex: 1; background: #f9f9f9; padding: 20px; border-radius: 8px; }
@media(max-width: 900px) { .admin-theme-settings-container { flex-direction: column; } }

.nav-tab-wrapper { margin-bottom: 20px; }
.tab-content { display: none; }
.tab-content.active { display: block; }
</style>

<div class="wrap">
  <h1>Distributed Admin Theme Settings</h1>

  <h2 class="nav-tab-wrapper">
    <a href="#tab-general" class="nav-tab nav-tab-active">General</a>
    <a href="#tab-colors" class="nav-tab">Colors</a>
  </h2>

  <div class="admin-theme-settings-container">
    <div class="admin-theme-settings-left">
      <form method="post" action="options.php">
        <?php settings_fields('distributed_admin_settings'); ?>
        <?php do_settings_sections('distributed_admin_settings'); ?>

        <div id="tab-general" class="tab-content active">
          <table class="form-table">
            <tr><th colspan="2"><h2>Admin Dashboard Logo</h2><p style="font-weight:400">The small logo top-left of WordPress dashboard</p></th></tr>
            <tr>
              <th>Logo</th>
              <td><?php include __DIR__ . '/uploader.php'; show_uploader('distributed_logo_admin'); ?></td>
            </tr>

            <tr><th colspan="2"><h2>Login Page Logo</h2><p style="font-weight:400">The logo for WordPress admin login page, as seen in the preview image.</p></th></tr>
            <tr>
              <th>Logo</th>
              <td><?php show_uploader('distributed_logo_login'); ?></td>
            </tr>
            <tr>
              <th>Logo Link</th>
              <td><input type="url" name="distributed_logo_login_link" value="<?php echo esc_attr(get_option('distributed_logo_login_link', 'https://distributedigital.co.uk')); ?>" class="regular-text"></td>
            </tr>

            <tr>
              <th>Require SGS Token on Login</th>
              <td>
                <label>
                  <input type="checkbox" name="distributed_enable_sgs_token" value="1" <?php checked(get_option('distributed_enable_sgs_token'), '1'); ?> />
                  Enable SGS Token Requirement
                </label>
              </td>
            </tr>

            <tr><th>Login Page Tagline</th><td><input type="text" name="distributed_tagline" value="<?php echo esc_attr(get_option('distributed_tagline')); ?>" class="regular-text"></td></tr>
          </table>
        </div>

        <div id="tab-colors" class="tab-content">
          <table class="form-table">
            <tr><th colspan="2"><h2>Colors</h2></th></tr>
            <?php
              $color_fields = [
                'Primary Color' => 'distributed_primary_color',
                'Admin Background Color' => 'distributed_background_color',
                'Login Background Color' => 'distributed_background_login_color',
                'Login Form Background Color' => 'distributed_form_background_login_color',
                'Login Headings Color' => 'distributed_form_headings_login_color',
                'Button Color' => 'distributed_button_color',
                'Menu & Submenu Color' => 'distributed_menu_color',
                'Menu & Submenu Link Color' => 'distributed_link_color',
                'Active/Hover Color' => 'distributed_active_color',
              ];
              foreach ($color_fields as $label => $name): ?>
                <tr>
                  <th><?php echo $label; ?></th>
                  <td><input type="text" name="<?php echo $name; ?>" value="<?php echo esc_attr(get_option($name, '#00BCC8')); ?>" class="color-field regular-text"></td>
                </tr>
            <?php endforeach; ?>
          </table>
        </div>

        <?php submit_button(); ?>
      </form>
    </div>

    <div class="admin-theme-settings-right">
      <h2>Login Page Preview</h2>
      <p>You must save before seeing the preview.</p>
      <?php if (function_exists('distributed_render_login_preview')) echo distributed_render_login_preview(); ?>
    </div>
  </div>
</div>

<script>
jQuery(document).ready(function($){
    $('.color-field').wpColorPicker();

    // Tab switching logic
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-content').removeClass('active');
        $($(this).attr('href')).addClass('active');
    });

    function initUploader(field) {
        $('#upload_' + field).click(function(e){
            e.preventDefault();
            var custom_uploader = wp.media({
                title: 'Select Image',
                button: { text: 'Use this image' },
                multiple: false
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $('#' + field).val(attachment.id);
                $('#preview_' + field).attr('src', attachment.url).show();
            }).open();
        });
    }

    initUploader('distributed_logo_admin');
    initUploader('distributed_logo_login');
});
</script>
