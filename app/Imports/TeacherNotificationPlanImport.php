<?php

namespace App\Imports;

use App\Models\Backend\TeacherNotificationPlan;
use Maatwebsite\Excel\Concerns\ToModel;

class TeacherNotificationPlanImport implements ToModel
{
  /**
   * @param array $row
   *
   * @return TeacherNotificationPlan
   */
    public function model(array $row)
    {
        return new TeacherNotificationPlan([
          'user_id' => 11,
          'notification_type' => 'a+',
          'plan_date' => $row[0],
          'plan_time' => '16:17:18',
        ]);
    }
}
