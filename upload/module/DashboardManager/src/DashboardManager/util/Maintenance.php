<?php
/**
 * CDNPAL NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2015 CDNPAL Ltd. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class Maintenance {

	public static function checkRunMaintenance($tagname, $interval_in_minutes) {

		$MaintenanceFactory = \_factory\Maintenance::get_instance();
		$params = array();
		$params["TagName"] = $tagname;
		$MaintenanceRecord = $MaintenanceFactory->get_row($params);

		$update_now = false;
		$action = null;

		if ($MaintenanceRecord != null):

			$last_updated_timestamp = strtotime($MaintenanceRecord->LastUpdated);
			// 60 seconds to a minute
			$minutes_to_check = $interval_in_minutes * 60;

			if (time() - $minutes_to_check > $last_updated_timestamp):
				$update_now = true;

				$maintenance_record = new \model\Maintenance();
				$maintenance_record->TagName = $tagname;
				$maintenance_record->LastUpdated = date("Y-m-d H:i:s");
				$MaintenanceFactory->updateMaintenanceRecord($maintenance_record);

			endif;

		else:

			$update_now = true;
			$maintenance_record = new \model\Maintenance();
			$maintenance_record->TagName = $tagname;
			$maintenance_record->LastUpdated = date("Y-m-d H:i:s");
			$MaintenanceFactory->insertMaintenanceRecord($maintenance_record);

		endif;

		return $update_now;

	}
}
