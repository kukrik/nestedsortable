<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Albums manager') ?></span>
        </div>
    </div>
    <div class="row">
        <div class="buttons-heading">
            <div class="row">
                <div class="col-md-1"><?= _r($this->btnAddAlbum); ?></div>
                <div class="col-md-1 move-button-js"><?= _r($this->btnMove); ?></div>
                <div class="new-item-js">
                    <div class="col-md-3"><?= _r($this->lstGroupTitle); ?></div>
                    <div class="col-md-5"><?= _r($this->txtTitle); ?></div>
                    <div class="col-md-3">
                        <?= _r($this->btnSave); ?>
                        <?= _r($this->btnCancel); ?>
                    </div>
                </div>
                <div class="move-items-js">
                    <div class="col-md-3"><?= _r($this->lstGalleryLocked); ?></div>
                    <div class="col-md-3"><?= _r($this->lstTargetGroup); ?></div>
                    <div class="col-md-3"><?= _r($this->btnLockedCancel); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-3 js-groups"><?= _r($this->lstGroups); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="<?php echo $this->changeColClass ?>" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgGallery->Paginator); ?></div>
        </div>
        <?= _r($this->dtgGallery); ?>
        <div class="row">
            <div class="col-md-3"><?= _r($this->btnBack); ?></div>
            <div class="col-md-9" style="text-align: right;"><?= _r($this->dtgGallery->PaginatorAlternate); ?></div>
        </div>
    </div>
</div>