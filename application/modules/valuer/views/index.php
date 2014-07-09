<div class="row">
    <div class="col-sm-6">
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>

        <?php echo form_open("valuer", array('role' => 'form')); ?>
        <div class="form-group <?php echo empty(form_error('domain')) ? '' : 'has-error'; ?>">
            <?php echo form_label('Domain:', 'domain'); ?>
            <?php echo form_input('domain', set_value('domain'), 'class="form-control" placeholder="Enter domain"'); ?>
        </div>
        <div class="form-group <?php echo empty(form_error('keyword')) ? '' : 'has-error'; ?>">
            <?php echo form_label('Keyword:', 'keyword'); ?>
            <?php echo form_input('keyword', set_value('keyword'), 'class="form-control" placeholder="Enter keyword"'); ?>
        </div>
        <?php echo form_submit('submit', 'Go!', 'class="btn btn-primary"'); ?>
        <?php echo form_close(); ?>
    </div>
</div>

<div class="row"><br></div>
<div class="row"><br></div>

<?php if (isset($rank)) : ?>
<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-primary">
            <div class="panel-heading">Result</div>
            <div class="panel-body">
                <div class="row">
                    <p class="col-sm-3 text-right">Domain:</p>
                    <p class="col-sm-3 text-left"><strong><?= $domain ?></strong></p>
                </div>
                <div class="row">
                    <p class="col-sm-3 text-right">Keyword:</p>
                    <p class="col-sm-3 text-left"><strong><?= $keyword ?></strong></p>
                </div>
                <div class="row">
                    <p class="col-sm-3 text-right">Rank:</p>
                    <p class="col-sm-3 text-left"><strong><?= is_numeric($rank) ? $rank : 'n/a' ?></strong></p>
                </div>
                <?php if ($rank): ?>
                    <div class="row">
                        <p class="col-sm-3 text-right">Page:</p>
                        <p class="col-sm-3 text-left"><strong><?= ceil($rank / 10) ?></strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
