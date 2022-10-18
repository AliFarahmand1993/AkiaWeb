<?php

/*

Plugin Name: Elfsight WhatsApp Chat CC

Description: همیشه از طریق یک چت محبوب در وب سایت خود با کاربران در ارتباط باشید

Plugin URI: https://www.rtl-theme.com/whatsappchat-wordpress-plugin/

Version: 1.2.3.2

Author: tiamtheme

Author URI: https://www.rtl-theme.com/author/tiamtheme/
*/



if (!defined('ABSPATH')) exit;





require_once('core/elfsight-plugin.php');



$elfsight_whatsapp_chat_config_path = plugin_dir_path(__FILE__) . 'config.json';

$elfsight_whatsapp_chat_config = json_decode(file_get_contents($elfsight_whatsapp_chat_config_path), true);



new ElfsightWhatsappChatPlugin(

    array(

        'name' => esc_html__('WhatsApp Chat'),

        'description' => esc_html__('همیشه از طریق یک چت محبوب در وب سایت خود با کاربران در ارتباط باشید'),

        'slug' => 'elfsight-whatsapp-chat',

        'version' => '1.2.2',

        'text_domain' => 'elfsight-whatsapp-chat',



        'editor_settings' => $elfsight_whatsapp_chat_config['settings'],

        'editor_preferences' => $elfsight_whatsapp_chat_config['preferences'],



        'plugin_name' => esc_html__('Elfsight WhatsApp Chat'),

        'plugin_file' => __FILE__,

        'plugin_slug' => plugin_basename(__FILE__),



        'vc_icon' => plugins_url('assets/img/vc-icon.png', __FILE__),

        'menu_icon' => plugins_url('assets/img/menu-icon.svg', __FILE__),



        'product_url' => esc_url('https://codecanyon.net/item/whatsapp-chat-wordpress-whatsapp-chat-plugin/23890257?ref=Elfsight'),

        'update_url' => esc_url('https://a.elfsight.com/updates/v1/'),

        'helpscout_plugin_id' => 110729

    )

);