<style>
    .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
    .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0; padding: 15px;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .add-wrapper {display: block; margin-top: 15px; padding: 0 0 15px; border-bottom: #ddd 1px solid;/* scroll-margin-top: 80px;*/}
    .setting-wrapper {margin-top: 0; padding: 30px 0 10px; scroll-margin: 0;}
    .link-wrapper {margin-top: 15px;/*text-align: left;*/}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="panel-heading">
            <span class="vauu-title-3"><?php _t('Competition areas settings') ?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="add-wrapper">
                <?= _r($this->btnAddCompetitionArea); ?>
            </div>
        </div>
    </div>
    <div class="setting-wrapper hidden">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= _r($this->lblCompetitionArea); ?>
                    <div class="col-md-4">
                        <?= _r($this->txtCompetitionArea); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUnits); ?>
                    <div class="col-md-4">
                        <?= _r($this->lstUnits); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblIsDetailedResult); ?>
                    <div class="col-md-4">
                        <?= _r($this->lstIsDetailedResult); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblIsEnabled); ?>
                    <div class="col-md-4">
                        <?= _r($this->lstIsEnabled); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-actions fluid hidden">
                <div class="col-md-offset-4 col-md-9">
                    <?= _r($this->btnSave); ?>
                    <?= _r($this->btnDelete); ?>
                    <?= _r($this->btnCancel); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgCompetitionAreas->Paginator); ?></div>
        </div>
        <?= _r($this->dtgCompetitionAreas); ?>
    </div>
</div>