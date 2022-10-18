<?php

/**
 * CMB2 Theme Options
 * @version 0.1.0
 */
class radwinweb_Admin
{
    /**
     * Option key, and option page slug
     * @var string
     */
    protected $key = 'radwinweb_options';
    /**
     * Options page metabox id
     * @var string
     */
    protected $metabox_id = 'radwinweb_option_metabox';
    /**
     * Options Page title
     * @var string
     */
    protected $title = '';
    /**
     * Options Page hook
     * @var string
     */
    protected $options_page = '';
    /**
     * Holds an instance of the object
     *
     * @var radwinweb_Admin
     */
    protected static $instance = null;
    /**
     * Returns the running object
     *
     * @return radwinweb_Admin
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->hooks();
        }
        return self::$instance;
    }
    /**
     * Constructor
     * @since 0.1.0
     */
    protected function __construct()
    {
        // Set our title
        $this->title = __('تنظیمات سایت', 'سایت');
    }
    /**
     * Initiate our hooks
     * @since 0.1.0
     */
    public function hooks()
    {
        add_action('admin_init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_options_page'));
        add_action('cmb2_admin_init', array($this, 'add_options_page_metabox'));
    }
    /**
     * Register our setting to WP
     * @since  0.1.0
     */
    public function init()
    {
        register_setting($this->key, $this->key);
    }
    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page()
    {
        $this->options_page = add_menu_page($this->title, $this->title, 'manage_options', $this->key, array($this, 'admin_page_display'));
        // Include CMB CSS in the head to avoid FOUC
        add_action("admin_print_styles-{$this->options_page}", array('CMB2_hookup', 'enqueue_cmb_css'));
    }
    /**
     * Admin page markup. Mostly handled by CMB2
     * @since  0.1.0
     */
    public function admin_page_display()
    {
?>
        <div class="wrap cmb2-options-page <?php echo $this->key; ?>">
            <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
            <?php cmb2_metabox_form($this->metabox_id, $this->key); ?>
        </div>
<?php
    }
    /**
     * Add the options metabox to the array of metaboxes
     * @since  0.1.0
     */
    function add_options_page_metabox()
    {
        // hook in our save notices
        add_action("cmb2_save_options-page_fields_{$this->metabox_id}", array($this, 'settings_notices'), 10, 2);
        $cmb = new_cmb2_box(array(
            'id'         => $this->metabox_id,
            'hookup'     => false,
            'cmb_styles' => false,
            'show_on'    => array(
                // These are important, don't remove
                'key'   => 'options-page',
                'value' => array($this->key,)
            ),
        ));
// header area
        $group_field_id = $cmb->add_field(array(
            'id'          => 'header_section',
            'type'        => 'group',
            'repeatable'  => false, // use false if you want non-repeatable group
            'options'     => array(
                'group_title'   => 'هدرسایت',
            ),
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'لوگو',
            'id'   => 'logo',
            'type' => 'file',
      
        ));

        // features 
        $group_field_id = $cmb->add_field(array(
            'id'          => 'features',
            'type'        => 'group',
            'repeatable'  => false, // use false if you want non-repeatable group
            'options'     => array(
                'group_title'   => 'ویژگی های ما',
            ),
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان اصلی',
            'id'   => 'tlt_features',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عکس ویژگی (1)',
            'id'   => 'fea_1_pic',
            'type' => 'file',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان ویژگی (1)',
            'id'   => 'fea_1_tlt',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'توضیحات ویژگی (1)',
            'id'   => 'fea_1_tex',
            'type' => 'textarea',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عکس ویژگی (2)',
            'id'   => 'fea_2_pic',
            'type' => 'file',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان ویژگی (2)',
            'id'   => 'fea_2_tlt',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'توضیحات ویژگی (2)',
            'id'   => 'fea_2_tex',
            'type' => 'textarea',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عکس ویژگی (3)',
            'id'   => 'fea_3_pic',
            'type' => 'file',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان ویژگی (3)',
            'id'   => 'fea_3_tlt',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'توضیحات ویژگی (3)',
            'id'   => 'fea_3_tex',
            'type' => 'textarea',
        ));
        // 
        // footer area
        $group_field_id = $cmb->add_field(array(
            'id'          => 'footer_section',
            'type'        => 'group',
            'repeatable'  => false, // use false if you want non-repeatable group
            'options'     => array(
                'group_title'   => 'فوتر سایت',
            ),
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان منو 1',
            'id'   => 'title_1',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'لوگو سایت',
            'id'   => 'logo',
            'type' => 'file',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'توضیحات برند',
            'id'   => 'desc',
            'type' => 'textarea',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان منو 2',
            'id'   => 'title_2',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان 3',
            'id'   => 'title_3',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عنوان 4',
            'id'   => 'title_4',
            'type' => 'text',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عکس نماد 1',
            'id'   => 'namad1',
            'type' => 'file',
        ));
        $cmb->add_group_field($group_field_id, array(
            'name' => 'عکس نماد 2',
            'id'   => 'namad2',
            'type' => 'file',
        ));
    
    }
    /**
     * Register settings notices for display
     *
     * @since  0.1.0
     * @param  int   $object_id Option key
     * @param  array $updated   Array of updated fields
     * @return void
     */
    public function settings_notices($object_id, $updated)
    {
        if ($object_id !== $this->key || empty($updated)) {
            return;
        }
        add_settings_error($this->key . '-notices', '', __('Settings updated.', 'radwinweb'), 'updated');
        settings_errors($this->key . '-notices');
    }
    /**
     * Public getter method for retrieving protected/private variables
     * @since  0.1.0
     * @param  string  $field Field to retrieve
     * @return mixed          Field value or exception is thrown
     */
    public function __get($field)
    {
        // Allowed fields to retrieve
        if (in_array($field, array('key', 'metabox_id', 'title', 'options_page'), true)) {
            return $this->{$field};
        }
        throw new Exception('Invalid property: ' . $field);
    }
}
/**
 * Helper function to get/return the radwinweb_Admin object
 * @since  0.1.0
 * @return radwinweb_Admin object
 */
function radwinweb_admin()
{
    return radwinweb_Admin::get_instance();
}
/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function radwinweb_get_option($key = '', $default = null)
{
    if (function_exists('cmb2_get_option')) {
        // Use cmb2_get_option as it passes through some key filters.
        return cmb2_get_option(radwinweb_admin()->key, $key, $default);
    }
    // Fallback to get_option if CMB2 is not loaded yet.
    $opts = get_option(radwinweb_admin()->key, $key, $default);
    $val = $default;
    if ('all' == $key) {
        $val = $opts;
    } elseif (array_key_exists($key, $opts) && false !== $opts[$key]) {
        $val = $opts[$key];
    }
    return $val;
}
// Get it started
radwinweb_admin();
