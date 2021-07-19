<style>
    table.form-table{
        margin-bottom:40px;
    }
</style>

<div class="wrap">
    <h1>Notificaciones</h1>

    <form action="options.php" method="post">
        <?php
            settings_fields('dcms_notif_options_bd');
            do_settings_sections('dcms_notif_sfields');
            submit_button();
        ?>
    </form>
</div>