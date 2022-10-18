<?php
if (! function_exists ( 'deandev_add_dashboard_widgets' )) {
	
	add_action ( 'wp_dashboard_setup', 'deandev_add_dashboard_widgets' );
	function deandev_add_dashboard_widgets() {
		add_meta_box ( 'deandev_dashboard_widget', 'تیم طراحی و فارسی سازی اوج طراحی', 'deandev_dashboard_widget_function', 'dashboard', 'side', 'high' );
	}
	
	/**
	 * Create the function to output the contents of our Dashboard Widget.
	 */
	function deandev_dashboard_widget_function() {
		
		 
		$purl = plugins_url('',__FILE__);
		
		?>
<table class="font-IR">

	<tbody>
		<tr>
		
			<td>
				<img style="float: left; margin-bottom: 20px;" src="<?php echo $purl ?>/images/widget/help.png">
			</td>

			<td>

				<p>
					این افزونه شامل پشتیبانی رایگان است. مشکلی رخ داده است؟ نگران نباشید ، ما فقط برای کمک شما یک میز راهنما راه اندازی کرده ایم. یک بلیط پشتیبانی ارسال کنید و ما همیشه خوشحال می شویم که در انتخاب افزونه ما به شما خوشحال شود.

				</p>
				<p>
					<a href="https://www.rtl-theme.com/my-account/purchase-support/?action=addTicket" class="button"> اکنون یک بلیط پشتیبانی را باز کنید </a>
				</p>

			</td>
		</tr>

		<tr>
			
			<td></td>
		
			<td><p></p>
				<div class="more-work">
					<div>

						<p>مجموعه محبوب ما</p>
						
					
						<a href="https://www.rtl-theme.com/rankie-wordpress-plugin/?aff=erfanvajdi" title="افزونه Rankie، پلاگین ردیابی رتبه در گوگل">
							<img width="80" height="80" border="0" alt="افزونه Rankie، پلاگین ردیابی رتبه در گوگل" src="https://files.rtl-theme.com/products/icons/2020/07/56077_867cc9dcd44326c32ab025d55-2.png" title="">
						</a>
						<a href="https://www.rtl-theme.com/shoptimizer-wordpress-theme/?aff=erfanvajdi">
							<img width="80" height="80" border="0" alt="قالب Shoptimizer، شاپتیمایزر سریع ترین قالب فروشگاهی وردپرس" src="https://files.rtl-theme.com/products/icons/2020/11/30853_0818cf45d2f7ccef6ba62f847-1.jpg" title="قالب Shoptimizer، شاپتیمایزر سریع ترین قالب فروشگاهی وردپرس">
						</a>
						<a href="https://www.rtl-theme.com/masterstudy-wordpress-theme/?aff=erfanvajdi">
							<img width="80" height="80" border="0" alt="قالب Masterstudy، قالب آموزشی وردپرس مستر استادی" src="https://files.rtl-theme.com/products/icons/2020/11/8174_2967eed67c1a3cbfe35351510.png" title="قالب Masterstudy، قالب آموزشی وردپرس مستر استادی">
						</a>
						<a href="https://www.rtl-theme.com/woocommerce-notification-wordpress-plugin/?aff=erfanvajdi">
							<img width="80" height="80" border="0" alt="افزونه WooCommerce Notification، افزونه نوتیفیکیشن ووکامرس" src="https://files.rtl-theme.com/products/icons/2021/02/36199_3ef1c9a7ae4b0972ede43f434.png" title="افزونه WooCommerce Notification، افزونه نوتیفیکیشن ووکامرس">
						</a>
						<a href="https://www.rtl-theme.com/user-role-editor-pro-wordpress-plugin/?aff=erfanvajdi">
							<img width="80" height="80" border="0" alt="افزونه User Role Editor Pro، افزونه مدیریت دسترسی کاربران وردپرس" src="https://files.rtl-theme.com/products/icons/2020/12/25323_273c0f8069062a9e27338b737.jpg" title="">
						</a>



					</div>
					<div class="clear">
						<!-- -->
					</div>
					<p>
						<small><a href="https://www.rtl-theme.com/user-profile/erfanvajdi/?aff=erfanvajdi">موارد بیشتر توسط اوج طراحی</a></small>
						<small>| <a id="wp_valvepress_widget_hide" href="#">  این ابزارک را نشان ندهید</a></small>
						
					</p>
					



				</div>
				<p></p></td>
		</tr>
		
		<tr><td>&nbsp;</td></tr>

	</tbody>
</table>

<script type="text/javascript">
	jQuery('#wp_valvepress_widget_hide').on('click',  function(){
		 "use strict";
		jQuery('#deandev_dashboard_widget-hide').trigger('click');
	});
</script>

<?php
	} // function of the widget
}//function exists
