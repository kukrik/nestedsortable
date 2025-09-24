<style>
    .select2-container--web-vauu .select2-results__option[aria-disabled=true] {
        display: none;
    }
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Event calendar manager') ?></span>
        </div>
    </div>
    <div class="row">
        <div class="buttons-heading">
            <div class="row">
                <div class="col-md-1"> <?= _r($this->btnAddEvent); ?></div>
                <div class="col-md-1 move-button-js"><?= _r($this->btnMove); ?></div>
                <div class="new-item-js">
                    <div class="col-md-1"><?= _r($this->txtYear); ?></div>
                    <div class="col-md-3"><?= _r($this->lstGroupTitle); ?></div>
                    <div class="col-md-4"><?= _r($this->txtTitle); ?></div>
                    <div class="col-md-3">
                        <?= _r($this->btnSave); ?>
                        <?= _r($this->btnCancel); ?>
                    </div>
                </div>
                <div class="move-items-js">
                    <div class="col-md-3"><?= _r($this->lstEventsLocked); ?></div>
                    <div class="col-md-3"><?= _r($this->lstTargetGroup); ?></div>
                    <div class="col-md-3"><?= _r($this->btnLockedCancel); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <div class="row" style="margin-top: -15px;">
            <div class="col-md-2" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2 js-years" style="width: 12%;"><?= _r($this->lstYears); ?></div>
            <div class="col-md-3 js-groups" style="width: 21%;"><?= _r($this->lstGroups); ?></div>
            <div class="col-md-2 js-targets" style="width: 16%;"><?= _r($this->lstTargets); ?></div>
            <div class="col-md-2 js-changes" style="width: 15%;"><?= _r($this->lstChanges); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
        </div>
        <div class="row">
            <div class="col-md-1" style="margin-top: 15px; margin-bottom: 15px;"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-11" style="text-align: right; margin-top: 15px;"><?= _r($this->dtgEventsCalendars->Paginator); ?></div>
        </div>
        <?= _r($this->dtgEventsCalendars); ?>
        <div class="row">
            <div class="col-md-3"><?= _r($this->btnBack); ?></div>
            <div class="col-md-9" style="text-align: right;"><?= _r($this->dtgEventsCalendars->PaginatorAlternate); ?></div>
        </div>
    </div>
</div>