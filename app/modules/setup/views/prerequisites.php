<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">
        
        <div class="card-header">
            <?php echo lang('setup_prerequisites'); ?>
        </div>
        
        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <?php echo $this->layout->load_view('layout/includes/alerts'); ?>

            <p><?php echo lang('setup_prerequisites_message'); ?></p>

            <div class="m-y-2">
                <?php foreach ($basics as $basic) {
                    if ($basic['success']) {?>
                        <p>
                            <i class="fa fa-check text-success fa-margin-right"></i>
                            <?php echo $basic['message']; ?>
                        </p>
                    <?php } elseif ($basic['warning']) { ?>
                        <p>
                            <i class="fa fa-exclamation text-warning fa-margin-right"></i>
                            <?php echo $basic['message']; ?></p>
                    <?php } else { ?>
                        <p>
                            <i class="fa fa-close text-danger fa-margin-right"></i>
                            <?php echo $basic['message']; ?></p>
                    <?php }
                } ?>

                <br>

                <?php foreach ($writables as $writable) {
                    if ($writable['success']) { ?>
                        <p>
                            <i class="fa fa-check text-success fa-margin-right"></i>
                            <?php echo $writable['message']; ?>
                        </p>
                    <?php } else { ?>
                        <p>
                            <i class="fa fa-close text-danger fa-margin-right"></i>
                            <?php echo $writable['message']; ?>
                        </p>
                    <?php }
                } ?>
            </div>

            <?php if ($errors) { ?>
                <a href="javascript:location.reload()" class="btn btn-danger">
                    <i class="fa fa-warning fa-margin-right"></i> <?php echo lang('try_again'); ?>
                </a>
            <?php } else { ?>
                <button type="submit" class="btn btn-success" name="btn_continue" value="1">
                    <i class="fa fa-angle-right fa-margin-right"></i> <?php echo lang('continue'); ?>
                </button>
            <?php } ?>

        </form>
        
    </div>
</div>
