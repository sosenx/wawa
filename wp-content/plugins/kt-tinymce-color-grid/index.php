<?php

/*
 * Plugin Name: TinyMCE Color Grid
 * Plugin URI: https://wordpress.org/plugins/kt-tinymce-color-grid
 * Description: Replace the TinyMCE color picker with a bigger and adjustable color grid. Create a central color palette for an uniform look'n'feel!
 * Version: 1.7.2
 * Author: Anagarika Daniel
 * Author URI: http://profiles.wordpress.org/kungtiger
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kt-tinymce-color-grid
 */

if (!function_exists('add_action')) {
    $options = '../../../wp-admin/options-general.php';
    if (file_exists($options)) {
        header("Location: $options?page=kt_tinymce_color_grid");
    } else {
        echo '<!DOCTYPE HTML>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>WordPress &rsaquo; Oops&hellip;</title>
    <style type="text/css">html{background: #F1F1F1}body{margin:50px auto 0;padding:1.2em 1.8em;max-width:450px;background:#FFF;color:#444;font:14px"Open Sans",sans-serif;border:1px solid #E5E5E5}h1{margin:0 0 .6em;font-size:21px;font-weight:400}p{margin:0;line-height:1.5}</style>
  </head>
  <body>
    <h1>Oops&hellip;</h1>
    <p>I\'m just a plugin, not much I can do when called directly.</p>
  </body>
</html>';
    }
    exit;
}

if (!class_exists('kt_TinyMCE_Color_Grid')) {

    class kt_TinyMCE_Color_Grid {

        const VERSION = 172;
        const KEY = 'kt_tinymce_color_grid';
        const NONCE = 'kt-tinymce-color-grid-save-editor';
        const MAP = 'kt_color_grid_map';
        const TYPE = 'kt_color_grid_type';
        const ROWS = 'kt_color_grid_rows';
        const COLS = 'kt_color_grid_cols';
        const LUMA = 'kt_color_grid_luma';
        const VISUAL = 'kt_color_grid_visual';
        const BLOCKS = 'kt_color_grid_blocks';
        const SIZE = 'kt_color_grid_block_size';
        const PALETTE = 'kt_color_grid_palette';
        const CUSTOMIZER = 'kt_color_grid_customizer';
        const ACTIVE_VERSION = 'kt_color_grid_version';
        const DEFAULT_SIZE = 5;
        const DEFAULT_ROWS = 9;
        const DEFAULT_COLS = 12;
        const DEFAULT_BLOCKS = 6;
        const DEFAULT_TYPE = 'rainbow';
        const DEFAULT_LUMA = 'natural';

        protected $picker;
        protected $wp_version = 0;
        protected $blocks = array(4, 6);
        protected $sizes = array(4, 5, 6);
        protected $columns = array(6, 12, 18);
        protected $rows = array(5, 7, 9, 11, 13);
        protected $types = array('default', 'rainbow', 'block');
        protected $lumas = array('linear', 'cubic', 'sine', 'natural');

        /**
         * Here we go ...
         *
         * Adds action and filter callbacks
         * @since 1.3
         * @global string $wp_version
         */
        public function __construct() {
            global $wp_version;
            $version = 0;
            if (preg_match('/^(\d+\.\d+)/', $wp_version, $version)) {
                $this->wp_version = floatval($version[1]);
            }

            add_action('admin_enqueue_scripts', array($this, 'enqueue_settings_scripts'));
            add_filter('plugin_action_links', array($this, 'add_action_link'), 10, 2);
            add_filter('tiny_mce_before_init', array($this, 'pass_rainbow_map'));
            add_action('after_wp_tiny_mce', array($this, 'print_tinymce_style'));
            add_action('admin_menu', array($this, 'add_settings_page'));
            add_action('plugins_loaded', array($this, 'init_plugin'));

            $this->update_plugin();
        }

        /**
         * Update procedures
         * @since 1.6
         */
        protected function update_plugin() {
            $version = get_option(self::ACTIVE_VERSION, 0);
            if ($version == self::VERSION) {
                return;
            }
            while ($version != self::VERSION) {
                switch ($version) {
                    case 0:
                        $sets = get_option('kt_color_grid_sets', array());
                        if ($sets) {
                            foreach ($sets as &$set) {
                                $set[0] = str_replace('#', '', $set[0]);
                            }
                            update_option('kt_color_grid_sets', $sets);
                        }
                        $version = 16;
                        break;
                    case 16:
                    case 161:
                        if (get_option('kt_color_grid_custom')) {
                            update_option('kt_color_grid_visual', '1');
                        }
                        $sets = get_option('kt_color_grid_sets', array());
                        if ($sets) {
                            update_option('kt_color_grid_palette', $sets);
                        }
                        delete_option('kt_color_grid_custom');
                        delete_option('kt_color_grid_sets');
                        $this->render_rainbow();
                        $version = 170;
                        break;
                    default:
                        $version = self::VERSION;
                }
            }
            update_option(self::ACTIVE_VERSION, self::VERSION);
        }

        /**
         * Init plugin
         * @since 1.4.4
         */
        public function init_plugin() {
            // load_plugin_textdomain is obsolete since WordPress 4.6
            if ($this->wp_version < 4.6) {
                load_plugin_textdomain('kt-tinymce-color-grid');
            }

            if (get_option(self::CUSTOMIZER)) {
                $fn = array($this, 'print_palette');
                add_action('admin_print_scripts', $fn);
                add_action('admin_print_footer_scripts', $fn);
                add_action('customize_controls_print_scripts', $fn);
                add_action('customize_controls_print_footer_scripts', $fn);
            }
        }

        /**
         * Enqueue JavaScript and CSS files
         * @since 1.3
         * @param string $hook Current page load hook
         */
        public function enqueue_settings_scripts($hook) {
            if ($hook == 'settings_page_' . self::KEY) {
                wp_enqueue_script(self::KEY, plugins_url("settings.js", __FILE__), array('jquery-ui-position', 'jquery-ui-sortable'), self::VERSION);
                wp_enqueue_style(self::KEY, plugins_url('settings.css', __FILE__), array('farbtastic'), self::VERSION);
                $picker = preg_replace(array('/\s*\n\s*/', '/"/'), array('', "'"), $this->picker);
                wp_localize_script(self::KEY, 'kt_TinyMCE_color_picker', $picker);
            }
        }

        /**
         * Central color palette integration
         * @since 1.7
         */
        public function print_palette() {
            static $printed = false;
            if ($printed || !wp_script_is('wp-color-picker', 'done')) {
                return;
            }
            $palette = get_option(self::PALETTE);
            if (!$palette) {
                $printed = true;
                return;
            }
            $printed = true;
            $colors = array();
            foreach ($palette as $set) {
                $colors[] = '"#' . esc_js($set[0]) . '"';
            }
            $colors = array_pad($colors, 6, '"transparent"');
            $colors = implode(',', $colors);
            echo '<script type="text/javascript">
jQuery.wp.wpColorPicker.prototype.options.palettes = [' . $colors . '];
</script>
';
        }

        /**
         * Add dynamic CSS for TinyMCE
         * @since 1.3
         */
        public function print_tinymce_style() {
            if (get_option(self::TYPE, self::DEFAULT_TYPE) == 'default') {
                return;
            }
            $map = get_option(self::MAP);
            if (!$map) {
                return;
            }
            list($map, $cols, $extra, $_, $rows) = $map;
            $cols += $extra;
            echo '<style type="text/css">.mce-grid{border-spacing:0!important}.mce-grid td{padding:0}.mce-grid td:last-child{padding-left:3px}.mce-grid td div{border-style:solid none none solid!important}' . ($extra ? ".mce-grid td:nth-child($extra){padding-right:3px}.mce-grid td:nth-child($extra) div{border-right-style:solid!important}" : '') . ".mce-grid td:nth-child($cols) div,.mce-grid td:last-child div{border-right-style:solid!important}.mce-grid tr:last-child td:nth-child($extra){padding:0 0 0 4px}.mce-grid tr:last-child td:nth-child($extra) div{border-right-style:none!important}.mce-grid tr:last-child td:last-child{padding:0 4px 0 0}.mce-grid tr:last-child td:last-child div{border-left-style:none!important}.mce-grid tr:nth-child($rows) td{padding-bottom:4px}.mce-grid tr:nth-child($rows) td div,.mce-grid tr:last-child td div{border-bottom-style:solid!important}</style>";
        }

        /**
         * Pass color map to TinyMCE
         * @since 1.3
         * @param array $init Wordpress' TinyMCE inits
         * @return array
         */
        public function pass_rainbow_map($init) {
            if (get_option(self::TYPE, self::DEFAULT_TYPE) == 'default') {
                return $init;
            }
            $map = get_option(self::MAP);
            if ($map) {
                list($map, $cols, $extra, $mono, $rows) = $map;
                $init['textcolor_map'] = $map;
                $init['textcolor_cols'] = $cols + $extra + $mono;
                $init['textcolor_rows'] = $rows;
            }
            return $init;
        }

        /**
         * Add a link to the plugin listing
         * @since 1.4
         * @param array $links Array holding HTML
         * @param string $file Current name of plugin file
         * @return array Modified array
         */
        public function add_action_link($links, $file) {
            if (plugin_basename($file) == plugin_basename(__FILE__)) {
                $links[] = '<a href="options-general.php?page=' . self::KEY . '&palette=1" class="dashicons-before dashicons-admin-settings" title="' . esc_attr__('Opens the settings page for this plugin', 'kt-tinymce-color-grid') . '"> ' . esc_html__('Color Palette', 'kt-tinymce-color-grid') . '</a>';
            }
            return $links;
        }

        /**
         * Add settings page to WordPress' admin menu
         * @since 1.3
         */
        public function add_settings_page() {
            $name = esc_html__('TinyMCE Color Grid', 'kt-tinymce-color-grid');
            $hook = add_options_page($name, $name, 'manage_options', self::KEY, array($this, 'print_settings_page'));
            add_action("load-$hook", array($this, 'init_settings_page'));
        }

        /**
         * Initialize settings page
         * @since 1.4.4
         */
        public function init_settings_page() {
            $this->save_settings();
            $this->add_help();

            $this->picker = vsprintf('<div class="picker" tabindex="2" aria-grabbed="false">
  <span class="sort hide-if-js">
    <button type="submit" name="kt_action" value="sort-%3$s-up" class="sort-up button" tabinde="3" title="%5$s">
      <i class="dashicons dashicons-arrow-up-alt2"></i>
      <span class="screen-reader-text">%4$s</span>
    </button>
    <button type="submit" name="kt_action" value="sort-%3$s-down" class="sort-down button" tabinde="3" title="%6$s">
      <i class="dashicons dashicons-arrow-down-alt2"></i>
      <span class="screen-reader-text">%5$s</span>
    </button>
  </span>
  <button type="button" class="color button hide-if-no-js" tabindex="3" aria-haspopup="true" aria-controls="kt_picker" aria-describedby="contextual-help-link" aria-label="%6$s">
    <span class="preview" style="background-color:%1$s"></span>
  </button>
  <span class="preview hide-if-js" style="background-color:%1$s"></span>
  <span class="screen-reader-text">%7$s</span>
  <input class="hex" type="text" name="kt_colors[]" tabindex="3" value="%1$s" maxlength="7" placeholder="#RRGGBB" autocomplete="off" aria-label="%7$s" pattern="\s*#?([a-fA-F0-9]{3}){1,2}\s*" required="required" title="%11$s" />
  <span class="screen-reader-text">%9$s</span>
  <input class="name" type="text" name="kt_names[]" value="%2$s" tabindex="3" placeholder="%8$s" aria-label="%10$s" />
  <button type="submit" name="kt_action" value="remove-%3$s" tabindex="3" class="remove button">
    <i class="dashicons dashicons-trash"></i>
    <span class="screen-reader-text">%10$s</span>
  </button>
</div>', array(
                '%1$s', '%2$s', '%3$s',
                esc_html__('Move up', 'kt-tinymce-color-grid'),
                esc_html__('Move down', 'kt-tinymce-color-grid'),
                esc_attr__('Color Picker', 'kt-tinymce-color-grid'),
                esc_attr__('Hexadecimal Color', 'kt-tinymce-color-grid'),
                esc_attr__('Unnamed Color', 'kt-tinymce-color-grid'),
                esc_attr__('Name of Color', 'kt-tinymce-color-grid'),
                esc_html__('Delete', 'kt-tinymce-color-grid'),
                esc_attr__('Three hexadecimal numbers between 00 and FF', 'kt-tinymce-color-grid')
            ));
        }

        /**
         * Add help to settings page
         * @since 1.7
         */
        protected function add_help() {
            $screen = get_current_screen();
            $screen->add_help_tab(array(
                'id' => 'grid',
                'title' => __('Color Grid', 'kt-tinymce-color-grid'),
                'content' => '
<p>' . __("<strong>Default</strong> leaves TinyMCE's color picker untouched.", 'kt-tinymce-color-grid') . '</p>
<p>' . __("<strong>Rainbow</strong> takes hue and lightness components from the HSL space and thus creates a rainbow. The <strong>Luminescence</strong> option controls how the lightness for each hue is spread.", 'kt-tinymce-color-grid') . '</p>
<p>' . __("<strong>Blocks</strong> takes sections from the RGB cube and places them next to one another. <strong>Block Count</strong> controls how many sections are taken, and <strong>Block Size</strong> determines their size.", 'kt-tinymce-color-grid') . '</p>'
            ));
            $screen->add_help_tab(array(
                'id' => 'palette',
                'title' => __('Central Palette', 'kt-tinymce-color-grid'),
                'content' => '
<p>' . __('You can create a color palette and include it to the Visual Editor and/or the Theme Customizer.', 'kt-tinymce-color-grid') . '</p>
<p>' . __('<strong>Add to Visual Editor</strong> adds the palette to the color picker of the text editor of posts and pages. This only works if you choose a color grid other than <strong>Default</strong>.', 'kt-tinymce-color-grid') . '</p>
<p>' . __("<strong>Add to Theme Customizer</strong> makes the palette available to the color picker of the Theme Customizer. This works by altering WordPress' color picker so every plugin using it receives the palette as well.", 'kt-tinymce-color-grid') . '</p>'
            ));
            $screen->add_help_tab(array(
                'id' => 'aria',
                'title' => __('Accessibility', 'kt-tinymce-color-grid'),
                'content' => '
<p>' . __('The palette editor consists of a toolbar and a list of entries. Every entry has a color picker, two text fields &mdash; one holding a hexadecimal representation of the color, and one for the name of the entry &mdash; and lastly a button to remove the entry.', 'kt-tinymce-color-grid') . '</p>
<p>' . __('You can reorder an entry by pressing the <strong>page</strong> keys. To delete an entry press the <strong>delete</strong> or <strong>backspace</strong> key. If a color picker has focus use the <strong>arrow</strong> keys, and <strong>plus</strong> and <strong>minus</strong> to change the color.', 'kt-tinymce-color-grid') . '</p>'
            ));
            $screen->set_help_sidebar('
<p><strong>' . esc_html__('For more information:', 'kt-tinymce-color-grid') . '</strong></p>
<p><a href="https://wordpress.org/plugins/kt-tinymce-color-grid" target="_blank">' . esc_html__('Visit plugin site', 'kt-tinymce-color-grid') . '</a></p>
<p><a href="https://wordpress.org/support/plugin/kt-tinymce-color-grid" target="_blank">' . esc_html__('Support Forums', 'kt-tinymce-color-grid') . '</a></p>');
        }

        /**
         * Sanitize and saves settings
         * @since 1.7
         */
        protected function save_settings() {
            if (!wp_verify_nonce($this->get('kt_settings_nonce'), self::NONCE)) {
                return;
            }

            $visual = $this->get('kt_visual') ? '1' : false;
            update_option(self::VISUAL, $visual);

            $customizer = $this->get('kt_customizer') ? '1' : false;
            update_option(self::CUSTOMIZER, $customizer);

            $palette = array();
            $colors = $this->get('kt_colors', array());
            $names = $this->get('kt_names', array());
            foreach ($names as $i => $name) {
                $color = $this->sanitize_color($colors[$i]);
                if ($color) {
                    $name = sanitize_text_field(stripslashes($name));
                    $palette[] = array($color, $name);
                }
            }
            $m = null;
            $l = count($palette);
            $action = $this->get('kt_action');
            if ($action == 'add') {
                $palette[] = array('000000', '');
            } else if ($l > 0 && preg_match('/remove-(\d+)/', $action, $m) && key_exists($m[1], $palette)) {
                array_splice($palette, $m[1], 1);
            } else if ($l > 1 && preg_match('/sort-(\d+)-(up|down)/', $action, $m) && key_exists($m[1], $palette)) {
                $i = $j = $m[1];
                if ($m[2] == 'up' && $i > 0) {
                    $j = $i - 1;
                } else if ($m[2] == 'down' && $i < ($l - 1)) {
                    $j = $i + 1;
                }
                if ($i != $j) {
                    $temp = $palette[$i];
                    $palette[$i] = $palette[$j];
                    $palette[$j] = $temp;
                }
            }
            update_option(self::PALETTE, $palette);

            $this->set('kt_type', $this->types, self::TYPE, self::DEFAULT_TYPE);
            $this->set('kt_rows', $this->rows, self::ROWS, self::DEFAULT_ROWS);
            $this->set('kt_cols', $this->columns, self::COLS, self::DEFAULT_COLS);
            $this->set('kt_luma', $this->lumas, self::LUMA, self::DEFAULT_LUMA);
            $this->set('kt_blocks', $this->blocks, self::BLOCKS, self::DEFAULT_BLOCKS);
            $this->set('kt_size', $this->sizes, self::SIZE, self::DEFAULT_SIZE);

            $this->render_map();

            wp_redirect(add_query_arg('updated', $action == 'save' ? '1' : false));
            exit;
        }

        /**
         * Pass a HTTP request value through a filter and stores it as option
         * @since 1.7
         * @param string $key
         * @param array $constrain
         * @param string $option
         * @param mixed $default
         */
        protected function set($key, $constrain, $option, $default) {
            $value = $this->get($key, $default);
            $value = in_array($value, $constrain) ? $value : $default;
            update_option($option, $value);
        }

        /**
         * Sanitize a string to RRGGBB
         * @since 1.4
         * @param string $string String to be checked
         * @return string|boolean Returns a color of RRGGBB or false on failure
         */
        protected function sanitize_color($string) {
            $string = strtoupper($string);
            $match = null;
            if (preg_match('/([0-9A-F]{6}|[0-9A-F]{3})/', $string, $match)) {
                if (strlen($match[1]) == 3) {
                    return preg_replace('/[0-9A-F]/', '\1\1', $match[1]);
                }
                return $match[1];
            }
            return false;
        }

        /**
         * Renders color map
         * @since 1.7
         */
        protected function render_map() {
            switch (get_option(self::TYPE, self::DEFAULT_TYPE)) {
                case 'rainbow': $this->render_rainbow();
                    break;
                case 'block': $this->render_blocks();
                    break;
            }
        }

        /**
         * Chunk palette into columns of constant size
         * @since 1.7
         * @param type $rows
         * @return array
         */
        protected function prepare_palette($rows) {
            $palette = array();
            if (get_option(self::VISUAL)) {
                $palette = get_option(self::PALETTE, array());
                if (count($palette)) {
                    $palette = array_chunk($palette, $rows);
                    $last = count($palette) - 1;
                    $padded = array_pad($palette[$last], $rows, array('FFFFFF', ''));
                    $palette[$last] = $padded;
                }
            }
            return $palette;
        }

        /**
         * Add a row from the palette to the color map
         * @since 1.7
         * @param array $map passed by reference
         * @param array $palette passed by reference
         * @param int $row
         */
        protected function add_palette(&$map, &$palette, $row) {
            $cols = count($palette);
            for ($col = 0; $col < $cols; $col++) {
                $color = $palette[$col][$row];
                list($color, $name) = array_map('esc_js', $color);
                $map[] = '"' . $color . '","' . $name . '"';
            }
        }

        /**
         * Add a monocrome/grayscale color to the color map
         * @since 1.7
         * @param array $map passed by reference
         * @param int $row
         * @param int $rows
         */
        protected function add_monocroma(&$map, $row, $rows) {
            if ($row == $rows - 1) {
                return;
            }
            $x = $this->float2hex($row / ($rows - 2));
            $map[] = '"' . "$x$x$x" . '",""';
        }

        /**
         * Render TinyMCE block color map
         * @since 1.7
         */
        protected function render_blocks() {
            $blocks = get_option(self::BLOCKS, self::DEFAULT_BLOCKS);
            $size = get_option(self::SIZE, self::DEFAULT_SIZE);
            $groups = 2;
            $rows = $size * $groups;
            $per_group = $blocks / $groups;
            $cols = $size * $per_group;
            $chunks = $square = array();
            for ($i = 0, $step = 1 / ($size - 1); $i < $size; $i++) {
                $square[] = $this->float2hex($i * $step);
            }
            for ($i = 0, $step = 1 / ($blocks - 1); $i < $blocks; $i++) {
                $chunks[] = $this->float2hex($i * $step);
            }
            $palette = $this->prepare_palette($rows);
            $map = array();
            for ($row = 0; $row < $rows; $row++) {
                $this->add_palette($map, $palette, $row);

                $b = $square[$row % $size];
                $shift = floor($row / $size) * $per_group;
                for ($col = 0; $col < $cols; $col++) {
                    $g = $square[$col % $size];
                    $r = $chunks[floor($col / $size) + $shift];
                    $map[] = '"' . "$r$g$b" . '",""';
                }

                $this->add_monocroma($map, $row, $rows);
            }
            $map = '[' . implode(',', $map) . ']';
            $map = array($map, $cols, count($palette), 1, $rows);
            update_option(self::MAP, $map);
        }

        /**
         * Render TinyMCE rainbow color map
         * @since 1.7
         */
        protected function render_rainbow() {
            $cols = get_option(self::COLS, self::DEFAULT_COLS);
            $rows = get_option(self::ROWS, self::DEFAULT_ROWS);
            $luma = get_option(self::LUMA, self::DEFAULT_LUMA);
            $palette = $this->prepare_palette($rows);

            $rgb = array();
            for ($i = 0; $i < $cols; $i++) {
                $rgb[] = $this->hue2rgb($i / $cols);
            }

            $map = array();
            for ($row = 0; $row < $rows; $row++) {
                $this->add_palette($map, $palette, $row);

                $p = 2 * ($row + 1) / ($rows + 1) - 1;
                $luminecence = $this->transform($p, $luma);
                for ($col = 0; $col < $cols; $col++) {
                    $hex = $this->apply($luminecence, $rgb[$col]);
                    $map[] = '"' . $hex . '",""';
                }

                $this->add_monocroma($map, $row, $rows);
            }
            $map = '[' . implode(',', $map) . ']';
            $map = array($map, $cols, count($palette), 1, $rows);
            update_option(self::MAP, $map);
        }

        /**
         * Return a RGB vector for a hue
         * @since 1.7
         * @param float $hue [0..1]
         * @return array [0..1, 0..1, 0..1]
         */
        protected function hue2rgb($hue) {
            $hue *= 6;
            if ($hue < 1) {
                return array(1, $hue, 0);
            } else if (--$hue < 1) {
                return array(1 - $hue, 1, 0);
            } else if (--$hue < 1) {
                return array(0, 1, $hue);
            } else if (--$hue < 1) {
                return array(0, 1 - $hue, 1);
            } else if (--$hue < 1) {
                return array($hue, 0, 1);
            }
            $hue -= 1;
            return array(1, 0, 1 - $hue);
        }

        /**
         * Apply a transformation on a linear float
         * @since 1.7
         * @param float $p [-1..1]
         * @param string $type [linear|sine|cubic|natural]
         * @return float [-1..1]
         */
        protected function transform($p, $type) {
            switch ($type) {
                case 'sine': return $this->sine($p);
                case 'cubic': return $this->cubic($p);
                case 'natural': return $p < 0 ? $this->sine($p) : $this->cubic($p);
            }
            return $p;
        }

        /**
         * Apply a sine transformation on a linear luma value.
         * @since 1.7
         * @param float $p [-1..1]
         * @return float [-1..1]
         */
        protected function sine($p) {
            return $p < 0 ? sin((1 - $p) * M_PI_2) - 1 : sin($p * M_PI_2);
        }

        /**
         * Apply a cubic transformation on a linear luma value.
         * @since 1.7
         * @param float $p [-1..1]
         * @return float [-1..1]
         */
        protected function cubic($p) {
            return $p < 0 ? pow(($p + 1), 8 / 11) - 1 : pow($p, 8 / 13);
        }

        /**
         * Apply a luma transformation on a RGB vector and returns it as HEX string
         * @since 1.7
         * @param float $luma [-1..1]
         * @param array $rgb RGB vector
         * @return string
         */
        protected function apply($luma, $rgb) {
            $hex = '';
            foreach ($rgb as $c) {
                if ($luma < 0) {
                    $c += $c * $luma;
                } else if ($luma > 0) {
                    $c = $c == 0 ? $luma : $c + (1 - $c) * $luma;
                    $c = max(0, min($c, 1));
                }
                $hex.= $this->float2hex($c);
            }
            return $hex;
        }

        /**
         * Convert a float into an HEX string
         * @since 1.7
         * @param float $p [0..1]
         * @return string
         */
        protected function float2hex($p) {
            $s = dechex($p * 255);
            return (strlen($s) == 1 ? '0' : '') . $s;
        }

        /**
         * Render settings page
         * @since 1.3
         */
        public function print_settings_page() {
            $head = $this->wp_version >= 4.3 ? 'h1' : 'h2';
            $nonce_field = wp_nonce_field(self::NONCE, 'kt_settings_nonce', false, false);

            $_type = get_option(self::TYPE, self::DEFAULT_TYPE);
            $_visual = get_option(self::VISUAL);
            $_customizer = get_option(self::CUSTOMIZER);
            if (!$_visual && !$_customizer && $this->get('palette')) {
                if ($_type == 'default') {
                    $_customizer = true;
                } else {
                    $_visual = true;
                }
            }
            $_cols = get_option(self::COLS, self::DEFAULT_COLS);
            $_rows = get_option(self::ROWS, self::DEFAULT_ROWS);
            $_luma = get_option(self::LUMA, self::DEFAULT_LUMA);
            $_blocks = get_option(self::BLOCKS, self::DEFAULT_BLOCKS);
            $_size = get_option(self::SIZE, self::DEFAULT_SIZE);

            $type = array(
                'default' => __('Default', 'kt-tinymce-color-grid'),
                'rainbow' => __('Rainbow', 'kt-tinymce-color-grid'),
                'block' => __('Blocks', 'kt-tinymce-color-grid'),
            );
            $luma = array(
                'linear' => __('Linear', 'kt-tinymce-color-grid'),
                'cubic' => __('Cubic', 'kt-tinymce-color-grid'),
                'sine' => __('Sine', 'kt-tinymce-color-grid'),
                'natural' => __('Natural', 'kt-tinymce-color-grid'),
            );
            $size = array(
                4 => __('small', 'kt-tinymce-color-grid'),
                5 => __('medium', 'kt-tinymce-color-grid'),
                6 => __('big', 'kt-tinymce-color-grid'),
            );

            foreach ($type as $key => $label) {
                $id = "kt_type_$key";
                $checked = $_type == $key ? ' checked="checked"' : '';
                $label = esc_html($label);
                $type[$key] = "
            <input type='radio' id='$id' name='kt_type' value='$key'$checked />
            <label for='$id'>$label</label>";
            }
            $type = implode('<br/>', $type);
            $cols = $this->selectbox('kt_cols', $this->columns, $_cols);
            $rows = $this->selectbox('kt_rows', $this->rows, $_rows);
            $luma = $this->selectbox('kt_luma', $luma, $_luma);
            $blocks = $this->selectbox('kt_blocks', $this->blocks, $_blocks);
            $size = $this->selectbox('kt_size', $size, $_size);

            $picker = '';
            $palette = get_option(self::PALETTE, array());
            foreach ($palette as $i => $set) {
                list($color, $name) = array_map('esc_attr', $set);
                $picker .= sprintf($this->picker, "#$color", $name, $i);
            }

            $visual_disabled = $_type == 'default' ? ' disabled="disabled"' : '';
            $visual_checked = $_visual ? ' checked="checked"' : '';
            $customizer_checked = $_customizer ? ' checked="checked"' : '';
            $editor_hidden = $_visual || $_customizer ? '' : ' hidden';
            $aria_hidden = $_visual || $_customizer ? 'false' : 'true';

            $add_key = _x('A', 'accesskey for adding color', 'kt-tinymce-color-grid');
            $save_key = _x('S', 'accesskey for saving', 'kt-tinymce-color-grid');

            $save_label = $this->underline_accesskey(__('Save', 'kt-tinymce-color-grid'), $save_key);
            $picker_label = esc_attr__('Visual Color Picker', 'kt-tinymce-color-grid');
            $add_label = $this->underline_accesskey(__('Add Color', 'kt-tinymce-color-grid'), $add_key);
            $rows_label = esc_html__('Rows', 'kt-tinymce-color-grid');
            $cols_label = esc_html__('Columns', 'kt-tinymce-color-grid');
            $luma_label = esc_html__('Luminescence', 'kt-tinymce-color-grid');
            $blocks_label = esc_html__('Block Count', 'kt-tinymce-color-grid');
            $size_label = esc_html__('Block Size', 'kt-tinymce-color-grid');

            echo "
<div class='wrap'>
  <$head>" . esc_html__('Settings', 'kt-tinymce-color-grid') . " › " . esc_html__('TinyMCE Color Grid', 'kt-tinymce-color-grid') . "</$head>
  <form action='options-general.php?page=" . self::KEY . "' method='post'>
    <div class='metabox-holder'>
    $nonce_field
      <div class='postbox-container'>
        <div id='kt_palette_metabox' class='postbox'>
          <h2 class='hndle'>" . esc_html__('Central Palette', 'kt-tinymce-color-grid') . "</h2>
          <div class='inside'>
            <p>
              <input type='checkbox' id='kt_visual' name='kt_visual' tabindex='9' value='1'$visual_checked$visual_disabled />
              <label for='kt_visual'>" . esc_html__('Add to Visual Editor', 'kt-tinymce-color-grid') . "</label>
            </p>
            <p>
              <input type='checkbox' id='kt_customizer' name='kt_customizer' tabindex='10' value='1'$customizer_checked />
              <label for='kt_customizer'>" . esc_html__('Add to Theme Customizer', 'kt-tinymce-color-grid') . "</label>
            </p>
            <table id='kt_editor' aria-hidden='$aria_hidden' class='form-table$editor_hidden'>
              <tbody>
                <tr><td id='kt_toolbar' role='toolbar'>
                  <button id='kt_add' type='submit' tabindex='8' name='kt_action' value='add' class='button' aria-controls='kt_colors' accesskey='$add_key'>$add_label</button>
                </td></tr>
                <tr><td id='kt_colors' data-empty='" . esc_attr__('Palette is empty', 'kt-tinymce-color-grid') . "'>$picker</td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div id='kt_grid_metabox' class='postbox'>
          <h2 class='hndle'>" . esc_html__('Color Grid', 'kt-tinymce-color-grid') . "</h2>
          <div class='inside'>$type
            <p id='kt_rows_wrap'><label for='kt_rows'>$rows_label</label>$rows</p>
            <p id='kt_cols_wrap'><label for='kt_cols'>$cols_label</label>$cols</p>
            <p id='kt_luma_wrap'><label for='kt_luma'>$luma_label</label>$luma</p>
            <p id='kt_blocks_wrap'><label for='kt_blocks'>$blocks_label</label>$blocks</p>
            <p id='kt_size_wrap'><label for='kt_size'>$size_label</label>$size</p>
          </div>
        </div>
      </div>
    </div>
    <p class='submit'><button type='submit' id='kt_save' name='kt_action' value='save' tabindex='9' class='button button-primary' accesskey='$save_key'>$save_label</button></p>
  </form>
  <div id='kt_picker' class='hidden' aria-hidden='true' aria-label='$picker_label'></div>
</div>";
        }

        /**
         * Highlight an accesskey inside a translated string
         * @since 1.4.4
         * @param string $string Translated string
         * @param string $key Accesskey
         * @return string
         */
        protected function underline_accesskey($string, $key) {
            $pattern = '/(' . preg_quote($key, '/') . ')/i';
            return preg_replace($pattern, '<u>$1</u>', esc_html($string), 1);
        }

        /**
         * Generate HTML markup of a selectbox
         * @since 1.7
         * @param string $name
         * @param array $data
         * @param mixed $selected
         * @param bool $disabled
         * @return string
         */
        protected function selectbox($name, $data, $selected = null, $disabled = false) {
            $options = '';
            if (key($data) === 0) {
                $data = array_combine($data, $data);
            }
            foreach ($data as $value => $label) {
                $sel = $value == $selected ? ' selected="selected"' : '';
                $value = esc_attr($value);
                $label = esc_html($label);
                $options .= "<option value='$value'$sel>$label</option>";
            }
            $name = esc_attr($name);
            $disabled = $disabled ? ' disabled="disable"' : '';
            return "<select id='$name' name='$name'$disabled>$options</select>";
        }

        /**
         * Fetch a HTTP request value
         * @since 1.3
         * @param string $key Name of the value to fetch
         * @param mixed|null $default Default value if $key does not exist
         * @return mixed The value for $key or $default
         */
        protected function get($key, $default = null) {
            return key_exists($key, $_REQUEST) ? $_REQUEST[$key] : $default;
        }

    }

}

new kt_TinyMCE_Color_Grid();
