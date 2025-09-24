<?php
    require_once('qcubed.inc.php');
    require_once('../src/Control/NanoGalleryBase.php');;


    error_reporting(E_ALL); // Error engine - always ON!
    ini_set('display_errors', TRUE); // Error display - OFF in production env or real server
    ini_set('log_errors', TRUE); // Error logging

    use QCubed as Q;
    use QCubed\Project\Control\FormBase as Form;
    use QCubed\Plugin\Control\NanoGallery;
    use QCubed\Bootstrap as Bs;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Event\Click;
    use QCubed\Action\Ajax;
    use QCubed\Action\ActionParams;
    use QCubed\Project\Application;
    use QCubed\Query\QQ;

    /**
     * ShowGalleryForm is responsible for rendering and managing a gallery interface.
     * It initializes a NanoGallery component to display a collection of items and provides
     * functionalities such as bindings, button actions, and overall gallery configuration.
     */
    class ShowGalleryForm extends Form
    {
        protected NanoGallery $objGallery;
        protected Q\Plugin\Label $lblTitle;
        protected Bs\Button $btnBack;
        protected int $intAlbumList;
        protected object $objAlbumList;

        /**
         * Initializes and configures the form and its components, including the gallery, labels, and buttons.
         *
         * This method sets up a NanoGallery instance with specific settings, such as thumbnail dimensions, alignment,
         * and display modes. It also creates a label to display the gallery title and a back button with an AJAX click action.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function formCreate(): void
        {
            $this->intAlbumList = Application::instance()->context()->queryStringItem('id');
            if ($this->intAlbumList) {
                $this->objAlbumList = GalleryList::load($this->intAlbumList);
            }

            $this->objGallery = new Q\Plugin\Control\NanoGallery($this);
            $this->objGallery->createNodeParams([$this, 'AlbumList_Draw']);
            $this->objGallery->setDataBinder('AlbumList_Bind');

            $this->objGallery->ItemsBaseURL = $this->objGallery->TempUrl . '/_files';
            $this->objGallery->ThumbnailWidth = 200;
            $this->objGallery->ThumbnailHeight = 150;
            $this->objGallery->ThumbnailBorderVertical = 0;
            $this->objGallery->ThumbnailBorderHorizontal = 0;
            $this->objGallery->ThumbnailGutterWidth = 15;
            $this->objGallery->ThumbnailGutterHeight = 15;
            $this->objGallery->ThumbnailAlignment = 'center';
            $this->objGallery->ImageTransition = 'swipe';
            $this->objGallery->GalleryDisplayMode = 'rows';
            $this->objGallery->GalleryMaxRows = 1;
            $this->objGallery->GalleryMaxItems = null;
            $this->objGallery->ThumbnailLabel = [
                "position" => "onBottom","display" => false
            ];
            $this->objGallery->ViewerToolbar = [
                "display" => true, "standard"=> "label", "fullWidth" => true, "minimized" =>  "minimizeButton, label, fullscreenButton, downloadButton, infoButton"
            ];
            $this->objGallery->ViewerTools = [
                "topLeft" => "pageCounter",
                "topRight" => "playPauseButton, zoomButton, rotateLeftButton, rotateRightButton, fullscreenButton, shareButton, downloadButton, closeButton"
            ];

            $this->objGallery->LocationHash = false;

            $this->lblTitle = new Q\Plugin\Label($this);
            $this->lblTitle->Text = $this->objAlbumList->Title;
            $this->lblTitle->setCssStyle('font-weight', 400);
            $this->lblTitle->UseWrapper = false;

            $this->btnBack = new Bs\Button($this);
            $this->btnBack->Text = t('Back');
            $this->btnBack->CssClass = 'btn btn-default';
            $this->btnBack->UseWrapper = false;
            $this->btnBack->addAction(new Click(), new Ajax( 'btnBack_Click'));
        }

        /**
         * Binds the data source to the gallery for album display.
         *
         * This method assigns a query result as the data source to the gallery component.
         * The query retrieves a specific album list and its associated galleries based on the album ID.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        protected function AlbumList_Bind(): void
        {
            $this->objGallery->DataSource = GalleryList::queryArray(
                QQ::Equal(QQN::GalleryList()->Id, $this->intAlbumList),
                QQ::clause(QQ::expand(QQN::GalleryList()->Album))
            );
        }

        /**
         * Prepares and returns an associative array containing details about the given gallery list and its associated album.
         *
         * This method extracts information such as descriptions, authors, path, and status from the provided gallery list
         * and album, and organizes it into a structured array.
         *
         * @param GalleryList $objList The gallery list object whose details are to be extracted.
         *
         * @return array An associative array containing the gallery list and album details, including:
         *               - 'list_description': Description of the gallery list.
         *               - 'list_author': Author of the gallery list.
         *               - 'path': Path to the album associated with the gallery list.
         *               - 'description': Description of the album.
         *               - 'author': Author of the album.
         *               - 'status': Status of the album.
         */
        public function AlbumList_Draw(GalleryList $objList): array
        {
            $a['list_description'] = $objList->ListDescription;
            $a['list_author'] = $objList->ListAuthor;
            $a['path'] = $objList->Album->Path;
            $a['description'] = $objList->Album->Description;
            $a['author'] = $objList->Album->Author;
            $a['status'] = $objList->Album->Status;
            return $a;
        }

        /**
         * Handles the 'Back' button click action and redirects the user to the list page.
         *
         * This method is triggered when the 'Back' button is clicked, and it performs
         * a redirection to the 'list.php' page using the Application object.
         *
         * @param ActionParams $params The parameters passed from the button click event.
         *
         * @return void
         * @throws Throwable
         */
        protected function btnBack_Click(ActionParams $params): void
        {
            Application::redirect('albums_list.php');
        }
    }
    ShowGalleryForm::run('ShowGalleryForm');