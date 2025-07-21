<?php $strPageTitle = t('Menu management'); ?>

<?php require('header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<!-- BEGIN CONTENT -->
<div class="page-content">
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            <div class="content-body">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _t('Menu management') ?></h3>
                    <div class="row">
                        <div class="form-group col-md-2 center-button">
                            <?= _r($this->btnAddMenuItem); ?>
                        </div>
                        <div class="form-group col-md-5 center-button">
                            <?= _r($this->txtMenuText); ?>
                        </div>
                        <div class="form-group col-md-5 center-button">
                            <?= _r($this->btnSave); ?>
                            <?= _r($this->btnCancel); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 center-button" style="margin-top: 10px;">
                            <?= _r($this->btnCollapseAll); ?>
                            <?= _r($this->btnExpandAll); ?>
                        </div>
                    </div>
                </div>
                <!-- MENU CONTAINER BEGIN -->
                <div class="panel-body">
                    <?= _r($this->lblHomePageAlert); ?>
                    <!-- MENU BEGIN -->
                    <?= _r($this->tblSorter); ?>
                    <!-- MENU END -->
                </div>
                <!-- MENU CONTAINER BEGIN -->
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
<!-- BEGIN CONTENT -->

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>
