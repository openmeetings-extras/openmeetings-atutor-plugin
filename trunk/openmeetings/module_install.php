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

/*******
 * the line below safe-guards this file from being accessed directly from
 * a web browser. It will only execute if required from within an ATutor script,
 * in our case the Module::install() method.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }
/*******
 * Note: the many options for these variables are used to decrease confusion.
 *       TRUE | FALSE | 1 will be the convention.
 *
 * $_course_privilege
 *     specifies the type of instructor privilege this module uses.
 *     set to empty | FALSE | 0   to disable any privileges.
 *     set to 1 | AT_PRIV_ADMIN   to use the instructor only privilege.
 *     set to TRUE | 'new'        to create a privilege specifically for this module:
 *                                will make this module available as a student privilege.
 *
 * $_admin_privilege
 *    specifies the type of ATutor administrator privilege this module uses.
 *    set to FALSE | AT_ADMIN_PRIV_ADMIN   to use the super administrator only privilege.
 *    set to TRUE | 'new'                  to create a privilege specifically for this module:
 *                                         will make this module available as an administrator privilege.
 *
 *
 * $_cron_interval
 *    if non-zero specifies in minutes how often the module's cron job should be run.
 *    set to 0 or not set to disable.
 */
$_course_privilege = TRUE; // possible values: FALSE | AT_PRIV_ADMIN | TRUE
$_admin_privilege  = TRUE; // possible values: FALSE | TRUE


if (!$msg->containsErrors() && file_exists(dirname(__FILE__) . '/module.sql')) {
	// deal with the SQL file:
	require(AT_INCLUDE_PATH . 'classes/sqlutility.class.php');
	$sqlUtility =& new SqlUtility();

	/*
	 * the SQL file could be stored anywhere, and named anything, "module.sql" is simply
	 * a convention we're using.
	 */
	$sqlUtility->queryFromFile(dirname(__FILE__) . '/module.sql', TABLE_PREFIX);
}

?>
