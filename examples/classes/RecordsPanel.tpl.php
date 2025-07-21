<style>
    .record-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; padding: 15px; text-align: right;}
    .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
    .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px 0 -15px; padding: 15px;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .add-wrapper {display: block; margin-top: -15px; padding: 0 0 15px; border-bottom: #ddd 1px solid;}
    .setting-wrapper {margin-top: 0; padding: 30px 0 10px;}
    .link-wrapper {margin-top: 15px;/*text-align: left;*/}
    .modal-xl {width: 90%;}
    .is-best-record {background-color: #eaffea;}
    .interchangeable-record {background-color: #fff0f1;}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="panel-heading" style="margin-top: -15px;">
            <span class="vauu-title-3"><?php _t('Managing sports records') ?></span>
        </div>
    </div>
    <div class="js-wrapper-top"></div>
    <div class="tab-content-body">
        <div class="row equal">
            <div class="col-md-9 left-box padded-wrapper">
                <div class="add-wrapper">
                    <div class="row">
                        <div class="col-md-4">
                            <?= _r($this->lstSportsAreas); ?>
                        </div>
                        <div class="col-md-3">
                            <?= _r($this->btnAddNewRecord); ?>
                        </div>
                    </div>
                </div>
                <div class="setting-wrapper hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= _r($this->lblAthletesNames); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstAthletesNames); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblSportsAreas); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstNewSportsAreas); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblCompetitionAreas); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstCompetitionAreas); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblUnits); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstUnits); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblResult); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtResult); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblDifference); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtDifference); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblDetailedResult); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtDetailedResult); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <?= _r($this->lblCompetitionVenue); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtCompetitionVenue); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblCompetitionDate); ?>
                                <div class="col-md-5">
                                    <div class="input-group" role="group">
                                    <?= _r($this->dtxCompetitionDate); ?>
                                    <?= _r($this->btnCompetitionDate); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblStatus); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstStatus); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-actions-wrapper hidden" style="text-align: right;">
                        <?= _r($this->btnCheckConfirm); ?>
                        <?= _r($this->btnDelete); ?>
                        <?= _r($this->btnCancel); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="link-wrapper">
                            <?= _r($this->lblWarning); ?>
                            <?= _r($this->lblInfo); ?>
                            <div class="table-body">
<!--                                <div class="row">-->
<!--                                    <div class="col-md-1">--><?php //= _r($this->lstItemsPerPageByAssignedUserObject); ?><!--</div>-->
<!--                                    <div class="col-md-3" style="margin-top: -7px;">--><?php //= _r($this->txtFilter); ?><!--</div>-->
<!--                                    <div class="col-md-8" style="text-align: right; margin-bottom: 15px;">--><?php //= _r($this->dtgAthletes->Paginator); ?><!--</div>-->
<!--                                </div>-->
<!--                                --><?php //= _r($this->dtgAthletes); ?>

                                <div class="row">
                                    <div class="col-md-3"><?= _r($this->btnBack); ?></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 right-box padded-wrapper">
                <div class="form-group">
                    <?= _r($this->lblPostDate); ?>
                    <?= _r($this->calPostDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblPostUpdateDate); ?>
                    <?= _r($this->calPostUpdateDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblAuthor); ?>
                    <?= _r($this->txtAuthor); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUsersAsEditors); ?>
                    <?= _r($this->txtUsersAsEditors); ?>
                </div>
            </div>
        </div>
    </div>
</div>