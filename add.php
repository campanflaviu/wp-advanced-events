<?php

    if(isset($_REQUEST['name']) && $_REQUEST['name'] !== '') {
        $wpdb->insert($wpdb->prefix . 'wpae_events', array(
            'time' => current_time('mysql', 1),
            'name' => $_REQUEST['name']
        ));
    }
?>
<form "<?php echo admin_url( 'admin.php?page='.$_REQUEST['page'] ); ?>" method='post'>
    <h2>Add Events</h2>
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><label for="name">Name</label></th>
            <td><input type="text" name="name" id="name"/></td>
        </tr>
    </table>
    <?php
    submit_button();
    ?>
</form>