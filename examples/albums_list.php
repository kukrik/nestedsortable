<?php
    require_once('qcubed.inc.php');

    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed\Exception\Caller;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Control\DataRepeater;

    /**
     * A form class for displaying a list of albums using a DataRepeater control.
     * Extends the base Form class to define specific behavior and layout for the album list.
     */
    class AlbumsListForm extends Form
    {
        protected DataRepeater $dtrGalleryList;

        /**
         * Initializes and sets up the form components, including the DataRepeater object,
         * template file, data binder function, and wrapper usage.
         *
         * @return void
         * @throws Caller
         */
        protected function formCreate(): void
        {
            $this->dtrGalleryList = new DataRepeater($this);
            $this->dtrGalleryList->Template = 'dtr_GalleryList.tpl.php';
            $this->dtrGalleryList->setDataBinder('dtrGalleryList_Bind');
            $this->dtrGalleryList->UseWrapper = false;
        }

        /**
         * Binds the gallery list data to the data source.
         *
         * @return void
         * @throws Caller
         */
        public function dtrGalleryList_Bind(): void
        {
            $this->dtrGalleryList->DataSource = GalleryList::loadAll();
        }
    }
    AlbumsListForm::run('AlbumsListForm');