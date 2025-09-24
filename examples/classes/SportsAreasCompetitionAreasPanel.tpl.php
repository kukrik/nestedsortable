<style>
    .select2-container--web-vauu .select2-results__option[aria-disabled=true] {display: none;}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Sports and Competition Areas Linking') ?></span>
        </div>
    </div>
    <div class="row">
        <div class="buttons-heading">
            <div class="row">
                <?= _r($this->btnRefresh); ?>
                <?= _r($this->btnAddAreas); ?>
                <div class="js-mapping-activities hidden">
                    <div class="col-md-3"><?= _r($this->lstSportsAreas); ?></div>
                    <div class="col-md-3"><?= _r($this->lstSportsCompetitionAreas); ?></div>
                    <div class="col-md-3">
                        <?= _r($this->btnSaveNew); ?>
                        <?= _r($this->btnCancelNew); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <?= _r($this->lblInfo); ?>
        <?= _r($this->lblWarning); ?>
            <div class="row">
                <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
                <div class="col-md-3" style="margin-top: -7px; margin-bottom: 15px;"><?= _r($this->txtFilter); ?></div>
                <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
                <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgAreas->Paginator); ?></div>
            </div>
             <?= _r($this->dtgAreas); ?>
            <div class="row">
                <div class="col-md-12">
                    <?= _r($this->txtSportsArea); ?>
                    <?= _r($this->txtCompetitionArea); ?>
                    <?= _r($this->btnDelete); ?>
                    <?= _r($this->btnCancel); ?>
                </div>
            </div>
    </div>
</div>