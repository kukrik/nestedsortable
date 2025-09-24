<div class="form-horizontal">
    <div class="table-body">
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->lblInfo); ?>
            </div>
        </div>
        <div class="row" style="margin-top: -15px;">
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2 js-years"><?= _r($this->lstYears); ?></div>
            <div class="col-md-2 js-groups"><?= _r($this->lstGroups); ?></div>
            <div class="col-md-2 js-types"><?= _r($this->lstContentTypes); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
        </div>
        <div class="row">
            <div class="col-md-1" style="margin-top: 15px; margin-bottom: 15px;"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-11" style="text-align: right; margin-top: 15px;"><?= _r($this->dtgSportsAreas->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSportsAreas); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnBack); ?>
            </div>
        </div>
    </div>
</div>









