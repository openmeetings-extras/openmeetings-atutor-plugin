<?php
/*
* Licensed to the Apache Software Foundation (ASF) under one
* or more contributor license agreements.  See the NOTICE file
* distributed with this work for additional information
* regarding copyright ownership.  The ASF licenses this file
* to you under the Apache License, Version 2.0 (the
* "License") +  you may not use this file except in compliance
* with the License.  You may obtain a copy of the License at
*
*   http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing,
* software distributed under the License is distributed on an
* "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
* KIND, either express or implied.  See the License for the
* specific language governing permissions and limitations
* under the License.
*/

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require ('lib/openmeetings.class.php');
require ('lib/openmeetings.inc.php');

//Validate 
$_POST['room_id'] = intval($_REQUEST['room_id']);


if (isset($_GET['group_id'])){
	$group_id = intval($_GET['group_id']);
	
	//TODO
	//Handles instrcutor as an exception, cuz instructor can go in and create room as well
	if (authenticate(AT_PRIV_OPENMEETINGS, true)){
		$sql = 'SELECT g.title FROM '.TABLE_PREFIX."groups g WHERE g.group_id=$group_id";
	} else {
		
		$sql = 'SELECT g.title FROM '.TABLE_PREFIX."groups_members gm INNER JOIN ".TABLE_PREFIX."groups g WHERE gm.group_id=$group_id AND gm.member_id=$_SESSION[member_id]";
	}
	
}

//Initiate Openmeeting
$om_obj = new Openmeetings($_SESSION['course_id'], $_SESSION['member_id'], $group_id);

//Login
$om_obj->om_login();

//Get the room id
//TODO: Course title added/removed after creation.  Affects the algo here.
if ($_row['title']!=''){
	$room_name = $_row['title'];
} else {
	$room_name = 'group_'.$group_id;
}

//Form action
//Handle form action
if (isset($_POST['create_room']) || (isset($_POST['update_room']) && isset($_POST['room_id']))) {
	
	//mysql escape
	$_POST['openmeetings_roomtype']				= intval($_POST['openmeetings_roomtype']);
	$_POST['openmeetings_num_of_participants']	= intval($_POST['openmeetings_num_of_participants']);
	(intval($_POST['openmeetings_ispublic']) == 1)?$_POST['openmeetings_ispublic']='true':$_POST['openmeetings_ispublic']='false';
	$_POST['openmeetings_vid_w']				= intval($_POST['openmeetings_vid_w']);
	$_POST['openmeetings_vid_h']				= intval($_POST['openmeetings_vid_h']);
	(intval($_POST['openmeetings_show_wb']) == 1)?$_POST['openmeetings_show_wb']='true':$_POST['openmeetings_show_wb']='false';
	$_POST['openmeetings_wb_w']					= intval($_POST['openmeetings_wb_w']);
	$_POST['openmeetings_wb_h']					= intval($_POST['openmeetings_wb_h']);
	(intval($_POST['openmeetings_show_fp']) == 1)?$_POST['openmeetings_show_fp']='true':$_POST['openmeetings_show_fp']='false';
	$_POST['openmeetings_fp_w']					= intval($_POST['openmeetings_fp_w']);
	$_POST['openmeetings_fp_h']					= intval($_POST['openmeetings_fp_h']);

	//create a new room
	if (isset($_POST['create_room'])){
		//Get the room id
		//TODO: Course title added/removed after creation.  Affects the algo here.		
		if (isset($_SESSION['course_title']) && $_SESSION['course_title']!=''){
			$room_name = $_SESSION['course_title'];
		} else {
			$room_name = 'course_'.$course_id;
		}
		
		//add the room with the given parameters.
		$om_obj->om_addRoom($room_name, $_POST);

		$msg->addFeedback('OPENMEETINGS_ADDED_SUCEEDED');
		header('Location: index.php');
		exit;
	} elseif (isset($_POST['update_room'])){
		//update a room
		$om_obj->om_updateRoom(intval($_POST['room_id']), $_POST);
		$msg->addFeedback('OPENMEETINGS_UPDATE_SUCEEDED');
		header('Location: index.php');
		exit;
	}
} elseif (isset($_POST['cancel'])){
	$msg->addFeedback('OPENMEETINGS_CANCELLED');
	header('Location: index.php');
	exit;
} elseif (isset($_REQUEST['edit_room']) && isset($_POST['room_id'])){
	//Log into the room
	$room_id = $_POST['room_id'];

	//Get the room obj
	$room_obj = $om_obj->om_getRoomById($room_id);

	//Assign existing variables to the room
	$_POST['openmeetings_roomtype']				= intval($room_obj['roomtypes_id']);
	$_POST['openmeetings_room_name']			= $addslashes($room_obj['name']);
	$_POST['openmeetings_num_of_participants']	= $addslashes($room_obj['numberOfPartizipants']);
	(($room_obj['ispublic'])=='true')?$_POST['openmeetings_ispublic']=1:$_POST['openmeetings_ispublic']=0;
	$_POST['openmeetings_vid_w']				= intval($room_obj['videoPodWidth']);
	$_POST['openmeetings_vid_h']				= intval($room_obj['videoPodHeight']);
	(($room_obj['return']['showWhiteBoard'])=='true')?$_POST['openmeetings_show_wb']=1:$_POST['openmeetings_show_wb']=0;
	$_POST['openmeetings_wb_w']					= intval($room_obj['whiteBoardPanelWidth']);
	$_POST['openmeetings_wb_h']					= intval($room_obj['whiteBoardPanelHeight']);
	(($room_obj['return']['showFilesPanel'])=='true')?$_POST['openmeetings_show_fp']=1:$_POST['openmeetings_show_fp']=0;
	$_POST['openmeetings_fp_w']					= intval($room_obj['filesPanelWidth']);
	$_POST['openmeetings_fp_h']					= intval($room_obj['filesPanelHeight']);
	include (AT_INCLUDE_PATH.'header.inc.php');
	include ('html/update_room.inc.php');
	include (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
} elseif (isset($_GET['action']) && $_GET['action'] == 'view'){
	$room_id = intval($_GET['room_id']);
	$sid	 = $addslashes($_GET['sid']);
	header('Location: view_meetings.php?room_id='.$room_id.SEP.'sid='.$sid);
	exit;
}

$room_id = $om_obj->om_getRoom();

require (AT_INCLUDE_PATH.'header.inc.php');
if ($room_id == false) {
	include ('html/create_room.inc.php');
} else {
	//include page
	include ('html/edit_room.inc.php');
}
require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>
