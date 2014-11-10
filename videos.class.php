<?php

/*
 * Modified Youtube Class from:
 * http://jdmweb.com/youtube-video-manager-with-php-mysql-jquery
*/

class MyVideo {

  private $id;		//Id of the Video
  private $url;		//YouTube Url
  private $title;	//Video Title
	  /*************/
	 /* Construct */
	/*************/
  public function __construct( $id=0, $url = '', $title = '' )
  {
    $this->id    = $id;
    $this->url   = $url;
    $this->title = $title;
  }

	  /**********************/
	 /* Load a Video by ID */
	/**********************/
	public function load( $id )
	{
		global $oMySQL;
		//Select the Video Information from DB
		$aWhere['video_id'] = $id;
		$oMySQL->Select( VIDEOS_TBL , $aWhere, '*', '', 1 );
		$data = $oMySQL->aArrayedResult;
		//Assign those Infomation to a MyVideo Object
		$this->id =    $data['video_id'];
		$this->url =   $data['video_url'];
		$this->title = $data['video_title'];

		return $data; 
	}

	  /**************************/
	 /* Get Video List From DB */
	/**************************/
	public function getrecords( $limit = '' )
	{
		global $oMySQL;
	   
		$sql = empty( $limit ) ? $oMySQL->Select( VIDEOS_TBL ) : $oMySQL->Select( VIDEOS_TBL, '', '*', '',  $limit );
		//Select all videos from DB
		if( $sql )
		{
			return ( $oMySQL->iAffected == 1 ) ? $oMySQL->aArrayedResult : $oMySQL->aArrayedResults;
		} else {
			return false;
		}
	}  
  
	  /****************************************/
	 /* Display the form to add/edit a video */
	/****************************************/
	public function getVideoForm( $videoid = 0, $action = 'session.php', $method = 'post' )
	{
		//Load A Video if $videoid ( Used to edit an existing video )
		if( ( !empty( $videoid ) ) && ( is_numeric( $videoid ) ) )
		{ 
		  $video = MyVideo::load( $videoid ); 
		} else { 
		  $video = new MyVideo(); 
		}
    
		//The Form Markup (Video Url, + Video Title)
		return '
		<form action="'.$action.'" method="'.$method.'">
		<p><label for="video_url">YouTube url: </label> <input type="text" name="video_url" class="text" value="'.$video->url.'" /></p>
		<p><label for="video_title">Title:</label>      <input type="text" name="video_title" class="text" value="'.$video->title.'" /></p>
		<p><label> &nbsp; </label> <input type="submit" class="apply" value="Send" /><input id="cancelctcform" type="button" class="negative" value="Cancel" /></p>
			
		<input type="hidden" name="video_id" value="'.$video->id.'" />
		<input type="hidden" name="todo" value="savevideo" />
		</form>';
	} 

	  /**************************************/
	 /* Insert / Update a Row in the Table */
	/**************************************/
	public function save()
	{
	  global $oMySQL;
      //Get Values From Post
      $id =    htmlentities( $_POST["video_id"], ENT_QUOTES );
      $url =   htmlentities( $_POST["video_url"], ENT_QUOTES );
      $title = htmlentities( $_POST["video_title"], ENT_QUOTES );
    
		if( !empty( $url ) )
		{
		  if ( !$id ) //Creation
		  {
			$aVars = array( 'video_url' => $url, 'video_title' => $title );		
			if ( $oMySQL->Insert( $aVars, VIDEOS_TBL ) )
			{
			  Func::returnMsg( 'Your video Added to the database' ); return true;
			} else {
			  Func::returnMsg( 'There was a problem adding the video: error message: '.$oMySQL->sLastError, 0 ); return false;
			}
		  } else {	//Update
			$aSet = array();
			$aSet['video_url']   = ( !empty( $url ) )   ? $url   : '';
			$aSet['video_title'] = ( !empty( $title ) ) ? $title : '';
			$aWhere['video_id']  = $id;
		
			if( $oMySQL->Update( VIDEOS_TBL, $aSet, $aWhere ) )
			{
			  Func::returnMsg('Your video was updated.'); return true;
			} else {
			  Func::returnMsg( 'There was a problem updating the video: error message: '.$oMySQL->sLastError, 0 ); return false;
			}
		  }
		}
	}
  
	  /******************/
	 /* Delete a Video */
	/******************/
	public function delete()
	{
	  global $oMySQL;
	  $recordid = htmlentities( $_POST['recordid'], ENT_QUOTES );
	  if( !empty( $recordid ) )
	  {
		$aWhere['video_id'] = $recordid;
		if( $oMySQL->Delete( VIDEOS_TBL, $aWhere ) )
		{
		  Func::returnMsg( 'Video was deleted', 1, 0, 'MyVideo_'.$recordid ); return true;
		} else {
		  Func::returnMsg( 'There was a problem deleting the video: error message'.$oMySQL->sLastError, 0 ); return false;
		}
	  } else {
		Func::returnMsg( 'The video id is empty', 0 ); return false;
	  }
	}

	  /********************/
	 /* Create the Table */
	/********************/
	public function createVideoTable()
	{
	  $sql='CREATE TABLE IF NOT EXISTS `'.VIDEOS_TBL.'` (
	  `video_id` int(11) NOT NULL auto_increment,
	  `video_url` varchar(255) default NULL,
	  `video_title` varchar(255) default NULL,
	  PRIMARY KEY  (`video_id`)
	  )';
	  if(mysql_query($sql)) { return true; } else { die("SQL Error!<br>".$sql."<br>".mysql_error()); return false; }
	}

	  /********************************************/
	 /* Generates the markup for the public page */
	/********************************************/
	public function getPublicSide( $limit = '')
	{
	  $thumbdom = ''; 
	  $playerdom = '';
    
	  //Get The videos from DB
	  $videolist = MyVideo::getrecords( $limit );
	  if( count( $videolist ) == count( $videolist, COUNT_RECURSIVE ) )
	  {
	    $list[] = $videolist;
	  } else {
	    $list = $videolist;
	  }
	  //Loop Through Them
	  if( $videolist )
	  {
	    foreach( $list as $video )
	    {
	  	  //Get YouTube Id
          $youtubeid = str_replace( "watch?v=", "", end(explode("/", $video['video_url'])));
          $youtubeid = reset( explode( "&",$youtubeid ) );
      
          //Youtube video File + Youtube video Thumbnail
          $thumb = "http://img.youtube.com/vi/$youtubeid/default.jpg";
          $file = "http://www.youtube.com/embed/$youtubeid";
          $shortTitle  = substr($video['video_title'], 0, 13);  // abcd
          $shortTitle .= '...';  // abcd
          
          //Build Up your list of video
          $thumbdom.='
          <span class="GalBox">
			<a href="#video_'.$video['video_id'].'" title="'.$video['video_title'].'" rel="ibox&width=640">
			<img src="'.$thumb.'" alt="'.$video['video_title'].'" />'.$shortTitle.'</a>
			<br>
			<span class="GalText">
			  <div class="fb-like" data-href="'.$file.'" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
			 </span>
          </span>
	  
          <div style="display: none" id="video_'.$video['video_id'].'">
			<iframe width="640" height="480" src="'.$file.'?rel=0" frameborder="0" allowfullscreen></iframe>
          </div>
          ';
        }
	  }
    echo $thumbdom;
	}
  
	  /***************************************/
	 /* Lists the videos for administration */
	/***************************************/
	public function getAdminList()
	{
	  $admindom = '';
      //Get the videos From DB
      $videolist = MyVideo::getrecords();
		if(count($videolist) == count($videolist, COUNT_RECURSIVE))
		{
	     $list[] = $videolist;
		} else {
	     $list = $videolist;
		}
	  if( $videolist )
	  {
	    foreach( $list as  $record )
	    {
		  //Build the Admin List of videos
		  $admindom .= '
		  <div id="MyVideo_'.$record['video_id'].'">
		  <span>
		  <a href="'.$record['video_url'].'" target="_blank" title="'.$record['video_url'].'">'.$record['video_title'].'</a> | 
		  </span>
		  <a class="editvideolink" href="#"><img src="layout/icons/write.png" class="icon">  Edit</a>
		  <form action="session.php" method="post" style="display: inline">
		  <input type="hidden" name="todo" value="delvideo" />
		  <input type="hidden" name="recordid" value="'.$record['video_id'].'" />
		  <input type="submit" value="Delete" class="negative"  onclick="return deletechecked(\'Are you sure you want to delete: '.$record['video_title'].' \');" />
		  </form>
		  <hr>
		  </div>
		  ';
		}
	  }
    return $admindom;
  }

	  /********************************************/
	 /* Generates The markup for the Admin Page  */
	/********************************************/
	public function getAdminSide()
	{	
		//Table Creation (Uncomment to create the SQL Table)
		//MyVideo::createVideoTable();
		//Get the Admin Form and the video List
		echo'
		<div id="addrecordwrap">
			<a href="#">Add a new Video</a>
		</div>
		<div id="recordform">';
		echo MyVideo::getVideoForm(0, 'session.php', 'post');	//Admin Form
		echo'
		</div>
		<hr>
		<div id="recordlist">
		<h3>Your Videos:</h3>';
		//Video List
		echo MyVideo::getAdminList();	
		echo'</div>';
	}

	  /*****************************************/
	 /* Loads a video and return a json array */
	/*****************************************/
	public function loadVideoJson()
	{
        $recordid = htmlentities($_POST['recordid'],ENT_QUOTES);
        if( !empty( $recordid ) )
		{
          $record = MyVideo::load( $recordid );
          if( !empty( $record ) )
		  {
			echo json_encode($record);
          }
        }				
    } 
}

?>