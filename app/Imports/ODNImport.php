<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\DnPorts;
use App\Models\FiberCable;
use App\Models\JcBox;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ODNImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
      
        /** For Distributed Route **/
            // $fiber = FiberCable::firstOrNew(['name' => $row['distribute_route']]);
            // $fiber->core_quantity = 12;
            // $fiber->type = "distributed_route"; 
            // $fiber->cable_layout = "OH";
            // $fiber->status = 'Active';
            // $fiber->save();
       /** For JC  **/
            $jc = JcBox::firstOrNew(['name' => trim($row['jc_box'])]);
            $jc->location = "";
            $jc->status = 'Active';
            $jc->save();
          
    }
}