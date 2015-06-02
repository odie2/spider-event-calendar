<?php
if (function_exists('current_user_can')) {
  if (!current_user_can('manage_options')) {
    die(__( 'Access Denied', 'sp_calendar' ));
  }
}
function html_show_spider_calendar($rows, $pageNav, $sort) {
  ?>
  <script language="javascript">
    function confirmation(href, title) {
      var answer = confirm("Are you sure you want to delete '" + title + "'?")
      if (answer) {
        document.getElementById('admin_form').action = href;
        document.getElementById('admin_form').submit();
      }
    }
    function ordering(name, as_or_desc) {
      document.getElementById('asc_or_desc').value = as_or_desc;
      document.getElementById('order_by').value = name;
      document.getElementById('admin_form').submit();
    }
    function submit_form_id(x) {
      var val = x.options[x.selectedIndex].value;
      document.getElementById("id_for_playlist").value = val;
      document.getElementById("admin_form").submit();
    }
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) var e = window.event;
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
    var show_one_cal = 1;
    var get_cal_id = 0;
    function show_calendar_inline(cal_id) {
      if (show_one_cal == 1) {
        show_one_cal = 0;
        jQuery.ajax({
          type:'POST',
          url:'<?php echo admin_url('admin-ajax.php?action=spidercalendarinlineedit') ?>',
          data:{calendar_id:cal_id},
          dataType:'html',
          success:function (data) {
            cancel_qiucik_edit(get_cal_id);
            var edit_cal_tr = document.createElement("tr")
            edit_cal_tr.innerHTML = data;
            edit_cal_tr.setAttribute('class', 'inline-edit-row inline-edit-row-page inline-edit-page quick-edit-row quick-edit-row-page inline-edit-page alternate inline-editor')
            edit_cal_tr.setAttribute('id', 'edit_calendar-' + cal_id);

            document.getElementById('Calendar-' + cal_id).style.display = "none";
            document.getElementById('calendar_body').appendChild(edit_cal_tr);
            document.getElementById('calendar_body').insertBefore(edit_cal_tr, document.getElementById('Calendar-' + cal_id));
            get_cal_id = cal_id;
            show_one_cal = 1
          }
        });
      }
    }
    function cancel_qiucik_edit(cal_id) {
      if (document.getElementById('edit_calendar-' + cal_id)) {
        var tr = document.getElementById('edit_calendar-' + cal_id);
        tr.parentNode.removeChild(tr);
        document.getElementById('Calendar-' + cal_id).style.display = "";
      }
    }
    function updae_inline_sp_calendar(cal_id) {
      var cal_title = document.getElementById('calendar_title').value;
      var cal_12_format = getCheckedValue(document.getElementsByName('time_format'));
      var def_year = document.getElementById('def_year').value;
      var def_month = document.getElementById('def_month').value;
      document.getElementById('imig_for_waiting').style.display = "block";
      jQuery.ajax({
        type:'POST',
        url:'<?php echo admin_url('admin-ajax.php?action=spidercalendarinlineupdate') ?>',
        data:{
          calendar_id:cal_id,
          calendar_title:cal_title,
          us_12_format_sp_calendar:cal_12_format,
          default_year:def_year,
          default_month:def_month
        },
        dataType:'html',
        success:function (data) {
          if (data) {
            document.getElementById('imig_for_waiting').style.display = "none";
            document.getElementById('Calendar-' + cal_id).innerHTML = data;
            cancel_qiucik_edit(cal_id);
          }
          else {
            alert('<?php _e( 'ERROR PLEASE INSTALL PLUGIN AGAIN', 'sp_calendar' ); ?>');
            cancel_qiucik_edit(cal_id);
          }
        }
      });
    }
    function getCheckedValue(radioObj) {
      if (!radioObj)
        return "";
      var radioLength = radioObj.length;
      if (radioLength == undefined)
        if (radioObj.checked)
          return radioObj.value;
        else
          return "";
      for (var i = 0; i < radioLength; i++) {
        if (radioObj[i].checked) {
          return radioObj[i].value;
        }
      }
      return "";
    }
  </script>
  <form method="post" onKeyPress="doNothing()" action="admin.php?page=SpiderCalendar" id="admin_form" name="admin_form">
    <?php $sp_cal_nonce = wp_create_nonce('nonce_sp_cal'); ?>
	<table cellspacing="10" width="100%" id="calendar_table">
      <tr>
        <td width="100%" style="font-size:14px; font-weight:bold">
          <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
          <br />
          <?php _e( 'This section allows you to create calendars. You can add unlimited number of calendars.', 'sp_calendar' ); ?>
          <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
        </td>
        <td colspan="7" align="right" style="font-size:16px;">
          <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
            <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
          </a>
        </td>
      </tr>
      <tr>
        <td style="width:210px"><h2><?php _e( 'Calendar Manager', 'sp_calendar' ); ?></h2></td>
        <td style="width:90px; text-align:right;">
          <p class="submit" style="padding:0px; text-align:left">
            <input type="button" value="<?php _e( 'Add a Calendar', 'sp_calendar' ); ?>" name="custom_parametrs" onClick="window.location.href='admin.php?page=SpiderCalendar&task=add_calendar'"/>
          </p>
        </td>
        <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px">
        </td>
      </tr>
    </table>
    <?php
    if (isset($_POST['serch_or_not']) && ($_POST['serch_or_not'] == "search")) {
      $serch_value = esc_js(esc_html(stripslashes($_POST['search_events_by_title'])));
    }
    else {
      $serch_value = "";
    }
    $serch_fields = '
      <div class="alignleft actions">
        <label for="search_events_by_title" style="font-size:14px">' . __( 'Title' ) . ': </label>
        <input type="text" name="search_events_by_title" value="' . $serch_value . '" id="search_events_by_title" onchange="clear_serch_texts()">
      </div>
      <div class="alignleft actions">
        <input type="button" value="' . _x( 'Search', 'submit button' ) . '" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
          document.getElementById(\'admin_form\').submit();" class="button-secondary action">
        <input type="button" value="' . __( 'Reset', 'sp_calendar' ) . '" onclick="window.location.href=\'admin.php?page=SpiderCalendar\'" class="button-secondary action">
      </div>';
    print_html_nav($pageNav['total'], $pageNav['limit'], $serch_fields);
    ?>
    <table class="wp-list-table widefat fixed pages" style="width:99%">
      <thead>
      <TR>
        <th scope="col" id="id" class="<?php echo (($sort["sortid_by"] == "id") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:50px">
          <a href="javascript:ordering('id',<?php echo(($sort["sortid_by"] == "id") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'ID', 'sp_calendar' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="title" class="<?php echo (($sort["sortid_by"] == "title") ? $sort["custom_style"] : $sort["default_style"]); ?>">
          <a href="javascript:ordering('title',<?php echo (($sort["sortid_by"] == "title") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'Title' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
        <th style="width:100px"><?php _e( 'Manage Events', 'sp_calendar' ); ?></th>
        <th scope="col" id="published" class="<?php echo (($sort["sortid_by"] == "published") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:100px">
          <a href="javascript:ordering('published',<?php echo (($sort["sortid_by"] == "published") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'Published', 'sp_calendar' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
      </TR>
      </thead>
      <tbody id="calendar_body">
        <?php for ($i = 0; $i < count($rows); $i++) { ?>
      <tr id="Calendar-<?php echo $rows[$i]->id; ?>" class=" hentry alternate iedit author-self" style="display:table-row;">
        <td><?php echo $rows[$i]->id; ?></td>
        <td class="post-title page-title column-title">
          <a title="<?php _e( 'Manage Events', 'sp_calendar' ); ?>" class="row-title" href="admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=<?php echo $rows[$i]->id; ?>"><?php echo $rows[$i]->title; ?></a>
          <div class="row-actions">
            <span class="edit">
              <a href="admin.php?page=SpiderCalendar&task=edit_calendar&id=<?php echo $rows[$i]->id; ?>" title="<?php _e( 'Edit This Calendar', 'sp_calendar' ); ?>"><?php _e( 'Edit' ); ?></a> | </span>
            <span class="inline hide-if-no-js">
              <a href="#" class="editinline" onClick="show_calendar_inline(<?php echo  $rows[$i]->id; ?>)" title="<?php _e( 'Edit This Calendar Inline', 'sp_calendar' ); ?>"><?php _e( 'Quick Edit' ); ?></a>  | </span>
            <span class="trash">
              <a class="submitdelete" title="<?php _e( 'Delete This Calendar', 'sp_calendar' ); ?>" href="javascript:confirmation('admin.php?page=SpiderCalendar&task=remove_calendar&id=<?php echo $rows[$i]->id; ?>','<?php echo $rows[$i]->title; ?>')"><?php _e( 'Delete' ); ?></a></span>
          </div>
        </td>
        <td><a href="admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=<?php echo $rows[$i]->id; ?>"><?php _e( 'Manage events', 'sp_calendar' ); ?></a></td>
        <td><a <?php if (!$rows[$i]->published) echo 'style="color:#C00"'; ?>
          href="admin.php?page=SpiderCalendar&task=published&id=<?php echo $rows[$i]->id; ?>&_wpnonce=<?php echo $sp_cal_nonce; ?>"><?php if ($rows[$i]->published) _e( 'Yes' ); else _e( 'No' ); ?></a>
        </td>
      </tr>
        <?php } ?>
      </tbody>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="id_for_playlist" id="id_for_playlist" value="<?php if (isset($_POST['id_for_playlist'])) echo esc_js(esc_html(stripslashes($_POST['id_for_playlist'])));?>"/>
    <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if (isset($_POST['asc_or_desc'])) echo esc_js(esc_html(stripslashes($_POST['asc_or_desc'])));?>"/>
    <input type="hidden" name="order_by" id="order_by" value="<?php if (isset($_POST['order_by'])) echo esc_js(esc_html(stripslashes($_POST['order_by'])));?>"/>
    <?php
    ?>
  </form>
  <?php
}

function html_add_spider_calendar() {
  ?>
  <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel_calendar') {
        submitform(pressbutton);
        return;
      }
      submitform(pressbutton);
    }
    function submitform(pressbutton) {
      document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
	  if (document.getElementById('title').value == "") {
					alert('<?php _e( 'Provide calendar title', 'sp_calendar' ); ?>');
				  }
				  else {
      document.getElementById('adminForm').submit();
	  }
    }
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) {
          var e = window.event;
        }
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
  </script>
  <style>
    .calendar .button {
      display: table-cell !important;
    }
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <table width="95%">
    <tr>
      <td width="100%" style="font-size:14px; font-weight:bold">
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
        <br />
        <?php _e( 'This section allows you to create calendars. You can add unlimited number of calendars.', 'sp_calendar' ); ?>
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
      </td>
      <td colspan="7" align="right" style="font-size:16px;">
        <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
          <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
        </a>
      </td>
    </tr>
    <tr>
      <td width="100%"><h2><?php _e( 'Add Calendar', 'sp_calendar' ); ?></h2></td>
      <td align="right"><input type="button" onClick="submitbutton('Save')" value="<?php _e( 'Save' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="submitbutton('Apply')" value="<?php _e( 'Apply' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="window.location.href='admin.php?page=SpiderCalendar'" value="<?php _e( 'Cancel' ); ?>" class="button-secondary action"></td>
    </tr>
  </table>

  <form onKeyPress="doNothing()" action="admin.php?page=SpiderCalendar" method="post" name="adminForm" id="adminForm">
    <table class="form-table" style="width:400px">
      <tr>
        <td class="key"><label for="name"><?php _e( 'Title' ); ?>: </label></td>
        <td><input type="text" name="title" id="title" size="30" value=""/></td>
      </tr>
      <tr>
        <td class="key"><label for="def_year"><?php _e( 'Default Year', 'sp_calendar' ); ?>: </label></td>
        <td><input type="text" name="def_year" id="def_year" size="30" value=""/></td>
      </tr>
      <tr>
        <td class="key"><label for="def_month"><?php _e( 'Default Month', 'sp_calendar' ); ?>: </label></td>
        <td>
          <select id="def_month" name="def_month">
            <option selected="selected" value=""><?php _e( 'Current', 'sp_calendar' ); ?></option>
          <?php
          $month_array = array(
            '' => __( 'Current', 'sp_calendar' ),
            '01' => __( 'January' ),
            '02' => __( 'February' ),
            '03' => __( 'March' ),
            '04' => __( 'April' ),
            '05' => __( 'May' ),
            '06' => __( 'June' ),
            '07' => __( 'July' ),
            '08' => __( 'August' ),
            '09' => __( 'September' ),
            '10' => __( 'October' ),
            '11' => __( 'November' ),
            '12' => __( 'December' ),
          );
          foreach ($month_array as $key => $def_month) {
            ?>
            <option value="<?php echo $key;?>"><?php echo $def_month;?></option>
            <?php
          }
          ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="key"><label for="name"><?php _e( 'Use 12-hour time format', 'sp_calendar' ); ?>: </label></td>
        <td>
          <input type="radio" name="time_format" id="time_format0" value="0" checked="checked" class="inputbox">
          <label for="time_format0"><?php _e( 'No' ); ?></label>
          <input type="radio" name="time_format" id="time_format1" value="1" class="inputbox">
          <label for="time_format1"><?php _e( 'Yes' ); ?></label>
        </td>
      </tr>
      <tr>
        <td class="key"><label for="published"><?php _e( 'Published', 'sp_calendar' ); ?>: </label></td>
        <td>
          <input type="radio" name="published" id="published0" value="0" class="inputbox">
          <label for="published0"><?php _e( 'No' ); ?></label>
          <input type="radio" name="published" id="published1" value="1" checked="checked" class="inputbox">
          <label for="published1"><?php _e( 'Yes' ); ?></label>
        </td>
      </tr>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="option" value="com_spidercalendar"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="cid[]" value=""/>
    <input type="hidden" name="task" value=""/>
  </form>
  <?php
}

function html_edit_spider_calendar($row) {
  ?>
  <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel_calendar') {
        submitform(pressbutton);
        return;
      }
      submitform(pressbutton);
    }
    function submitform(pressbutton) {
      document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
	  if (document.getElementById('title').value == "") {
					alert('<?php _e( 'Provide calendar title', 'sp_calendar' ); ?>');
				  }
				  else {
      document.getElementById('adminForm').submit();
	  }
    }    
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) {
          var e = window.event;
        }
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
  </script>
  <style>
    .calendar .button {
      display: table-cell !important;
    }
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <table width="95%">
    <tr>
      <td width="100%" style="font-size:14px; font-weight:bold">
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
        <br />
        <?php _e( 'This section allows you to create calendars. You can add unlimited number of calendars.', 'sp_calendar' ); ?>
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
      </td>
      <td colspan="7" align="right" style="font-size:16px;">
        <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
          <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
        </a>
      </td>
    </tr>
    <tr>
      <td width="100%"><h2><?php echo sprintf( __( 'Calendar - %s', 'sp_calendar' ), $row->title ); ?></h2></td>
      <td align="right"><input type="button" onClick="submitbutton('Save')" value="<?php _e( 'Save' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="submitbutton('Apply')" value="<?php _e( 'Apply' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="window.location.href='admin.php?page=SpiderCalendar'" value="<?php _e( 'Cancel' ); ?>" class="button-secondary action"></td>
    </tr>
  </table>

  <form onKeyPress="doNothing()" action="admin.php?page=SpiderCalendar&id=<?php echo $row->id; ?>" method="post" name="adminForm" id="adminForm">
    <table class="form-table" style="width:400px">
      <tr>
        <td class="key"><label for="name"><?php _e( 'Title' ); ?>: </label></td>
        <td><input type="text" name="title" id="title" size="30" value="<?php echo $row->title; ?>"/></td>
      </tr>
      <tr>
        <td class="key"><label for="name"><?php _e( 'Default Year', 'sp_calendar' ); ?>: </label></td>
        <td><input type="text" name="def_year" id="def_year" size="30" value="<?php echo $row->def_year; ?>"/></td>
      </tr>
      <tr>
        <td class="key"><label for="name"><?php _e( 'Default Month', 'sp_calendar' ); ?>: </label></td>
        <td>
          <select id="def_month" name="def_month">
          <?php
          $month_array = array(
            '' => __( 'Current', 'sp_calendar' ),
            '01' => __( 'January' ),
            '02' => __( 'February' ),
            '03' => __( 'March' ),
            '04' => __( 'April' ),
            '05' => __( 'May' ),
            '06' => __( 'June' ),
            '07' => __( 'July' ),
            '08' => __( 'August' ),
            '09' => __( 'September' ),
            '10' => __( 'October' ),
            '11' => __( 'November' ),
            '12' => __( 'December' ),
          );
          foreach ($month_array as $key => $def_month) {
            ?>
            <option <?php echo (($row->def_month == $key) ? 'selected="selected"' : ''); ?> value="<?php echo $key; ?>"><?php echo $def_month; ?></option>
            <?php
          }
          ?>
          </select>
        </td>
      </tr>
      <tr>
        <td class="key"><label for="name"><?php _e( 'Use 12 hours time format', 'sp_calendar' ); ?>: </label></td>
        <td>
          <input type="radio" name="time_format" id="time_format0" value="0" <?php cheched($row->time_format, '0'); ?> class="inputbox">
          <label for="time_format0"><?php _e( 'No' ); ?></label>
          <input type="radio" name="time_format" id="time_format1" value="1" <?php cheched($row->time_format, '1'); ?> class="inputbox">
          <label for="time_format1"><?php _e( 'Yes' ); ?></label>
        </td>
      </tr>
      <tr>
        <td class="key"><label for="published"><?php _e( 'Published', 'sp_calendar' ); ?>:</label></td>
        <td>
          <input type="radio" name="published" id="published0" value="0" <?php cheched($row->published, '0'); ?> class="inputbox">
          <label for="published0"><?php _e( 'No' ); ?></label>
          <input type="radio" name="published" id="published1" value="1" <?php cheched($row->published, '1'); ?> class="inputbox">
          <label for="published1"><?php _e( 'Yes' ); ?></label>
        </td>
      </tr>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="option" value="com_spidercalendar"/>
    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>"/>
    <input type="hidden" name="task" value=""/>
  </form>
  <?php
}

function cheched($row, $y) {
  if ($row == $y) {
    echo 'checked="checked"';
  }
}

function selectted($row, $y) {
  if ($row == $y) {
    echo 'selected="selected"';
  }
}

function show_event_category($rows, $pageNav, $sort){
 global $wpdb;
   ?>
  <script language="javascript">
    function confirmation(href, title) {
      // TODO (also on top)
      var answer = confirm("Are you sure you want to delete '" + title + "'?")
      if (answer) {
        document.getElementById('admin_form').action = href;
        document.getElementById('admin_form').submit();
      }
    }
    function ordering(name, as_or_desc) {
      document.getElementById('asc_or_desc').value = as_or_desc;
      document.getElementById('order_by').value = name;
      document.getElementById('admin_form').submit();
    }
    function submit_form_id(x) {
      var val = x.options[x.selectedIndex].value;
      document.getElementById("id_for_playlist").value = val;
      document.getElementById("admin_form").submit();
    }
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) var e = window.event;
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
    var show_one_cal = 1;
    var get_cal_id = 0;
    function show_calendar_inline(cal_id) {
      if (show_one_cal == 1) {
        show_one_cal = 0;
        jQuery.ajax({
          type:'POST',
          url:'<?php echo admin_url('admin-ajax.php?action=spidercalendarinlineedit') ?>',
          data:{calendar_id:cal_id},
          dataType:'html',
          success:function (data) {
            cancel_qiucik_edit(get_cal_id);
            var edit_cal_tr = document.createElement("tr")
            edit_cal_tr.innerHTML = data;
            edit_cal_tr.setAttribute('class', 'inline-edit-row inline-edit-row-page inline-edit-page quick-edit-row quick-edit-row-page inline-edit-page alternate inline-editor')
            edit_cal_tr.setAttribute('id', 'edit_calendar-' + cal_id);

            document.getElementById('Calendar-' + cal_id).style.display = "none";
            document.getElementById('calendar_body').appendChild(edit_cal_tr);
            document.getElementById('calendar_body').insertBefore(edit_cal_tr, document.getElementById('Calendar-' + cal_id));
            get_cal_id = cal_id;
            show_one_cal = 1
          }
        });
      }
    }
    function cancel_qiucik_edit(cal_id) {
      if (document.getElementById('edit_calendar-' + cal_id)) {
        var tr = document.getElementById('edit_calendar-' + cal_id);
        tr.parentNode.removeChild(tr);
        document.getElementById('Calendar-' + cal_id).style.display = "";
      }
    }
    function updae_inline_sp_calendar(cal_id) {
      var cal_title = document.getElementById('calendar_title').value;
      var cal_12_format = getCheckedValue(document.getElementsByName('time_format'));
      var def_year = document.getElementById('def_year').value;
      var def_month = document.getElementById('def_month').value;
      document.getElementById('imig_for_waiting').style.display = "block";
      jQuery.ajax({
        type:'POST',
        url:'<?php echo admin_url('admin-ajax.php?action=spidercalendarinlineupdate') ?>',
        data:{
          calendar_id:cal_id,
          calendar_title:cal_title,
          us_12_format_sp_calendar:cal_12_format,
          default_year:def_year,
          default_month:def_month
        },
        dataType:'html',
        success:function (data) {
          if (data) {
            document.getElementById('imig_for_waiting').style.display = "none";
            document.getElementById('Calendar-' + cal_id).innerHTML = data;
            cancel_qiucik_edit(cal_id);
          }
          else {
            alert('<?php _e( 'ERROR PLEASE INSTALL PLUGIN AGAIN', 'sp_calendar' ); ?>');
            cancel_qiucik_edit(cal_id);
          }
        }
      });
    }
    function getCheckedValue(radioObj) {
      if (!radioObj)
        return "";
      var radioLength = radioObj.length;
      if (radioLength == undefined)
        if (radioObj.checked)
          return radioObj.value;
        else
          return "";
      for (var i = 0; i < radioLength; i++) {
        if (radioObj[i].checked) {
          return radioObj[i].value;
        }
      }
      return "";
    }
  </script>
  <form method="post" onKeyPress="doNothing()" action="admin.php?page=spider_calendar_event_category" id="admin_form" name="admin_form">
    <?php $sp_cal_nonce = wp_create_nonce('nonce_sp_cal'); ?>
	<table cellspacing="10" width="100%" id="category_table">
      <tr>
        <td width="100%" style="font-size:14px; font-weight:bold">
          <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
          <br />
          <?php _e( 'This section allows you to create event categories. You can add unlimited number of categories.', 'sp_calendar' ); ?>
          <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-4.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
        </td>
		<td colspan="7" align="right" style="font-size:16px;">
      <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
        <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
      </a>
    </td>
      </tr>
	  
      <tr>
        <td style="width:210px"><h2><?php _e( 'Event Category', 'sp_calendar' ); ?></h2></td>
        <td style="width:90px; text-align:right;">
          <p class="submit" style="padding:0px; text-align:left">
            <input type="button" value="<?php _e( 'Add a Category', 'sp_calendar' ); ?>" name="custom_parametrs" onClick="window.location.href='admin.php?page=spider_calendar_event_category&task=add_category'"/>
          </p>
        </td>
        <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px">
        </td>
      </tr>
    </table>
    <?php
    if (isset($_POST['serch_or_not']) && ($_POST['serch_or_not'] == "search")) {
      $serch_value = esc_js(esc_html(stripslashes($_POST['search_cat_by_title'])));
    }
    else {
      $serch_value = "";
    }
    $serch_fields = '
      <div class="alignleft actions" >
        <label for="search_cat_by_title" style="font-size:14px">' . __( 'Title' ) . ': </label>
        <input type="text" name="search_cat_by_title" value="' . $serch_value . '" id="search_cat_by_title" onchange="clear_serch_texts()">
      </div>
      <div class="alignleft actions">
        <input type="button" value="' . _x( 'Search', 'submit button' ) . '" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
          document.getElementById(\'admin_form\').submit();" class="button-secondary action">
        <input type="button" value="' . __( 'Reset', 'sp_calendar' ) . '" onclick="window.location.href=\'admin.php?page=spider_calendar_event_category\'" class="button-secondary action">
      </div>';
    print_html_nav($pageNav['total'], $pageNav['limit'], $serch_fields);
    ?>
    <table class="wp-list-table widefat fixed pages" style="width:99%">
      <thead>
      <TR>
        <th scope="col" id="id" class="<?php echo (($sort["sortid_by"] == "id") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:50px">
          <a href="javascript:ordering('id',<?php echo(($sort["sortid_by"] == "id") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'ID', 'sp_calendar' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="title" class="<?php echo (($sort["sortid_by"] == "title") ? $sort["custom_style"] : $sort["default_style"]); ?>">
          <a href="javascript:ordering('title',<?php echo (($sort["sortid_by"] == "title") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'Title' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="description" class="<?php echo (($sort["sortid_by"] == "description") ? $sort["custom_style"] : $sort["default_style"]); ?>">
          <a href="javascript:ordering('description',<?php echo (($sort["sortid_by"] == "description") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'Description', 'sp_calendar' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
        <th scope="col" id="published" class="<?php echo (($sort["sortid_by"] == "published") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:100px">
          <a href="javascript:ordering('published',<?php echo (($sort["sortid_by"] == "published") ? $sort["1_or_2"] : "1"); ?>)">
            <span><?php _e( 'Published', 'sp_calendar' ); ?></span>
            <span class="sorting-indicator"></span>
          </a>
        </th>
      </TR>
      </thead>
      <tbody id="category_body">
        <?php for ($i = 0; $i < count($rows); $i++) { ?>
      <tr id="Calendar-<?php echo $rows[$i]->id; ?>" class=" hentry alternate iedit author-self" style="display:table-row;">
        <td><?php echo $rows[$i]->id; ?></td>
        <td class="post-title page-title column-title">
          <?php echo $rows[$i]->title; ?></a>
		  
		 <div class="row-actions">
            <span class="edit">
              <a href="admin.php?page=spider_calendar_event_category&task=edit_event_category&id=<?php echo $rows[$i]->id; ?>" title="<?php _e( 'Edit This Calendar', 'sp_calendar' ); ?>"><?php _e( 'Edit' ); ?></a> | </span>
            <span class="trash">
              <a class="submitdelete" title="<?php _e( 'Delete This Calendar', 'sp_calendar' ); ?>" href="javascript:confirmation('admin.php?page=spider_calendar_event_category&task=remove_event_category&id=<?php echo $rows[$i]->id; ?>','<?php echo $rows[$i]->title; ?>')"><?php _e( 'Delete' ); ?></a></span>
          </div>
        </td>
        <td><?php echo $rows[$i]->description; ?></td>
        <td><a <?php if (!$rows[$i]->published) echo 'style="color:#C00"'; ?> href="admin.php?page=spider_calendar_event_category&task=published&id=<?php echo $rows[$i]->id; ?>&_wpnonce=<?php echo $sp_cal_nonce; ?>"><?php if ($rows[$i]->published) _e( 'Yes' ); else _e( 'No' ); ?></a>
        </td>
      </tr>
        <?php } ?>
      </tbody>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="id_for_playlist" id="id_for_playlist" value="<?php if (isset($_POST['id_for_playlist'])) echo esc_js(esc_html(stripslashes($_POST['id_for_playlist'])));?>"/>
    <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if (isset($_POST['asc_or_desc'])) echo esc_js(esc_html(stripslashes($_POST['asc_or_desc'])));?>"/>
    <input type="hidden" name="order_by" id="order_by" value="<?php if (isset($_POST['order_by'])) echo esc_js(esc_html(stripslashes($_POST['order_by'])));?>"/>
    <?php
    ?>
  </form>
  <?php

}

function edit_event_category($id){
 global $wpdb;
$row=$wpdb->get_row($wpdb->prepare ("SELECT * FROM " . $wpdb->prefix . "spidercalendar_event_category WHERE id=%d" , $id ));
		?>
				
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			document.getElementById('adminForm').action = "admin.php?page=spider_calendar_event_category&task=" + pressbutton+"&id=<?php echo $id?>";
				if (document.getElementById('cat_title').value == "") {
					alert('<?php _e( 'Provide the category title', 'sp_calendar' ); ?>');
				  }
				  else {
					document.getElementById('adminForm').submit();
				  }
		  }
		  jQuery(document).ready(function() {
		jQuery('.color_input').wpColorPicker();
	 });
        </script>    
		<style>
		.wp-picker-holder{
			position: absolute;
			z-index: 2;
			top: 20px;
		}
		.wp-color-result {
		  background-color: transparent;
		  width: 85px;
		}
		.wp-color-result:focus{
			outline: none;
		}
		.color_for_this {
		  height: 24px;
		  top: 0px;
		  position: relative;
		  width: 35px;
		  left: 2px;
		}
		 .wp-color-result:hover{
		  background-color: transparent;
		}
		</style>
	<table>
		 <tr>
			  <td align="right"><input type="button" onClick="submitbutton('save_category_event')" value="<?php _e( 'Save' ); ?>" class="button-secondary action"></td>
			  <td align="right"><input type="button" onClick="submitbutton('apply_event_category')" value="<?php _e( 'Apply' ); ?>" class="button-secondary action"></td>
			  <td align="right"><input type="button" onClick="window.location.href='admin.php?page=spider_calendar_event_category'" value="<?php _e( 'Cancel' ); ?>" class="button-secondary action">
			  </td>   
		 </tr>
    </table>

	
		<form action="" method="post" name="adminForm" id="adminForm">
	<div class="width-45 fltlft ">
	<fieldset class="adminform" >

	
    
    
<table class="admintable" >


          
              <tr>
                <td class="key" ><label for="message"><?php _e( 'Category title', 'sp_calendar' ); ?>:</label>  </td>
               <td> 
			   
			   <input   type="text" name="title" value="<?php if(isset($row->title)) echo htmlspecialchars($row->title); 
			   ?>" id="cat_title"/>
			   </td>
               </tr>
               
				 <tr>
			   <td class="key" ><label for="message"><?php _e( 'Category Color', 'sp_calendar' ); ?>:</label>  </td>
             
               <td>
				<div class="color_for_this" style="background-color: #<?php if(isset($row->color)) echo htmlspecialchars($row->color); ?>">
					<input type="text" name="color" id="color" class="color_input wp-color-picker" style="width:134px;" value="<?php if(isset($row->color)) echo htmlspecialchars($row->color); ?>"/>
				</div>
				</td>
               </tr>
                

			    <tr>
					<td class="key"><label for="message"> <?php _e( 'Description', 'sp_calendar' ); ?>:</label></td>
					<td ><div id="poststuff" style="width:100% !important;">
					 <?php if(version_compare(get_bloginfo('version'),3.3)<0) {?>
							<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea"><?php the_editor(stripslashes($row->description),"description","title" ); ?>
							</div>
							<?php }else{
							if(isset($row->description)) $desc1 = $row->description;
							else $desc1 = "";
							wp_editor($desc1, "description"); }?>
						
					  </div>
                    </div></td>	
                        
				</tr>		
							
					<tr>
				<td class="key" ><label for="message"><?php _e( 'Published', 'sp_calendar' ); ?>:</label>  </td>
               
			   <td>
                    <input type="radio" name="published" id="published0" value="0" <?php if(isset($row->published)) cheched($row->published, '0'); ?> class="inputbox">
                    <label for="published0"><?php _e( 'No' ); ?></label>
                    <input type="radio" name="published" id="published1" value="1" <?php if(isset($row->published)) cheched($row->published, '1'); ?> class="inputbox">
                    <label for="published1"><?php _e( 'Yes' ); ?></label>
                  </td>
                 </tr> 		

                </table>
                
     </fieldset>  
       </div>
<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>	   
<input type="hidden" name="id" value="<?php echo $id ?>" />
</form>
				<?php		
			   
		
	
	}

	
	function html_upcoming_widget($rows, $pageNav, $sort){
require_once("spidercalendar_upcoming_events_widget.php");
	  global $wpdb;
	 $input_id=$_GET['id_input'];
	 $w_id = $_GET['w_id'];
	 $tbody_id='event'.$w_id;
	 $calendar_id=$_GET['upcalendar_id'];
	 ?><html>
  <head>
  <link rel="stylesheet" id="thickbox-css" href="<?php echo plugins_url("elements/calendar-jos.css", __FILE__) ?>" type="text/css" media="all">
 <?php   wp_print_scripts("Canlendar_upcoming");
  wp_print_scripts("calendnar-setup_upcoming");
  wp_print_scripts("calenndar_function_upcoming");
 ?>
 
  <style>
    .calendar .button {
      display: table-cell !important;
    }
	
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
	}
	input[type=checkbox]:checked:before,
	th.sorted.asc .sorting-indicator:before, th.desc:hover span.sorting-indicator:before,
	th.sorted.desc .sorting-indicator:before, th.asc:hover span.sorting-indicator:before{
		content: close-quote !important;
	}
	
  </style>
  <script language="javascript">
	function ordering(name, as_or_desc) {
      document.getElementById('asc_or_desc').value = as_or_desc;
      document.getElementById('order_by').value = name;
      document.getElementById('admin_form').submit();
    }
    function submit_form_id(x) {
      var val = x.options[x.selectedIndex].value;
      document.getElementById("id_for_playlist").value = val;
      document.getElementById("admin_form").submit();
    }
	
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) {
          var e = window.event;
        }
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
	
	
	function isChecked(isitchecked){
	if (isitchecked == true){
		document.adminForm.boxchecked.value++;
	}
	else {
		document.adminForm.boxchecked.value--;
	}
}
	
	
	function checkAll( n, fldName ) {

  if (!fldName) {

     fldName = 'cb';

  }

	var f = document.admin_form;

	var c = f.toggle.checked;

	var n2 = 0;

	for (i=0; i < n; i++) {

		cb = eval( 'f.' + fldName + '' + i );

		if (cb) {

			cb.checked = c;

			n2++;

		}

	}

	if (c) {

		document.admin_form.boxchecked.value = n2;

	} else {

		document.admin_form.boxchecked.value = 0;

	}

}


	

function select_events()

{
	var id =[];
	var title =[];

	for(i=0; i<<?php echo count($rows)?>; i++)
		if(document.getElementById("p"+i))
			if(document.getElementById("p"+i).checked)
			{
				id.push(document.getElementById("p"+i).value);
				title.push(document.getElementById("title_"+i).value);
				
			}
	window.parent.jSelectEvents('<?php echo $input_id ?>','<?php echo $tbody_id ?>','<?php echo $w_id ?>',id, title);
	}

		
  </script>
<?php


  if(get_bloginfo( 'version' )>3.3){

	?>

<link rel="stylesheet" href="<?php echo bloginfo("url") ?>/wp-admin/load-styles.php?c=0&amp;dir=ltr&amp;load=admin-bar,wp-admin&amp;ver=7f0753feec257518ac1fec83d5bced6a" type="text/css" media="all">

<?php

}

else

{

	?>

 <link rel="stylesheet" href="<?php echo bloginfo("url") ?>/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global,wp-admin&amp;ver=aba7495e395713976b6073d5d07d3b17" type="text/css" media="all">

 <?php

}

?>

<link rel="stylesheet" id="thickbox-css" href="<?php echo bloginfo('url')?>/wp-includes/js/thickbox/thickbox.css?ver=20111117" type="text/css" media="all">

<!-- <link rel="stylesheet" id="colors-css" href="<?php echo bloginfo('url')?>/wp-admin/css/colors-classic.css?ver=20111206" type="text/css" media="all"> -->
</head>
  <body>
    <form method="post" onKeyPress="doNothing()" action="<?php echo admin_url('admin-ajax.php') ?>?action=upcoming&id_input=<?php echo $input_id;?>&upcalendar_id=<?php echo $calendar_id;?>&w_id=<?php echo $w_id;?>" id="admin_form" name="admin_form">
    <table cellspacing="10" width="100%">
    
    <tr>
      <td width="100%"><h2<?php _e( 'Event Manager', 'sp_calendar' ); ?>/h2></td></td>
	  <td align="right" width="100%">

                <button onClick="select_events();" style="width:98px; height:34px; background:url(<?php echo plugins_url('',__FILE__) ?>/front_end/images/add_but.png) no-repeat;border:none;cursor:pointer;">&nbsp;</button>			
           </td>
    </tr>
  </table>
  <?php
  if (isset($_POST['serch_or_not']) && ($_POST['serch_or_not'] == "search")) {
    $serch_value = esc_js(esc_html(stripslashes($_POST['search_events_by_title'])));
  }
  else {
    $serch_value = "";
  }
  $startdate = (isset($_POST["startdate"]) ? esc_js(esc_html(stripslashes($_POST["startdate"]))) : '');
  $enddate = (isset($_POST["enddate"]) ? esc_js(esc_html(stripslashes($_POST["enddate"]))) : '');
  $serch_fields = '
    <div class="alignleft actions">
    	<label for="search_events_by_title" style="font-size:14px">' . __( 'Title' ) . ': </label>
      <input type="text" name="search_events_by_title" value="' . $serch_value . '" id="search_events_by_title" onchange="clear_serch_texts()" style="border: 1px solid #DCDCEC;"/>
    </div>
    <div class="alignleft actions">
      ' . __( 'From', 'sp_calendar' ) . ':<input class="inputbox" type="text" style="width: 90px;border: 1px solid #DCDCEC;" name="startdate" id="startdate" size="10" maxlength="10" value="' . $startdate . '" />
      <input type="reset" class="wd_button" value="..." onclick="return showCalendar(\'startdate\',\'%Y-%m-%d\');">
      ' . __( 'To', 'sp_calendar' ) . ':<input class="inputbox" type="text" style="width: 90px;border: 1px solid #DCDCEC;" name="enddate" id="enddate" size="10" maxlength="10" value="' . $enddate . '">
      <input type="reset" class="wd_button" value="..." onclick="return showCalendar(\'enddate\',\'%Y-%m-%d\');">
    </div>
    <div class="alignleft actions">
   		<input type="button" style="border: 1px solid #DCDCEC;border-radius: 10px;" value="' . _x( 'Search', 'submit button' ) . '" onclick="document.getElementById(\'page_number\').value=\'1\';document.getElementById(\'serch_or_not\').value=\'search\'; document.getElementById(\'admin_form\').submit();" class="button-secondary action">
      <input type="button" style="border: 1px solid #DCDCEC;border-radius: 10px;" value="' . __( 'Reset', 'sp_calendar' ) . '" onclick="window.location.href=\'admin-ajax.php?action=upcoming&id_input='.$input_id.'&upcalendar_id='.$calendar_id.'&w_id='.$w_id.'\'" class="button-secondary action">
    </div>';
  print_html_nav($pageNav['total'], $pageNav['limit'], $serch_fields);
  ?>
  <style>
   .sorting-indicator {
		width: 7px;
		height: 4px;
		margin-top: 8px;
		margin-left: 7px;
		background-image: url('images/sort.gif');
		background-repeat: no-repeat;
	}
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <table class="wp-list-table widefat fixed pages" style="width:100%">
    <thead>
    <TR>
      <th scope="col" id="id" class="<?php echo (($sort["sortid_by"] == "id") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:50px;background-image: linear-gradient(to top, #EFF8FF, #F7FCFE);">
        <a href="javascript:ordering('id',<?php echo (($sort["sortid_by"] == "id") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'ID', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
	  <th style="background-image: linear-gradient(to top, #EFF8FF, #F7FCFE);" width="20" class="manage-column column-cb check-column">

            <input  style="border: 1px solid #DCDCEC;-webkit-appearance: checkbox;" type="checkbox" name="toggle" id="toggle" value="" onClick="checkAll(<?php echo count($rows)?>, 'p')">

            </th>
      <th style="background-image: linear-gradient(to top, #EFF8FF, #F7FCFE);" scope="col" id="title" class="<?php echo (($sort["sortid_by"] == "title") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('title',<?php echo (($sort["sortid_by"] == "title") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Title' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th style="background-image: linear-gradient(to top, #EFF8FF, #F7FCFE);"scope="col" id="date" class="<?php echo (($sort["sortid_by"] == "date") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('date',<?php echo (($sort["sortid_by"] == "date") ? $sort["1_or_2"] :  "1"); ?>)">
          <span><?php _e( 'Date', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th style="background-image: linear-gradient(to top, #EFF8FF, #F7FCFE);"scope="col" id="time" class="<?php echo (($sort["sortid_by"] == "time") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('time',<?php echo (($sort["sortid_by"] == "time") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Time', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
    </TR>
    </thead>
    <tbody>
      <?php for ($i = 0; $i < count($rows); $i++) { ?>
    <tr>

      <td style="border-bottom: 1px solid #DCDCEC;"><?php echo $rows[$i]->id; ?></td>
	  	<td style="border-bottom: 1px solid #DCDCEC;">
			<input style="border: 1px solid #DCDCEC;-webkit-appearance: checkbox;" type="checkbox" id="p<?php echo $i?>" value="<?php echo $rows[$i]->id;?>" />
			<input type="hidden" id="title_<?php echo $i?>" value="<?php echo  htmlspecialchars($rows[$i]->title);?>" />
		</td>
      <td style="border-bottom: 1px solid #DCDCEC;"><a href="<?php echo admin_url('admin-ajax.php') ?>?action=upcoming" onClick="window.parent.jSelectEvents('<?php echo $input_id ?>','<?php echo $tbody_id ?>','<?php echo $w_id ?>',['<?php echo $rows[$i]->id?>'],['<?php echo htmlspecialchars(addslashes($rows[$i]->title));?>'])"><?php echo $rows[$i]->title; ?></a>
      </td>
      <td style="border-bottom: 1px solid #DCDCEC;"><?php if ($rows[$i]->date_end != '0000-00-00' && $rows[$i]->date_end != '2070-12-12') echo $rows[$i]->date . ' - ' . $rows[$i]->date_end; else echo $rows[$i]->date; ?></td>
      <td style="border-bottom: 1px solid #DCDCEC;"><?php echo $rows[$i]->time ?></td>
    </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
  <input type="hidden" name="boxchecked" value="0">
  <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if (isset($_POST['asc_or_desc'])) echo esc_js(esc_html(stripslashes($_POST['asc_or_desc']))); ?>"/>
  <input type="hidden" name="order_by" id="order_by" value="<?php if (isset($_POST['order_by'])) echo esc_js(esc_html(stripslashes($_POST['order_by']))); ?>"/>
  <?php
  ?>
</form>
  </body>
  </html>
<?php
die();
}
	
// Events.
function html_show_spider_event($rows, $pageNav, $sort, $calendar_id, $cal_name) {
  global $wpdb;
  ?>
  <style>
    .calendar .button {
      display: table-cell !important;
    }
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <script language="javascript">
    function ordering(name, as_or_desc) {
      document.getElementById('asc_or_desc').value = as_or_desc;
      document.getElementById('order_by').value = name;
      document.getElementById('admin_form').submit();
    }
    function doNothing() {
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
      if (keyCode == 13) {
        if (!e) {
          var e = window.event;
        }
        e.cancelBubble = true;
        e.returnValue = false;
        if (e.stopPropagation) {
          e.stopPropagation();
          e.preventDefault();
        }
      }
    }
  </script>
  <form method="post" onKeyPress="doNothing()" action="admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=<?php echo $calendar_id; ?>" id="admin_form" name="admin_form">
    <?php $sp_cal_nonce = wp_create_nonce('nonce_sp_cal'); ?>
	<table cellspacing="10" width="95%">
    <tr>
      <td width="100%" style="font-size:14px; font-weight:bold">
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a><br>
        <?php _e( 'This section allows you to create/edit the events of a particular calendar.', 'sp_calendar' ); ?><br>
        <?php _e( 'You can add unlimited number of events for each calendar.', 'sp_calendar' ); ?>
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
      </td>
      <td colspan="7" align="right" style="font-size:16px;">
        <a href="https://web-dorado.com/files/fromSpiderCalendarWP.php" target="_blank" style="color:red; text-decoration:none;">
          <img src="<?php echo plugins_url('images/header.png', __FILE__); ?>" border="0" alt="https://web-dorado.com/files/fromSpiderCalendarWP.php" width="215">
        </a>
      </td>
    </tr>
    <tr>
      <td width="100%"><h2><?php sprintf( __( 'Event Manager for calendar %s', 'sp_calendar' ), '<font style="color:red">' . $cal_name . '</font>' ); ?></h2></td>
      <td>
        <p class="submit" style="padding:0px; text-align:left">
          <input class="button-primary" type="button" value="<?php _e( 'Add an Event', 'sp_calendar' ); ?>" name="custom_parametrs" onClick="window.location.href='admin.php?page=SpiderCalendar&task=add_event&calendar_id=<?php echo $calendar_id; ?>'"/>
        </p>
      </td>
      <td>
        <p class="submit" style="padding:0px; text-align:left">
          <input type="button" class="button-primary" value="<?php _e( 'Back' ); ?>" name="custom_parametrs" onClick="window.location.href='admin.php?page=SpiderCalendar'"/>
        </p>
      </td>
    </tr>
  </table>
  <?php
  if (isset($_POST['serch_or_not']) && ($_POST['serch_or_not'] == "search")) {
    $serch_value = esc_js(esc_html(stripslashes($_POST['search_events_by_title'])));
  }
  else {
    $serch_value = "";
  }
  $startdate = (isset($_POST["startdate"]) ? esc_js(esc_html(stripslashes($_POST["startdate"]))) : '');
  $enddate = (isset($_POST["enddate"]) ? esc_js(esc_html(stripslashes($_POST["enddate"]))) : '');
  $serch_fields = '
    <div class="alignleft actions">
    	<label for="search_events_by_title" style="font-size:14px">' . __( 'Title' ) . ': </label>
      <input type="text" name="search_events_by_title" value="' . $serch_value . '" id="search_events_by_title" onchange="clear_serch_texts()" />
    </div>
    <div class="alignleft actions">
      ' . __( 'From', 'sp_calendar' ) . ':<input style="width: 90px;" class="inputbox" type="text" name="startdate" id="startdate" size="10" maxlength="10" value="' . $startdate . '" />
      <input type="reset" class="wd_button" value="..." onclick="return showCalendar(\'startdate\',\'%Y-%m-%d\');">
      ' . __( 'To', 'sp_calendar' ) . ':<input style="width: 90px;" class="inputbox" type="text" name="enddate" id="enddate" size="10" maxlength="10" value="' . $enddate . '">
      <input type="reset" class="wd_button" value="..." onclick="return showCalendar(\'enddate\',\'%Y-%m-%d\');">
    </div>
    <div class="alignleft actions">
   		<input type="button" value="' . _x( 'Search', 'submit button' ) . '" onclick="document.getElementById(\'page_number\').value=\'1\';document.getElementById(\'serch_or_not\').value=\'search\'; document.getElementById(\'admin_form\').submit();" class="button-secondary action">
      <input type="button" value="' . __( 'Reset', 'sp_calendar' ) . '" onclick="window.location.href=\'admin.php?page=SpiderCalendar&task=show_manage_event&calendar_id=' . $calendar_id . '\'" class="button-secondary action">
    </div>';
  print_html_nav($pageNav['total'], $pageNav['limit'], $serch_fields);
  ?>
  <table class="wp-list-table widefat fixed pages" style="width:99%">
    <thead>
    <TR>
      <th scope="col" id="id" class="<?php echo (($sort["sortid_by"] == "id") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:50px">
        <a href="javascript:ordering('id',<?php echo (($sort["sortid_by"] == "id") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'ID', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th scope="col" id="title" class="<?php echo (($sort["sortid_by"] == "title") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('title',<?php echo (($sort["sortid_by"] == "title") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Title' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th scope="col" id="date" class="<?php echo (($sort["sortid_by"] == "date") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('date',<?php echo (($sort["sortid_by"] == "date") ? $sort["1_or_2"] :  "1"); ?>)">
          <span><?php _e( 'Date', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th scope="col" id="time" class="<?php echo (($sort["sortid_by"] == "time") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('time',<?php echo (($sort["sortid_by"] == "time") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Time', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
	   <th scope="col" id="cattitle" class="<?php echo (($sort["sortid_by"] == "cattitle") ? $sort["custom_style"] : $sort["default_style"]); ?>">
        <a href="javascript:ordering('cattitle',<?php echo (($sort["sortid_by"] == "cattitle") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Category', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th scope="col" id="published" class="<?php echo (($sort["sortid_by"] == "published") ? $sort["custom_style"] : $sort["default_style"]); ?>" style="width:100px">
        <a href="javascript:ordering('published',<?php echo (($sort["sortid_by"] == "published") ? $sort["1_or_2"] : "1"); ?>)">
          <span><?php _e( 'Published', 'sp_calendar' ); ?></span>
          <span class="sorting-indicator"></span>
        </a>
      </th>
      <th style="width:80px"><?php _e( 'Edit' ); ?></th>
      <th style="width:80px"><?php _e( 'Delete' ); ?></th>
    </TR>
    </thead>
    <tbody>
      <?php for ($i = 0; $i < count($rows); $i++) { ?>
    <tr>
      <td><?php echo $rows[$i]->id; ?></td>
      <td><a href="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=edit_event&id=<?php echo $rows[$i]->id; ?>"><?php echo $rows[$i]->title; ?></a>
      </td>
      <td><?php if ($rows[$i]->date_end != '0000-00-00' && $rows[$i]->date_end != '2070-12-12') echo $rows[$i]->date . ' - ' . $rows[$i]->date_end; else echo $rows[$i]->date; ?></td>
      <td><?php echo $rows[$i]->time ?></td>
	  <td><?php echo $rows[$i]->cattitle ?></td>
      <td>
	    <a <?php if (!$rows[$i]->published) echo 'style="color:#C00"'; ?> href="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=published_event&id=<?php echo $rows[$i]->id; ?>&_wpnonce=<?php echo $sp_cal_nonce; ?>"><?php if ($rows[$i]->published)
          _e( 'Yes' ); else _e( 'No' ); ?>
		</a>
      </td>
      <td>
        <a href="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=edit_event&id=<?php echo $rows[$i]->id; ?>"><?php _e( 'Edit' ); ?></a>
      </td>
      <td>
        <a href="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=remove_event&id=<?php echo $rows[$i]->id; ?>&_wpnonce=<?php echo $sp_cal_nonce; ?>"><?php _e( 'Delete' ); ?></a>
      </td>
    </tr>
      <?php } ?>
    </tbody>
  </table>
  <input type="hidden" name="id_for_playlist" id="id_for_playlist" value="<?php if (isset($_POST['id_for_playlist'])) echo esc_js(esc_html(stripslashes($_POST['id_for_playlist']))); ?>"/>
  <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if (isset($_POST['asc_or_desc'])) echo esc_js(esc_html(stripslashes($_POST['asc_or_desc']))); ?>"/>
  <input type="hidden" name="order_by" id="order_by" value="<?php if (isset($_POST['order_by'])) echo esc_js(esc_html(stripslashes($_POST['order_by']))); ?>"/>
  <?php
  ?>
</form>

<?php
}

function html_add_spider_event($calendar_id, $cal_name) {
  ?>
  <style>
    .calendar .button {
      display: table-cell !important;
    }
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <script language="javascript" type="text/javascript">
  function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'cancel_event') {
      submitform(pressbutton);
      return;
    }
    if (form.date.value.search(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/)) {
      alert('<?php _e( 'Invalid Date', 'sp_calendar' ); ?>');
    }
    else if (form.selhour_from.value == "" && form.selminute_from.value == "" && form.selhour_to.value == "" && form.selminute_to.value == "") {
      submitform(pressbutton);
    }
    else if (form.selhour_from.value != "" && form.selminute_from.value != "" && form.selhour_to.value == "" && form.selminute_to.value == "") {
      submitform(pressbutton);
    }
    else if (form.selhour_from.value != "" && form.selminute_from.value != "" && form.selhour_to.value != "" && form.selminute_to.value != "") {
      submitform(pressbutton);
    }

    else {
      alert('<?php _e( 'Invalid Time', 'sp_calendar' ); ?>');
    }
  }
  
  
  function submitform(pressbutton) {
  
  if (document.getElementById('title').value == "") {
					alert('<?php _e( 'Provide the title', 'sp_calendar' ); ?>');
				  }
  else {
		document.getElementById('adminForm').submit();	  
		document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
		document.getElementById('adminForm').submit();
	}
  }
  function checkhour(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      hour = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      hour = parseFloat(hour);
      if (document.getSelection() != '') {
        return true;
      }
      if ((hour < 0) || (hour > 23)) {
        return false;
      }
    }
    return true;
  }
  function check12hour(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      input = document.getElementById(id);
      if (charCode == 48 && input.value.length == 0) {
        return false;
      }
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      hour = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      hour = parseFloat(hour);
      if (document.getSelection() != '') {
        return true;
      }
      if ((hour < 0) || (hour > 12)) {
        return false;
      }
    }
    return true;
  }
  function checknumber(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
    }
    return true;
  }
  function checkminute(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      minute = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      minute = parseFloat(minute);
      if (document.getSelection() != '') {
        return true;
      }
      if ((minute < 0) || (minute > 59)) {
        return false;
      }
    }
    return true;
  }
  function add_0(id) {
    input = document.getElementById(id);
    if (input.value.length == 1) {
      input.value = '0' + input.value;
      input.setAttribute("value", input.value);
    }
  }
  function change_type(type) {
    if (document.getElementById('daily1').value == '') {
      document.getElementById('daily1').value = 1;
    }
    else {
      document.getElementById('repeat_input').removeAttribute('style');
    }
    if (document.getElementById('weekly1').value == '') {
      document.getElementById('weekly1').value = 1;
    }
    if (document.getElementById('monthly1').value == '') {
      document.getElementById('monthly1').value = 1;
    }
    if (document.getElementById('yearly1').value == '') {
      document.getElementById('yearly1').value = 1;
    }
    switch (type) {
      case 'no_repeat':
        document.getElementById('daily').setAttribute('style', 'display:none');
        document.getElementById('weekly').setAttribute('style', 'display:none');
        document.getElementById('monthly').setAttribute('style', 'display:none');
        document.getElementById('year_month').setAttribute('style', 'display:none');
        document.getElementById('month').value = '';
        document.getElementById('date_end').value = '';
        document.getElementById('repeat_until').setAttribute('style', 'display:none');
        break;

      case 'daily':
        document.getElementById('daily').removeAttribute('style');
        document.getElementById('weekly').setAttribute('style', 'display:none');
        document.getElementById('monthly').setAttribute('style', 'display:none');
        document.getElementById('repeat').innerHTML = '<?php _e( 'Day(s)', 'sp_calendar' ); ?>';
        document.getElementById('repeat_input').value = document.getElementById('daily1').value;
        document.getElementById('month').value = '';
        document.getElementById('year_month').setAttribute('style', 'display:none');
        document.getElementById('repeat_until').removeAttribute('style');
        document.getElementById('repeat_input').onchange = function onchange(event) {
          return input_value('daily1')
        };
        break;

      case 'weekly':
        document.getElementById('daily').removeAttribute('style');
        document.getElementById('weekly').removeAttribute('style');
        document.getElementById('monthly').setAttribute('style', 'display:none');
        document.getElementById('repeat').innerHTML = '<?php _e( 'Week(s) on :', 'sp_calendar' ); ?>';
        document.getElementById('repeat_input').value = document.getElementById('weekly1').value;
        document.getElementById('month').value = '';
        document.getElementById('year_month').setAttribute('style', 'display:none');
        document.getElementById('repeat_until').removeAttribute('style');
        document.getElementById('repeat_input').onchange = function onchange(event) {
          return input_value('weekly1')
        };
        break;

      case 'monthly':
        document.getElementById('daily').removeAttribute('style');
        document.getElementById('weekly').setAttribute('style', 'display:none');
        document.getElementById('monthly').removeAttribute('style');
        document.getElementById('repeat').innerHTML = '<?php _e( 'Month(s)', 'sp_calendar' ); ?>'
        document.getElementById('repeat_input').value = document.getElementById('monthly1').value;
        document.getElementById('month').value = '';
        document.getElementById('year_month').setAttribute('style', 'display:none');
        document.getElementById('repeat_until').removeAttribute('style');
        document.getElementById('repeat_input').onchange = function onchange(event) {
          return input_value('monthly1')
        };

        break;

      case 'yearly':
        document.getElementById('daily').removeAttribute('style');
        document.getElementById('year_month').removeAttribute('style');
        document.getElementById('weekly').setAttribute('style', 'display:none');
        document.getElementById('monthly').removeAttribute('style');
        document.getElementById('repeat').innerHTML = '<?php _e( 'Year(s) in ', 'sp_calendar' ); ?>';
        document.getElementById('repeat_input').value = document.getElementById('yearly1').value;
        document.getElementById('month').value = '';
        document.getElementById('repeat_until').removeAttribute('style');
        document.getElementById('repeat_input').onchange = function onchange(event) {
          return input_value('yearly1')
        };
        break;
    }
  }
  function week_value() {
    var value = '';
    for (i = 1; i <= 7; i++) {
      if (document.getElementById('week_' + i).checked) {
        value = value + document.getElementById('week_' + i).value + ',';
      }
    }
    document.getElementById('week').value = value;
  }
  function input_repeat() {
    if (document.getElementById('repeat_input').value == 1) {
      document.getElementById('repeat_input').value = '';
    }
    document.getElementById('repeat_input').removeAttribute('style');
  }
  function radio_month() {
    if (document.getElementById('radio1').checked == true) {
      document.getElementById('monthly_list').disabled = true;
      document.getElementById('month_week').disabled = true;
      document.getElementById('month').disabled = false;
    }
    else {
      document.getElementById('month').disabled = true;
      document.getElementById('monthly_list').disabled = false;
      document.getElementById('month_week').disabled = false;
    }
  }
  function input_value(id) {
    document.getElementById(id).value = document.getElementById('repeat_input').value;
  }
  </script>
  <style>
    fieldset {
      border: 2px solid #4f9bc6;
      width: 100%;
      background: #fafbfd;
      padding: 13px;
      margin-top: 20px;
      -webkit-border-radius: 8px;
      -moz-border-radius: 8px;
      border-radius: 8px;

    }
	.wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
<table width="95%">
  <tr>
    <td width="100%" style="font-size:14px; font-weight:bold">
      <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
      <br />
      <?php _e( 'This section allows you to create/edit the events of a particular calendar.', 'sp_calendar' ); ?><br/><?php _e( 'You can add unlimited number of events for each calendar.', 'sp_calendar' ); ?>
      <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
    </td>
  </tr>
  <tbody>
    <tr>
      <td width="100%"><h2><?php sprintf( __( 'Add an event for calendar %s', 'sp_calendar' ), '<font style="color:red">' . $cal_name . '</font>' ); ?></h2></td>
      <td align="right"><input type="button" onClick="submitbutton('save_event')" value="<?php _e( 'Save' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="submitbutton('apply_event')" value="<?php _e( 'Apply' ); ?>" class="button-secondary action"></td>
      <td align="right"><input type="button" onClick="window.location.href='admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=show_manage_event'" value="<?php _e( 'Cancel' ); ?>" class="button-secondary action"></td>
    </tr>
    </tbody>
</table>
<?php
  global $wpdb;
  $calendar = $wpdb->get_row($wpdb->prepare ("SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar WHERE id=%d",  $calendar_id ));
 
  
  $query1 = $wpdb->get_results("SELECT " . $wpdb->prefix . "spidercalendar_event.category, " . $wpdb->prefix . "spidercalendar_event_category.title
FROM " . $wpdb->prefix . "spidercalendar_event
JOIN " . $wpdb->prefix . "spidercalendar_event_category
ON " . $wpdb->prefix . "spidercalendar_event.category=" . $wpdb->prefix . "spidercalendar_event_category.id;");
  
  $query2 = $wpdb->get_results("SELECT title,id FROM " . $wpdb->prefix . "spidercalendar_event_category");

  ?>
  <form action="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>" method="post" id="adminForm" name="adminForm">
    <table width="95%">
      <tr>
        <td style="width:45%">
          <div style="width:95%">
            <fieldset class="adminform">
              <legend><?php _e( 'Event Details', 'sp_calendar' ); ?></legend>
              <table class="admintable">
                <tr>
                  <td class="key"><label for="title"><?php _e( 'Title' ); ?>: </label></td>
                  <td><input type="text" id="title" name="title" size="41"/></td>
                </tr>
				
				<tr>
                  <td class="key"><label for="category"><?php _e( 'Select Category', 'sp_calendar' ); ?>: </label></td>
                  <td>
						<select id="category" name="category" style="width:240px">
							<option value="0">--<?php _e( 'Select Category', 'sp_calendar' ); ?>--</option>
							<?php foreach ($query2 as $key => $category) {
            ?>
            <option value="<?php echo $category->id; ?>"><?php if(isset($category)) echo $category->title ?></option>
            <?php
          }
          ?>
						</select>
				  </td>
                </tr>
                <tr>
                  <td class="key"><label for="date"><?php _e( 'Date', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <input style="width:90px" class="inputbox" type="text" name="date" id="date" size="10" maxlength="10" value="" />
                    <input type="reset" class="wd_button" value="..." onClick="return showCalendar('date','%Y-%m-%d');" style="width: 31px;" />
                  </td>
                </tr>
                <tr>
                  <td class="key"><label for="selhour_from"><?php _e( 'Time', 'sp_calendar' ); ?>: </label></td>
                  <?php if ($calendar->time_format == 1) { ?>
                  <td>
                    <input type="text" id="selhour_from" name="selhour_from" size="1" style="text-align:right" onKeyPress="return check12hour('selhour_from',event)" value="" title="<?php _e( 'From', 'sp_calendar' ); ?>"/> <b>:</b>
                    <input type="text" id="selminute_from" name="selminute_from" size="1" style="text-align:right" onKeyPress="return checkminute('selminute_from',event)" value="" onBlur="add_0('selminute_from')" title="<?php _e( 'From', 'sp_calendar' ); ?>"/>
                    <select id="select_from" name="select_from">
                      <option selected="selected"><?php _e( 'AM', 'sp_calendar' ); ?></option>
                      <option><?php _e( 'PM', 'sp_calendar' ); ?></option>
                    </select>
                    <span style="font-size:12px">&nbsp;-&nbsp;</span>
                    <input type="text" id="selhour_to" name="selhour_to" size="1" style="text-align:right" onKeyPress="return check12hour('selhour_to',event)" value="" title="<?php _e( 'To', 'sp_calendar' ); ?>"/> <b>:</b>
                    <input type="text" id="selminute_to" name="selminute_to" size="1" style="text-align:right" onKeyPress="return checkminute('selminute_to',event)" value="" onBlur="add_0('selminute_to')" title="<?php _e( 'To', 'sp_calendar' ); ?>"/>
                    <select id="select_to" name="select_to">
                      <option><?php _e( 'AM', 'sp_calendar' ); ?></option>
                      <option><?php _e( 'PM', 'sp_calendar' ); ?></option>
                    </select>
                  </td>
                  <?php } if ($calendar->time_format == 0) { ?>
                  <td>
                    <input type="text" id="selhour_from" name="selhour_from" size="1" style="text-align:right" onKeyPress="return checkhour('selhour_from',event)" value="" title="<?php _e( 'From', 'sp_calendar' ); ?>" onBlur="add_0('selhour_from')"/> <b>:</b>
                    <input type="text" id="selminute_from" name="selminute_from" size="1" style="text-align:right" onKeyPress="return checkminute('selminute_from',event)" value="" title="<?php _e( 'From', 'sp_calendar' ); ?>" onBlur="add_0('selminute_from')"/>
                    <span style="font-size:12px">&nbsp;-&nbsp;</span>
                    <input type="text" id="selhour_to" name="selhour_to" size="1" style="text-align:right" onKeyPress="return checkhour('selhour_to',event)" value="" title="<?php _e( 'To', 'sp_calendar' ); ?>" onBlur="add_0('selhour_to')"/> <b>:</b>
                    <input type="text" id="selminute_to" name="selminute_to" size="1" style="text-align:right" onKeyPress="return checkminute('selminute_to',event)" value="" title="<?php _e( 'To', 'sp_calendar' ); ?>" onBlur="add_0('selminute_to')"/>
                  </td>
                  <?php }?>
                </tr>
                <tr>
                  <td class="key"><label for="poststuff"><?php _e( 'Note', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <div id="poststuff" style="width:100% !important;">  
					  <?php if(version_compare(get_bloginfo('version'),3.3)<0) {?>
						<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
						<?php the_editor(stripslashes(""),"text_for_date","title" ); ?>
						</div>
						<?php }else{
						 wp_editor("", "text_for_date"); }?>
					  </div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="key"><label for="published1"><?php _e( 'Published', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <input type="radio" name="published" id="published0" value="0" class="inputbox">
                    <label for="published0"><?php _e( 'No' ); ?></label>
                    <input type="radio" name="published" id="published1" value="1" checked="checked" class="inputbox">
                    <label for="published1"><?php _e( 'Yes' ); ?></label>
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </td>
        <td style="padding-left:25px; vertical-align:top !important; width:45%">
          <div style="width:100%">
            <fieldset class="adminform" style="margin-left: -25px;"><legend><?php _e( 'Repeat Event', 'sp_calendar' ); ?></legend>
              <table>
                <tr>
                  <td valign="top">
					<input type="radio" id="no_repeat_type" value="no_repeat" name="repeat_method" checked="checked" onChange="change_type('no_repeat')">
					<label for="no_repeat_type"><?php _e( "Don't repeat this event", 'sp_calendar' ); ?></label>
                    <br/>
                    <input type="radio" id="daily_type" value="daily" name="repeat_method" onChange="change_type('daily');">
					<label for="daily_type"><?php _e( 'Repeat daily', 'sp_calendar' ); ?></label>
					<br/>
                    <input type="radio"  id="weekly_type" value="weekly" name="repeat_method" onChange="change_type('weekly');">
					<label for="weekly_type"><?php _e( 'Repeat weekly', 'sp_calendar' ); ?></label>
					<br/>
                    <input type="radio"  id="monthly_type" value="monthly" name="repeat_method" onChange="change_type('monthly');">
					<label for="monthly_type"><?php _e( 'Repeat monthly', 'sp_calendar' ); ?></label>
					<br/>
                    <input type="radio"  id="yearly_type" value="yearly" name="repeat_method" onChange="change_type('yearly');">
					<label for="yearly_type"><?php _e( 'Repeat yearly', 'sp_calendar' ); ?></label>
					<br/>
                  </td>
                  <td style="padding-left:10px" valign="top">
                    <div id="daily" style="display:none"><?php _e( 'Repeat every', 'sp_calendar' ); ?>
                      <input type="text" id="repeat_input" size="5" name="repeat" onClick="return input_repeat()" onKeyPress="return checknumber(repeat_input)" value="1"/>
                      <label id="repeat"></label>
                      <label id="year_month" style="display:none;">
                        <select name="year_month" id="year_month" class="inputbox">
                          <option value="1" selected="selected"><?php _e( 'January' ); ?></option>
                          <option value="2"><?php _e( 'February' ); ?></option>
                          <option value="3"><?php _e( 'March' ); ?></option>
                          <option value="4"><?php _e( 'April' ); ?></option>
                          <option value="5"><?php _e( 'May' ); ?></option>
                          <option value="6"><?php _e( 'June' ); ?></option>
                          <option value="7"><?php _e( 'July' ); ?></option>
                          <option value="8"><?php _e( 'August' ); ?></option>
                          <option value="9"><?php _e( 'September' ); ?></option>
                          <option value="10"><?php _e( 'October' ); ?></option>
                          <option value="11"><?php _e( 'November' ); ?></option>
                          <option value="12"><?php _e( 'December' ); ?></option>
                        </select>
                      </label>
                    </div>
                    <br/>
                    <input type="hidden" id="daily1"/>
                    <input type="hidden" id="weekly1"/>
                    <input type="hidden" id="monthly1"/>
                    <input type="hidden" id="yearly1"/>
                    <div class="key" id="weekly" style="display:none">
                      <input type="checkbox" value="Mon" id="week_1" onChange="week_value()"/><?php _e ( 'Mon' ); ?>
                      <input type="checkbox" value="Tue" id="week_2" onChange="week_value()"/><?php _e ( 'Tue' ); ?>
                      <input type="checkbox" value="Wed" id="week_3" onChange="week_value()"/><?php _e ( 'Wed' ); ?>
                      <input type="checkbox" value="Thu" id="week_4" onChange="week_value()"/><?php _e ( 'Thu' ); ?>
                      <input type="checkbox" value="Fri" id="week_5" onChange="week_value()"/><?php _e ( 'Fri' ); ?>
                      <input type="checkbox" value="Sat" id="week_6" onChange="week_value()"/><?php _e ( 'Sat' ); ?>
                      <input type="checkbox" value="Sun" id="week_7" onChange="week_value()"/><?php _e ( 'Sun' ); ?>
                      <input type="hidden" name="week" id="week"/>
                    </div>
                    <br/>
                    <div class="key" id="monthly" style="display:none">
                      <input type="radio" id="radio1" onChange="radio_month()" name="month_type" value="1" checked="checked"/><?php _e( 'on the', 'sp_calendar' ); ?>:  
                      <input type="text" onKeyPress="return checknumber(month)" name="month" size="3" id="month"/><br/>
                      <input type="radio" id="radio2" onChange="radio_month()" name="month_type" value="2"/><?php _e( 'on the', 'sp_calendar' ); ?>: 
                      <select name="monthly_list" id="monthly_list" class="inputbox">
                        <option value="1"><?php _e( 'First', 'sp_calendar' ); ?></option>
                        <option value="8"><?php _e( 'Second', 'sp_calendar' ); ?></option>
                        <option value="15"><?php _e( 'Third', 'sp_calendar' ); ?></option>
                        <option value="22"><?php _e( 'Fourth', 'sp_calendar' ); ?></option>
                        <option value="last"><?php _e( 'Last', 'sp_calendar' ); ?></option>
                      </select>
                      <select name="month_week" id="month_week" class="inputbox">
                        <option value="Mon"><?php _e( 'Monday' ); ?></option>
                        <option value="Tue"><?php _e( 'Tuesday' ); ?></option>
                        <option value="Wed"><?php _e( 'Wednesday' ); ?></option>
                        <option value="Thu"><?php _e( 'Thursday' ); ?></option>
                        <option value="Fri"><?php _e( 'Friday' ); ?></option>
                        <option value="Sat"><?php _e( 'Saturday' ); ?></option>
                        <option value="Sun"><?php _e( 'Sunday' ); ?></option>
                      </select>
                    </div>
                    <br/>
                    <script>
                      window.onload = radio_month();
                    </script>
                  </td>
                </tr>
                <tr id="repeat_until" style="display:none">
                  <td><?php _e( 'Repeat until', 'sp_calendar' ); ?>: </td>
                  <td>
                    <input style="width:90px" class="inputbox" type="text" name="date_end" id="date_end" size="10" maxlength="10" value=""/>
                    <input type="reset" class="wd_button" value="..." onClick="return showCalendar('date_end','%Y-%m-%d');"/>
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </td>
      </tr>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="option" value="com_spidercalendar"/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="calendar" value=""/>
  </form>
  <?php
}

function html_edit_spider_event($row, $calendar_id, $id, $cal_name) {
  global $wpdb;
  $calendar = $wpdb->get_row($wpdb->prepare ("SELECT * FROM " . $wpdb->prefix . "spidercalendar_calendar where id=%d", $calendar_id ));
  
  ?>
  <style>
    .calendar .button {
      display: table-cell !important;
    }
    fieldset {
      border: 2px solid #4f9bc6; /*#CCA383 1462a5*/
      width: 100%;
      background: #fafbfd;
      padding: 13px;
      margin-top: 20px;
      -webkit-border-radius: 8px;
      -moz-border-radius: 8px;
      border-radius: 8px;
  }
  .wd_button{
		border: 1px solid #D5D5D5 !important;
		border-radius: 10px;
		width: 30px;
		height: 25px;
	}
  </style>
  <script language="javascript" type="text/javascript">
    function submitform(pressbutton) {
	if (document.getElementById('title').value == "") {
					alert('<?php _e( 'Provide the title', 'sp_calendar' ); ?>');
				  }
  else {
      document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
      document.getElementById('adminForm').submit();
	  }
    }
    function submitbutton(pressbutton) {
      var form = document.adminForm;
      if (pressbutton == 'cancel_event') {
        submitform(pressbutton);
        return;
      }
      if (form.date.value.search(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/)) {
        alert('Invalid Date');
      }
      else if (form.date.value.search(/^[0-9]{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])/)) {
        alert('Invalid Date');
      }
      else if (form.selhour_from.value == "" && form.selminute_from.value == "" && form.selhour_to.value == "" && form.selminute_to.value == "") {
        submitform(pressbutton);
      }
      else if (form.selhour_from.value != "" && form.selminute_from.value != "" && form.selhour_to.value == "" && form.selminute_to.value == "") {
        submitform(pressbutton);
      }
      else if (form.selhour_from.value != "" && form.selminute_from.value != "" && form.selhour_to.value != "" && form.selminute_to.value != "") {
        submitform(pressbutton);
      }
      else {
        alert('Invalid Time');
      }
    }
      function checkhour(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      hour = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      hour = parseFloat(hour);
      if (document.getSelection() != '') {
        return true;
      }
      if ((hour < 0) || (hour > 23)) {
        return false;
      }
    }
    return true;
  }
  function check12hour(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      input = document.getElementById(id);
      if (charCode == 48 && input.value.length == 0) {
        return false;
      }
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      hour = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      hour = parseFloat(hour);
      if (document.getSelection() != '') {
        return true;
      }
      if ((hour < 0) || (hour > 12)) {
        return false;
      }
    }
    return true;
  }
  function checknumber(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
    }
    return true;
  }
  function checkminute(id,event) {
    if (typeof(event) != 'undefined') {
      var e = event; // for trans-browser compatibility
      var charCode = e.which || e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
      }
      minute = "" + document.getElementById(id).value + String.fromCharCode(e.charCode);
      minute = parseFloat(minute);
      if (document.getSelection() != '') {
        return true;
      }
      if ((minute < 0) || (minute > 59)) {
        return false;
      }
    }
    return true;
  }
    function add_0(id) {
      input = document.getElementById(id);
      if (input.value.length == 1) {
        input.value = '0' + input.value;
        input.setAttribute("value", input.value);
      }
    }
    function change_type(type) {
      if (document.getElementById('daily1').value == '') {
        document.getElementById('daily1').value = 1;
      }
      if (document.getElementById('weekly1').value == '') {
        document.getElementById('weekly1').value = 1;
      }
      if (document.getElementById('monthly1').value == '') {
        document.getElementById('monthly1').value = 1;
      }
      if (document.getElementById('yearly1').value == '') {
        document.getElementById('yearly1').value = 1;
      }
      switch (type) {
        case 'no_repeat':
          document.getElementById('daily').setAttribute('style', 'display:none');
          document.getElementById('weekly').setAttribute('style', 'display:none');
          document.getElementById('monthly').setAttribute('style', 'display:none');
          document.getElementById('year_month').setAttribute('style', 'display:none');
          document.getElementById('repeat_until').setAttribute('style', 'display:none');
          document.getElementById('month').value = '';
          document.getElementById('date_end').value = ''
          break;

        case 'daily':
          document.getElementById('daily').removeAttribute('style');
          document.getElementById('repeat_until').removeAttribute('style');
          document.getElementById('weekly').setAttribute('style', 'display:none');
          document.getElementById('monthly').setAttribute('style', 'display:none');
          document.getElementById('repeat').innerHTML = '<?php _e( 'Day(s)', 'sp_calendar' ); ?>';
          document.getElementById('repeat_input').onchange = function onchange(event) {
            return input_value('daily1')
          };
          document.getElementById('month').value = '';
          document.getElementById('year_month').setAttribute('style', 'display:none');
          document.getElementById('repeat_input').value = document.getElementById('daily1').value;
          break;

        case 'weekly':
          document.getElementById('daily').removeAttribute('style');
          document.getElementById('weekly').removeAttribute('style');
          document.getElementById('monthly').setAttribute('style', 'display:none');
          document.getElementById('repeat').innerHTML = '<?php _e( 'Week(s) on :', 'sp_calendar' ); ?>';
          document.getElementById('repeat_input').onchange = function onchange(event) {
            return input_value('weekly1')
          };
          document.getElementById('month').value = '';
          document.getElementById('year_month').setAttribute('style', 'display:none');
          document.getElementById('repeat_until').removeAttribute('style');
          document.getElementById('repeat_input').value = document.getElementById('weekly1').value;
          break;

        case 'monthly':
          document.getElementById('daily').removeAttribute('style');
          document.getElementById('weekly').setAttribute('style', 'display:none');
          document.getElementById('monthly').removeAttribute('style');
          document.getElementById('repeat').innerHTML = '<?php _e( 'Month(s)', 'sp_calendar' ); ?>'
          document.getElementById('repeat_input').value = document.getElementById('monthly1').value;
          document.getElementById('month').value = '';
          document.getElementById('year_month').setAttribute('style', 'display:none');
          document.getElementById('repeat_until').removeAttribute('style');
          document.getElementById('repeat_input').onchange = function onchange(event) {
            return input_value('monthly1')
          };
          break;

        case 'yearly':
          document.getElementById('daily').removeAttribute('style');
          document.getElementById('year_month').removeAttribute('style');
          document.getElementById('weekly').setAttribute('style', 'display:none');
          document.getElementById('monthly').removeAttribute('style');
          document.getElementById('repeat').innerHTML = '<?php _e( 'Year(s) in ', 'sp_calendar' ); ?>';
          document.getElementById('repeat_input').value = document.getElementById('yearly1').value;
          document.getElementById('month').value = '';
          document.getElementById('repeat_until').removeAttribute('style');
          document.getElementById('repeat_input').onchange = function onchange(event) {
            return input_value('yearly1')
          };
          break;
      }
    }
    function week_value() {
      var value = '';
      for (i = 1; i <= 7; i++) {
        if (document.getElementById('week_' + i).checked) {
          value = value + document.getElementById('week_' + i).value + ',';
        }
      }
      document.getElementById('week').value = value;
    }
    function radio_month() {
      if (document.getElementById('radio1').checked == true) {
        document.getElementById('monthly_list').disabled = true;
        document.getElementById('month_week').disabled = true;
        document.getElementById('month').disabled = false;
      }
      else {
        document.getElementById('month').disabled = true;
        document.getElementById('monthly_list').disabled = false;
        document.getElementById('month_week').disabled = false;
      }
    }
    function input_value(id) {
      document.getElementById(id).value = document.getElementById('repeat_input').value;
    }
  </script>
  <?php $query = $wpdb->get_results("SELECT " . $wpdb->prefix . "spidercalendar_event.category, " . $wpdb->prefix . "spidercalendar_event_category.title as cattitle FROM " . $wpdb->prefix . "spidercalendar_event JOIN " . $wpdb->prefix . "spidercalendar_event_category ON " . $wpdb->prefix . "spidercalendar_event.category=" . $wpdb->prefix . "spidercalendar_event_category.id");

  $query2 = $wpdb->get_results("SELECT title,id FROM " . $wpdb->prefix . "spidercalendar_event_category");
  ?>
  <table width="95%">
    <tr>
      <td width="100%" style="font-size:14px; font-weight:bold">
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'User Manual', 'sp_calendar' ); ?></a>
        <br />
        <?php _e( 'This section allows you to create/edit the events of a particular calendar.', 'sp_calendar' ); ?><br/> <?php _e( 'You can add unlimited number of events for each calendar.', 'sp_calendar' ); ?>
        <a href="https://web-dorado.com/spider-calendar-wordpress-guide-step-3.html" target="_blank" style="color:blue; text-decoration:none;"><?php _e( 'More...', 'sp_calendar' ); ?></a>
      </td>
    </tr>
    <tbody>
      <tr>
        <td width="100%"><h2><?php sprintf( __( 'Edit an event for calendar %s', 'sp_calendar' ), '<font style="color:red">' . $cal_name . '</font>' ); ?></h2></td>
        <td align="right"><input type="button" onClick="submitbutton('save_event')" value="<?php _e( 'Save' ); ?>" class="button-secondary action"></td>
        <td align="right"><input type="button" onClick="submitbutton('apply_event')" value="<?php _e( 'Apply' ); ?>" class="button-secondary action"></td>
        <td align="right"><input type="button" onClick="window.location.href='admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&task=show_manage_event'" value="<?php _e( 'Cancel' ); ?>" class="button-secondary action"></td>
      </tr>
    </tbody>
  </table>

  <form action="admin.php?page=SpiderCalendar&calendar_id=<?php echo $calendar_id; ?>&id=<?php echo $id; ?>" method="post" id="adminForm" name="adminForm">
    <table width="95%">
      <tr>
        <td style="width:45%">
          <div style="width:95%">
            <fieldset class="adminform">
              <legend><?php _e( 'Event Details', 'sp_calendar' ); ?></legend>
              <table class="admintable">
                <tr>
                  <td class="key"><label for="message"><?php _e( 'Title' ); ?>: </label></td>
                  <td><input type="text" id="title" name="title" size="41" value="<?php echo htmlspecialchars($row->title, ENT_QUOTES); ?>"/></td>
                </tr>
				<tr>
                  <td class="key"><label for="category"><?php _e( 'Select Category', 'sp_calendar' ); ?>: </label></td>
                  <td>
						<select id="category" name="category" style="width:240px">
							<option value="0" <?php if ($row->category == "0") echo 'selected="selected"'; ?>><?php if(isset($category)) echo $category->title ?>--<?php _e( 'Select Category', 'sp_calendar' ); ?>--</option>
							<?php foreach ($query2 as $key => $category) {
            ?>
            <option value="<?php echo $category->id; ?>"  <?php if ( $category->id == $row->category ) echo 'selected="selected"'; ?>><?php if(isset($category)) echo $category->title ?></option>
            <?php
          }
          ?>
							
						</select>
				  </td>
                </tr>
				
                <tr>
                  <td class="key"><label for="message"><?php _e( 'Date', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <input class="inputbox" style="width:90px" type="text" name="date" id="date" size="10" maxlength="10" value="<?php echo $row->date; ?>"/>
                    <?php
                    if ($row->date_end == '0000-00-00') {
                      $row->date_end = "";
                    }
                    ?>
                    <input type="reset" class="wd_button" value="..." onClick="return showCalendar('date','%Y-%m-%d');"/>
                  </td>
                </tr>
                <tr>
                  <td class="key"><label for="message"><?php _e( 'Time', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <?php
                    if (!$row->time) {
                      $from[0] = "";
                      $from[1] = "";
                      $to[0] = "";
                      $to[1] = "";
                    }
                    else {
                      $from_to = explode("-", $row->time);
                      $from = explode(":", $from_to[0]);
                      if (isset($from_to[1])) {
                        $to = explode(":", $from_to[1]);
                      }
                      else {
                        $to[0] = "";
                        $to[1] = "";
                      }
                    }
                    ?>
                    <?php if ($calendar->time_format == 0) { ?>
                    <input type="text" id="selhour_from" name="selhour_from" size="1" style="text-align:right"
                           onkeypress="return checkhour('selhour_from',event)" value="<?php echo $from[0]; ?>" title="<?php _e( 'From', 'sp_calendar' ); ?>"
                           onblur="add_0('selhour_from')"/> <b>:</b>
                    <input type="text" id="selminute_from" name="selminute_from" size="1" style="text-align:right"
                           onkeypress="return checkminute('selminute_from',event)" value="<?php echo substr($from[1], 0, 2); ?>"
                           title="<?php _e( 'From', 'sp_calendar' ); ?>" onBlur="add_0('selminute_from')"/> <span style="font-size:12px">&nbsp;-&nbsp;</span>
                    <input type="text" id="selhour_to" name="selhour_to" size="1" style="text-align:right"
                           onkeypress="return checkhour('selhour_to',event)" value="<?php echo $to[0]; ?>" title="<?php _e( 'To', 'sp_calendar' ); ?>"
                           onblur="add_0('selhour_to')"/> <b>:</b>
                    <input type="text" id="selminute_to" name="selminute_to" size="1" style="text-align:right"
                           onkeypress="return checkminute('selminute_to',event)" value="<?php echo substr($to[1], 0, 2); ?>"
                           title="<?php _e( 'To', 'sp_calendar' ); ?>" onBlur="add_0('selminute_to')"/>
                    <?php } 
					if ($calendar->time_format == 1) { ?>
                    <input type="text" id="selhour_from" name="selhour_from" size="1" style="text-align:right"
                           onkeypress="return check12hour('selhour_from',event)" value="<?php echo $from[0]; ?>" title="<?php _e( 'From', 'sp_calendar' ); ?>"
                           onblur="add_0('selhour_from')"/> <b>:</b>
                    <input type="text" id="selminute_from" name="selminute_from" size="1" style="text-align:right"
                           onkeypress="return checkminute('selminute_from',event)" value="<?php echo substr($from[1], 0, 2); ?>"
                           title="from" onBlur="add_0('selminute_from')"/>
                    <select id="select_from" name="select_from">
                      <option <?php if ( false !== strpos( substr( $from[1], 0, strpos( $from[1], '-' ) ), __( 'AM', 'sp_calendar' ) ) )
                        echo 'selected="selected"'; ?>><?php _e( 'AM', 'sp_calendar' ); ?>
                      </option>
                      <option <?php if ( false !== strpos( substr( $from[1], strpos( $from[1], '-' ) +1 ), __( 'PM', 'sp_calendar' ) ) )
                        echo 'selected="selected"'; ?>><?php _e( 'PM', 'sp_calendar' ); ?>
                      </option>
                    </select>
                    <span style="font-size:12px">&nbsp;-&nbsp;</span>
                    <input type="text" id="selhour_to" name="selhour_to" size="1" style="text-align:right"
                           onkeypress="return check12hour('selhour_to',event)" value="<?php echo $to[0]; ?>" title="<?php _e( 'To', 'sp_calendar' ); ?>"
                           onblur="add_0('selhour_to')"/> <b>:</b>
                    <input type="text" id="selminute_to" name="selminute_to" size="1" style="text-align:right"
                           onkeypress="return checkminute('selminute_to',event)" value="<?php echo substr($to[1], 0, 2); ?>"
                           title="<?php _e( 'To', 'sp_calendar' ); ?>" onBlur="add_0('selminute_to')"/>
                    <select id="select_to" name="select_to">
                      <option <?php if ( false !== strpos( substr( $to[1], 0, strpos( $to[1], '-' ) ), __( 'AM', 'sp_calendar' ) ) )
                        echo 'selected="selected"'; ?>><?php _e( 'AM', 'sp_calendar' ); ?>
                      </option>
                      <option <?php if ( false !== strpos( substr( $to[1], strpos( $to[1], '-' ) +1 ), __( 'PM', 'sp_calendar' ) ) )
                        echo 'selected="selected"';  ?>><?php _e( 'PM', 'sp_calendar' ); ?>
                      </option>
                    </select>
                    <?php }?>
                  </td>
                </tr>
                <tr>
                  <td class="key"><label for="note"><?php _e( 'Note', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <div id="poststuff" style="width:100% !important;"> 
						  <?php if(version_compare(get_bloginfo('version'),3.3)<0) {?>
							<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea"><?php the_editor(stripslashes($row->description),"text_for_date","title" ); ?>
							</div>
							<?php }else{
							if(isset($row->text_for_date)) $desc1 = $row->text_for_date;
							else $desc1 = "";
							wp_editor($desc1, "text_for_date"); }?>
						</div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td class="key"><label for="note"><?php _e( 'Published', 'sp_calendar' ); ?>: </label></td>
                  <td>
                    <input type="radio" name="published" id="published0" value="0" <?php cheched($row->published, '0'); ?> class="inputbox">
                    <label for="published0"><?php _e( 'No' ); ?></label>
                    <input type="radio" name="published" id="published1" value="1" <?php cheched($row->published, '1'); ?> class="inputbox">
                    <label for="published1"><?php _e( 'Yes' ); ?></label>
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </td>
        <td style="padding-left:25px; vertical-align:top !important; width:45%">
          <div style="width:100%">
            <fieldset class="adminform" style="margin-left: -25px;">
              <legend><?php _e( 'Repeat Event', 'sp_calendar' ); ?></legend>
              <table>
                <tr>
                  <td valign="top">
                    <input type="radio" value="no_repeat" id="no_repeat_type" name="repeat_method" <?php if ($row->repeat_method == 'no_repeat')
                      echo 'checked="checked"' ?> checked="checked" onChange="change_type('no_repeat')"/>
					  <label for="no_repeat_type"><?php _e( "Don't repeat this event", 'sp_calendar' ); ?></label><br/>
					  
                    <input type="radio" value="daily" id="daily_type" name="repeat_method" <?php if ($row->repeat_method == 'daily')
                      echo 'checked="checked"' ?>  onchange="change_type('daily')"/>
					  <label for="daily_type"><?php _e( 'Repeat daily', 'sp_calendar' ); ?></label><br/>
					  
                    <input type="radio" value="weekly" id="weekly_type" name="repeat_method" <?php if ($row->repeat_method == 'weekly')
                      echo 'checked="checked"' ?> onChange="change_type('weekly')"/>
					  <label for="weekly_type"><?php _e( 'Repeat weekly', 'sp_calendar' ); ?></label>
					<br/>
                    <input type="radio" value="monthly" id="monthly_type" name="repeat_method" <?php if ($row->repeat_method == 'monthly')
                      echo 'checked="checked"'?> onChange="change_type('monthly')"/>
					  <label for="monthly_type"><?php _e( 'Repeat monthly', 'sp_calendar' ); ?></label>
					<br/>
                    <input type="radio" value="yearly" id="yearly_type" name="repeat_method" <?php if ($row->repeat_method == 'yearly')
                      echo 'checked="checked"' ?> onChange="change_type('yearly')"/>
					  <label for="yearly_type"><?php _e( 'Repeat yearly', 'sp_calendar' ); ?></label>
					<br/>
                  </td>
                  <td style="padding-left:10px" valign="top">
                    <div id="daily" style="display:<?php if ($row->repeat_method == 'no_repeat') echo 'none'; ?>">
                      <?php _e( 'Repeat every', 'sp_calendar' ); ?> <input type="text" id="repeat_input" size="5" name="repeat" onKeyPress="return checknumber(repeat_input)" value="<?php echo $row->repeat ?>"/>
                      <label id="repeat"><?php if ($row->repeat_method == 'daily')
                        _e( 'Day(s)', 'sp_calendar' );
                        if ($row->repeat_method == 'weekly')
                          _e( 'Week(s) on :', 'sp_calendar' );
                        if ($row->repeat_method == 'monthly')
                          _e( 'Month(s)', 'sp_calendar' );
                        if ($row->repeat_method == 'yearly')
                          _e( 'Year(s) in', 'sp_calendar' );
                        ?></label>
                      <label id="year_month" style="display:<?php if ($row->repeat_method != 'yearly') echo 'none'; ?>">
                      <select name="year_month" id="year_month" class="inputbox">
                        <option value="1" <?php echo selectted($row->year_month, '1'); ?>><?php _e( 'January' ); ?></option>
                        <option value="2" <?php echo selectted($row->year_month, '2'); ?>><?php _e( 'February' ); ?></option>
                        <option value="3" <?php echo selectted($row->year_month, '3'); ?>><?php _e( 'March' ); ?></option>
                        <option value="4" <?php echo selectted($row->year_month, '4'); ?>><?php _e( 'April' ); ?></option>
                        <option value="5" <?php echo selectted($row->year_month, '5'); ?>><?php _e( 'May' ); ?></option>
                        <option value="6" <?php echo selectted($row->year_month, '6'); ?>><?php _e( 'June' ); ?></option>
                        <option value="7" <?php echo selectted($row->year_month, '7'); ?>><?php _e( 'July' ); ?></option>
                        <option value="8" <?php echo selectted($row->year_month, '8'); ?>><?php _e( 'August' ); ?></option>
                        <option value="9" <?php echo selectted($row->year_month, '9'); ?>><?php _e( 'September' ); ?></option>
                        <option value="10" <?php echo selectted($row->year_month, '10'); ?>><?php _e( 'October' ); ?></option>
                        <option value="11" <?php echo selectted($row->year_month, '11'); ?>><?php _e( 'November' ); ?></option>
                        <option value="12" <?php echo selectted($row->year_month, '12'); ?>><?php _e( 'December' ); ?></option>
                      </select></label>
                      <input type="hidden" value="<?php if ($row->repeat_method == 'daily') echo $row->repeat; ?>" id="daily1"/>
                      <input type="hidden" value="<?php if ($row->repeat_method == 'weekly') echo $row->repeat; ?>" id="weekly1"/>
                      <input type="hidden" value="<?php if ($row->repeat_method == 'monthly') echo $row->repeat; ?>" id="monthly1"/>
                      <input type="hidden" value="<?php if ($row->repeat_method == 'yearly') echo $row->repeat; ?>" id="yearly1"/>
                    </div>
                    <br/>
                    <div class="key" id="weekly" style="display:<?php if ($row->repeat_method != 'weekly') echo 'none'; ?>">
                      <input type="checkbox" value="Mon" id="week_1" onChange="week_value()" <?php if (in_array('Mon', explode(',', $row->week))) echo 'checked="checked"' ?>   /><?php _e( 'Mon' ); ?>
                      <input type="checkbox" value="Tue" id="week_2" onChange="week_value()" <?php if (in_array('Tue', explode(',', $row->week))) echo 'checked="checked"' ?>   /><?php _e( 'Tue' ); ?>
                      <input type="checkbox" value="Wed" id="week_3" onChange="week_value()" <?php if (in_array('Wed', explode(',', $row->week))) echo 'checked="checked"' ?> /><?php _e( 'Wed' ); ?>
                      <input type="checkbox" value="Thu" id="week_4" onChange="week_value()" <?php if (in_array('Thu', explode(',', $row->week))) echo 'checked="checked"' ?>  /><?php _e( 'Thu' ); ?>
                      <input type="checkbox" value="Fri" id="week_5" onChange="week_value()" <?php if (in_array('Fri', explode(',', $row->week))) echo 'checked="checked"' ?> /><?php _e( 'Fri' ); ?>
                      <input type="checkbox" value="Sat" id="week_6" onChange="week_value()" <?php if (in_array('Sat', explode(',', $row->week))) echo 'checked="checked"' ?>  /><?php _e( 'Sat' ); ?>
                      <input type="checkbox" value="Sun" id="week_7" onChange="week_value()" <?php if (in_array('Sun', explode(',', $row->week))) echo 'checked="checked"' ?> /><?php _e( 'Sun' ); ?>
                      <input type="hidden" name="week" id="week" value="<?php echo $row->week ?>"/>
                    </div>
                    <br/>
                    <div class="key" id="monthly" style="display:<?php if ($row->repeat_method != 'monthly' && $row->repeat_method != 'yearly') echo 'none'; ?>">
                      <input type="radio" id="radio1" name="month_type" onChange="radio_month()" value="1" checked="checked" <?php if ($row->month_type == 1)
                        echo 'checked="checked"' ?>  /><?php _e( 'on the', 'sp_calendar' ); ?>: <input type="text" name="month" size="3" onKeyPress="return checknumber(month)" id="month"
                                                                      value="<?php echo $row->month; ?>"/><br/>
                      <input type="radio" id="radio2" name="month_type" onChange="radio_month()" value="2" <?php if ($row->month_type == 2) echo 'checked="checked"'; ?> /><?php _e( 'on the', 'sp_calendar' ); ?>:
                      <select name="monthly_list" id="monthly_list" class="inputbox">
                        <option <?php echo selectted($row->monthly_list, '1'); ?> value="1"><?php _e( 'First', 'sp_calendar' ); ?></option>
                        <option <?php echo selectted($row->monthly_list, '8'); ?> value="8"><?php _e( 'Second', 'sp_calendar' ); ?></option>
                        <option <?php echo selectted($row->monthly_list, '15'); ?> value="15"><?php _e( 'Third', 'sp_calendar' ); ?></option>
                        <option <?php echo selectted($row->monthly_list, '22'); ?> value="22"><?php _e( 'Fourth', 'sp_calendar' ); ?></option>
                        <option <?php echo selectted($row->monthly_list, 'last'); ?> value="last"><?php _e( 'Last', 'sp_calendar' ); ?></option>
                      </select>
                      <select name="month_week" id="month_week" class="inputbox">
                        <option <?php echo selectted($row->month_week, 'Mon'); ?> value="Mon"><?php _e( 'Monday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Tue'); ?> value="Tue"><?php _e( 'Tuesday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Wed'); ?> value="Wed"><?php _e( 'Wednesday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Thu'); ?> value="Thu"><?php _e( 'Thursday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Fri'); ?> value="Fri"><?php _e( 'Friday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Sat'); ?> value="Sat"><?php _e( 'Saturday' ); ?></option>
                        <option <?php echo selectted($row->month_week, 'Sun'); ?> value="Sun"><?php _e( 'Sunday' ); ?></option>
                      </select>
                    </div>
                    <br/>
                    <script>
                      window.onload = radio_month();
                    </script>
                  </td>
                </tr>
                <tr id="repeat_until" style="display:<?php if ($row->repeat_method == 'no_repeat') echo 'none'; ?>">
                  <td><?php _e( 'Repeat until', 'sp_calendar' ); ?>: </td>
                  <?php
                  if ($row->date_end == '2070-12-12') {
                    $row->date_end = '';
                  }
                  ?>
                  <td>
                    <input style="width:90px" class="inputbox" type="text" name="date_end" id="date_end" size="10" maxlength="10" value="<?php echo $row->date_end; ?>"/>
                    <input type="reset" class="wd_button" value="..." onClick="return showCalendar('date_end','%Y-%m-%d');"/>
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </td>
      </tr>
    </table>
	<?php wp_nonce_field('nonce_sp_cal', 'nonce_sp_cal'); ?>
    <input type="hidden" name="option" value="com_spidercalendar"/>
    <input type="hidden" name="id" value="<?php echo $row->id; ?>"/>
    <input type="hidden" name="cid[]" value="<?php echo $row->id; ?>"/>
    <input type="hidden" name="task" value="event"/>
    <input type="hidden" name="calendar" value=""/>
  </form> <?php } ?>