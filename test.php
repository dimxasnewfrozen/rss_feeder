<?php
class ImageBrowseContainer extends BrowseContainer {

	private $view = 'normal';
	private $imageSql, $tagSql;
	private $permissionSql;
	private $projectSql;
	private $imagePerms;
	private $imageCount;
	private $collectionList;
	private $breadcrumbs;
	
	private $selected_rows, $selected_columns, $selected_row_group, $selected_column_group, $selections;
	private $gridTiles;
	private $selectionType;

	public function __construct()
	{
		$this->prefix = 'images_';
	}

	private function updateCollectionList()
	{
		$request = $this->app->request();
		$this->collectionList->processRequest($request);
	}

	private function handleTagGridSelections() {
			
			
			
		/*******************************************************************************/
		# 		VARIABLES USED FOR THE TAG GRID	     									#
			if( $form_selected_rows = $this->app->request()->postVar('rows_tag')) {
				$this->app->session( )->save( 'rows_tag', $this->selected_rows = $form_selected_rows );
			}
			elseif( $form_selected_rows = $this->app->session( )->get( 'rows_tag' ) ) {
				$this->selected_rows = $form_selected_rows;
			}
			
			if( $form_selected_columns = $this->app->request()->postVar('columns_tag') ) {
				$this->app->session( )->save( 'columns_tag', $this->selected_columns = $form_selected_columns );
			}
			elseif( $form_selected_columns = $this->app->session( )->get( 'columns_tag' ) ) {
				$this->selected_columns = $form_selected_columns;
			}

			if( $selected_row_group_id = $this->app->request()->postVar('row_group_id') ) {
				$this->app->session( )->save( 'row_group_id', $this->selected_row_group = $selected_row_group_id );
			}
			elseif( $selected_row_group_id = $this->app->session( )->get( 'row_group_id' ) ) {
				$this->selected_row_group = $selected_row_group_id;
			}
			
			if( $selected_column_group_id = $this->app->request()->postVar('column_group_id') ) {
				$this->app->session( )->save( 'column_group_id', $this->selected_column_group = $selected_column_group_id );
			}
			elseif( $selected_column_group_id = $this->app->session( )->get( 'column_group_id' ) ) {
				$this->selected_column_group = $selected_column_group_id;
			}
			
		# 		END VARIABLE DEFINITIONS FOR TAG GRID									#
		/*******************************************************************************/
		
	
	}
	
	private function handleMultipleImageActions()
	{
		// delete
		if ($this->app->request()->getVar('delete') == 'true')
		{
			// project to move images to from collection list
			$moveToProjectId = $this->app->request()->postVar('destproject');
			// images whose check boxes were checked
			$selected = $this->app->request()->postVarsMatching('/imageselect_\d+/');
			if (count($selected))
			{
				// get the ids of the selected images
				$selectedIds = array();
				foreach ($selected as $k=>$v)
					$selectedIds[] = preg_replace('/imageselect_/', '', $k);
				$cantDelete = array();
				// which of the selected do they have permission to delete?
				if( $this->fullPerms )
					$canDelete = $selectedIds;
				else
				{
					$perms = $this->permissionSql->checkUserCanDeleteImages($selectedIds, $this->app->user()->getId(), $cantDelete);
					$canDelete = array_keys($perms);
				}
				if( ! count( $canDelete ) )
					return $this->setMessage( "You don't have permission to delete those images.", self::Warn );
				// delete from the filesystem (move to deleted folder) ones we have permission to
//				$filesDeleted = deleteImageFiles($canDelete, $this->app->database(), $notMoved);
				// delete from the database those that were successfully moved
//				if (count($filesDeleted))
				$this->imageSql->deleteImages( $canDelete );
				// give user feedback about what happened
				$notDeletedCount = count($cantDelete);
//				$notMovedCount = count($notMoved);
//				$deletedCount = count($filesDeleted);
				$msg = '';
				$msgType = self::Info;
				if ($notDeletedCount)
				{
					$msg .= itemString($cantDelete) . ( $notDeteledCount == 1 ? " wasn't" : " weren't" ) . " deleted because you don't have necessary permissions. ";
					$msgType = self::Warn;
				}
//				if ($notMovedCount)
//				{
//					$msg .= itemString($cantDelete) . ( $notMovedCount == 1 ? " wasn't" : " weren't" ) . " deleted because you don't have necessary permissions. ";
//					$msgType = self::Warn;
//				}
				$msg = substr( $msg, 0, -2 );
//				if ($deletedCount)
//					$this->changeSql->logChange($this->app->user()->getId(), $msg, 'Images deleted');
				$this->setMessage( $msg, $msgType );
			}
		}
		elseif( $this->app->request( )->postVar( 'moveImages' ) == 'true' )
		{
			$moveMode = strtolower( $this->app->request( )->postVar( 'moveMode' ) );
			// Move multiple images to another project
			// get collection to move the images to (the currently selected one)
			list( $moveToType, $moveToId, $moveToName ) = $this->collectionList->selectionValues( );
			$selectedIds = $this->app->session()->get('image_editIds');
			$projectSql = ProjectSQL::getDialect($this->app->database());
			// move to user's default project if the selected type is user
			if( $moveToType == ImageSQL::UserFilter )
				$collectionId = $this->projectSql->getWorkAreaId( $moveToId );
			elseif( $moveToType == ImageSQL::CollectionFilter )
				$collectionId = $moveToId;
			// check permissions
			if( ! $this->fullPerms )
			{
				if( $moveMode == 'move' )
					$canMove = $this->permissionSql->checkUserCantMoveImages( $selectedIds, $this->app->user( )->getId( ), $collectionId, $cantMove);
				else if( $moveMode == 'copy' )
				{
					$bool_canMove = $this->permissionSql->checkUserCan__( $collectionId, $this->app->user( )->getId( ), PermissionSQL::ADD, 'collection' );
					foreach( $selectedIds as $id )
						$canMove[$id] = $bool_canMove;
				}
			}
			else
				foreach ($selectedIds as $id)
					$canMove[$id] = $id;
			if( count( $canMove ) )
				if( $moveMode == 'move' )
					$this->imageSql->changeImagesCollection( array_keys( $canMove ), $collectionId );
				else
					$this->imageSql->addImagesToCollection( array_keys( $canMove ), $collectionId );
			else
			     $this->setMessage( "You don't have permission to move those images.", self::Warn );
			// report result
			if( count( $canMove ) )
			{
				$msg = file_tracker_scan( $projectSql->getById( $collectionId )->owner( )->id( ), 'checkdirectory' );
				$msg = ( $msg === true ? 'Image thumbnails being regenerated now.' : $msg );
				$this->setMessage( $msg, self::Info );
				$moved = ( $moveMode == 'move' ? 'moved' : 'copied' );
				$plural = count( $canMove ) == 1 ? '' : 's';
				$this->changeSql->logChange( $this->app->user( )->id( ), ucfirst( numToWord( count( $canMove ) ) ) . " image$plural $moved: " . itemString( $canMove ), "Images $moved" );
			}
		}
	}

	public function updateThumbnailSize( )
	{
		// update viewer thumbnail size
		if( $prefval = $this->app->request( )->getVar( 'thumbsize' ) )
			if( $prefval == 'Small' || $prefval == 'Medium' || $prefval == 'Large' )
			{
				$this->app->user( )->updatePreference( 'thumbnail_size', $prefval );
				// clear saved items per page so they are reset for new page width
				$this->app->session()->clear( array( 'images_itemsperpage', 'datafiles_itemsperpage',  'documents_itemsperpage' ) );
			}
	}

	private function shortNameSize( $thumbSize, $edit )
	{

		switch( strtolower( $thumbSize ) )
		{
		case 'small':
			return $edit ? 10 : 11;
		case 'medium':
			return $edit ? 13 : 14;
		case 'large':	default:
			return $edit ? 26 : 28;
		}
	}

	private function makeShortName(&$image, $thumbSize, $edit)
	{
		$image['shortname'] = shortName( $image['imagename'], $this->shortNameSize( $thumbSize, $edit ) );
	}

	public static function widthNameToNum( $name )
	{
		switch( strtolower( $name ) )
		{
			case 5:
			case 'wide':	return 5;
			case 4:
			case 'medium':	return 4;
			case 3:
			case 'narrow':
			default:		return 3;
		}
	}

	// process user input
	public function process( )
	{
	
		// update thumbnail size if link was clicked
		$this->updateThumbnailSize();

		// create sql query objects
		$this->imageSql = ImageSQL::getDialect($this->app->database());
		$this->projectSql = ProjectSQL::getDialect( $this->app->database( ) );
		$this->permissionSql = PermissionSQL::getDialect($this->app->database());

		// restore or create the collection list for display
		$cs = $this->app->session()->get('collectionList');
		if ($cs)
		{
			$this->collectionList = unserialize($cs);
			// pass in the fresh database connection
			if( is_object($this->collectionList))
				$this->collectionList->setDatabaseConnection( $this->app->database( ) );
			else
				$this->collectionList = null;
		}
		if (!$this->collectionList)
			$this->collectionList = new CollectionList($this->app->database(), $this->app->user() );
		if( $this->app->request( )->postVar( 'moveImages' ) == 'true' )
			$this->collectionList->setOpen( true );

		// restore breadcrumbs
		$crumbs = $this->app->session()->get('breadcrumbs');
		if ($crumbs)
			$this->breadcrumbs = unserialize($crumbs);
		if (!$this->breadcrumbs)
			$this->breadcrumbs = new BreadcrumbList();

		// check for collection change request, etc
		$this->updateCollectionList();

		// add breadcrumb
		list( $stype, $sid, $sname ) = $this->collectionList->selectionValues( );
		if( $stype == 'collection' && ( $collection = $this->projectSql->getById( $sid ) ) )
			if( $collection->owner( ) )
				$sname = Collection::properName( $collection->owner( )->fullName( ), $collection->name( ), $collection->owner( )->id( ) == $GLOBALS['biolucidaApp']->user( )->id( ) );
		$this->breadcrumbs->addBreadcrumb( new BreadCrumb( $stype, $sid, $sname ) );

		// correct thumbnail columns/number
		$width = $this->widthNameToNum( $this->app->user( )->preference( 'browsecolumns' )->value( ) );
		$size = $this->app->user( )->preference( 'thumbnail_size' )->value( );
		if( $width == 3 )
			if( $size == "Small" )
				$this->cols = 8;
			elseif( $size == "Medium" )
				$this->cols = 6;
			else // Large
				$this->cols = 4;
		elseif( $width == 4 )
			if( $size == "Small" )
				$this->cols = 9;
			elseif( $size == "Medium" )
				$this->cols = 7;
			else // Large
				$this->cols = 5;
		elseif( $width == 5 )
			if( $size == "Small" )
				$this->cols = 12;
			elseif( $size == "Medium" )
				$this->cols = 9;
			else // Large
				$this->cols = 6;
		$this->ippStart = $this->cols * 4;
		$this->ippInterval = $this->cols;
		$this->pager->setItemsPerPage($this->ippStart);

		// get the image count for the current filter
		$cataloged = $this->collectionList->cataloged() == 'yes';
		list( $filterType, $filterId, $name ) = $this->collectionList->selectionValues( );
		
		
		$this->imageCount = $this->imageSql->getFilteredImageCount( $this->app->user( )->getId( ), $filterType, $filterId, $this->tags, $this->searchString, $cataloged, $this->fullPerms );
		$this->pager->setItemCount($this->imageCount);

		parent::process();
		
		
		if ($filterType == 'grid') {
			$this->handleTagGridSelections();
		}
		else {
			$this->app->session( )->save( 'rows_tag', "" );
		}

		$this->handleMultipleImageActions();
		
		// save state of the collection list across page loads
		$this->breadcrumbs->filter( $this->projectSql );
				
		if( $this->app->request( )->postVar( 'moveImages' ) == 'true' )
			$this->collectionList->setOpen( true );
		$cs = serialize($this->collectionList);
		$crumbs = serialize($this->breadcrumbs);
		$this->app->session( )->save( array( 'collectionList' => $cs, 'breadcrumbs' => $crumbs ) );
	}

	public function output(Smarty $smarty)
	{
		global $Config;
		
		// get image list
		$cataloged = ($this->collectionList->cataloged() == 'yes');
		
		list( $filterType, $filterId, $name ) = $this->collectionList->selectionValues( );
		
		// get info needed to determine short name length
		$edit = $this->app->user( )->getType( ) != 'guest' && !preg_match( '/^embedded/', $this->page->getUrl( ) );
		
		$thumbSize = $this->app->user( )->preference( 'thumbnail_size' )->value();

		// create DownloadRequest object to generate access ids
		$downloadRequest = new DownloadRequest($this->app->session()->id());
		// setup image list
		
		//$this->gridTiles = TagSQL::getDialect( $this->app->database( ) )->getGridThumbnails($this->selected_rows);

	
		$images = $this->imageSql->getByFilter($this->app->user( )->getId( ), $filterType, $filterId, $this->tags, $this->searchString, $this->pager->getItemsPerPage( ),
											   $this->pager->getOffset( ), $cataloged, $this->fullPerms, "", $this->selected_rows, $this->selected_columns  );

		
		foreach( $images as &$image )
		{	
			$downloadRequest->registerThumb( $image, $this->imageSql );
			$image->linkUrl( $Config['Path']['BaseUrl'] . "/image_view?image_id=" . $image->id( ) . "&amp;editmode=single&amp;mode=view&amp;filename=" . htmlentities( $image->fileName( ) ) );
			
			// $image->fileInfo( FS::pathInfo( $image->fileName( ) ) );
		}

		// Odd variables follow:
		// $this->assign( array( 'accesstoken' => '12345', 'application' => 'Neurolucida', 'server' => 'http://biolucida:1080' ) );
		$this->collectionList->outputVars( $smarty );
		$this->breadcrumbs->outputVars( $smarty );
		$smarty->assign( array( 
		'images' => $images, 
		'showIconSize' => (bool) $this->app->user( )->getId( ),
		'thumbsizes' => array( 'small', 'medium', 'large' ), 
		'shortNameLen' => $this->shortNameSize( $thumbSize, $edit ),
		# Below are used for the tag grid
		'gridGenerated' => $this->app->session( )->get( 'grid_options' ),
		'selectedRows' => $this->selected_rows,
		'selectedColumns' => $this->selected_columns,
		'selectedRowGroup' => $this->selected_row_group,
		'selectedColumnGroup' => $this->selected_column_group,
		'allGroups' 	=> TagSQL::getDialect( $this->app->database( ) )->getTagGroups('ignore'),
		'rowTags'   	=> TagSQL::getDialect( $this->app->database( ) )->getUserTags( $this->app->user( )->id( ), 0 ),
		'columnTags'    => TagSQL::getDialect( $this->app->database( ) )->getUserTags( $this->app->user( )->id( ), 0 ),	
		
		) );

		parent::output( $smarty );
     	}
		
		public function smallFileName($file_name) {
		$image['shortname'] = shortName( $image['imagename'], $this->shortNameSize( $thumbSize, $edit ) );
	}
}
?>
