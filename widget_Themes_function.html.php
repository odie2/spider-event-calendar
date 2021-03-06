<?php
if (!current_user_can('manage_options')) {
  die(__( 'Access Denied', 'sp_calendar' ));
}
function html_show_theme_calendar_widget() {
  ?>
  <div class="updated" style="font-size: 14px; color:red !important">
    <p><strong>
      <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;"><?php _e( 'This feature is disabled for the non-commercial version.', 'sp_calendar' ); ?></a>
    </strong>
    </p>
  </div>
  <table width="95%">
    <tr>
      <td width="100%" style="font-size:14px; font-weight:bold">
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-6/6-1.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a><br />
        <?php _e( 'This section allows you to create/edit themes for the calendars for the widget mode.', 'sp_calendar' ); ?><br />
        <?php _e( 'This feature is disabled for the non-commercial version.', 'sp_calendar' ); ?>
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-6/6-1.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a><br />
        <?php _e( 'Here are examples of 6 standard templates included in the commercial version.', 'sp_calendar' ); ?>
        <a href="http://wpdemo.web-dorado.com/spider-calendar/" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'Demo', 'sp_calendar' ); ?></a>
      </td>
      <td colspan="7" align="right" style="font-size:16px;">
        <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
          <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
        </a>
      </td>
    </tr>
  </table>
  <br /><br />
  <img src="<?php echo plugins_url('images/spider_calendar_widget_themes.png', __FILE__); ?>" />
  <?php
}

?>