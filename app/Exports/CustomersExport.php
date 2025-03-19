<?php

namespace App\Exports;

use App\Models\BundleEquiptment;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\SnPorts;
use App\Models\User;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as StyleNumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CustomersExport implements FromQuery, WithColumnFormatting, WithMapping, WithHeadings, WithStrictNullComparison
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function query()
    {
        $request = $this->request;

        $packages = Package::get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();



        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

        $all_township = Township::select('id')
            ->get()
            ->toArray();
        $all_packages = Package::select('id')
            ->get()
            ->toArray();
            $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();
        $mycustomer =   Customer::with('package','township','isp','sn','dn','pop','pop_device','status','subcon')
                        ->where(function ($query) {
                            return $query->where('customers.deleted', '=', 0)
                                ->orWhereNull('customers.deleted');
                        })
                        ->when($user->user_type, function ($query, $user_type) use ($user) {
                            if($user_type == 'partner') {
                                $query->where('customers.partner_id', '=', $user->partner_id);
                            }
                            if($user_type == 'isp') {
                                $query->where('customers.isp_id', '=', $user->isp_id);
                            }
                            if($user_type == 'subcon') {
                                $query->where('customers.subcom_id', '=', $user->subcom_id);
                            }
                    
                        })
                        ->when($request->keyword, function ($query, $search = null) {
                            $query->where(function ($query) use ($search) {
                                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
                            });
                        })->when($request->general, function ($query, $general) {
                            $query->where(function ($query) use ($general) {
                                $query->where('customers.name', 'LIKE', '%' . $general . '%')
                                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                                    ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%');
                            });
                        })
                        ->when($request->installation, function ($query, $installation) {
                            $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                            $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                            $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
                        })
                        ->when($request->order, function ($query, $order) {
                            $startDate = Carbon::parse($order[0])->format('Y-m-d');
                            $endDate = Carbon::parse($order[1])->format('Y-m-d');
                            $query->whereBetween('customers.order_date',  [$startDate, $endDate]);
                        })
                        ->when($request->prefer, function ($query, $prefer) {
                            $startDate = Carbon::parse($prefer[0])->format('Y-m-d');
                            $endDate = Carbon::parse($prefer[1])->format('Y-m-d');
                            $query->whereBetween('customers.prefer_install_date', [$startDate, $endDate]);
                        })
                        ->when($request->dn, function ($query, $dn_2) {
                            $query->where('customers.dn_id', '=', $dn_2['id']);
                        })
                        ->when($request->sn, function ($query, $sn) {
                            $query->where('customers.sn_id', '=', $sn['id']);
                        })
                        ->when($request->pop, function ($query, $pop) {
                            $query->where('customers.pop_id', '=', $pop['id']);
                        })
                        ->when($request->pop_device, function ($query, $pop_device) {
                            $query->where('customers.pop_device_id', '=', $pop_device['id']);
                        })
                        ->when($request->package, function ($query, $package) use ($all_packages) {
                            if ($package == 'empty') {
                                $query->whereNotIn('customers.package_id', $all_packages);
                            } else {
                                $query->where('customers.package_id', '=', $package);
                            }
                        })
                        ->when($request->package_speed, function ($query, $package_speed) {
                            $speed_type =  explode("|", $package_speed);
                            $speed = $speed_type[0];
                            $type = $speed_type[1];
                            $query->whereHas('package', function($q) use ($speed, $type) {
                                $q->where('speed', '=', $speed);
                                $q->where('type', '=', $type);
                            });
                        })
                        ->when($request->township, function ($query, $township) use ($all_township) {
                            if ($township == 'empty') {
                                $query->whereNotIn('customers.township_id', $all_township);
                            } else {
                                $query->where('customers.township_id', '=', $township);
                            }
                        })
                        ->when($request->status, function ($query, $status) {
                            $query->where('customers.status_id', '=', $status);
                        })
                        ->when($request->sh_isp, function ($query, $sh_isp) {
                            $query->where('customers.isp_id', '=', $sh_isp['id']);
                        })
                        ->when($request->partner, function ($query, $partner) {
                            $query->where('customers.partner_id', '=', $partner['id']);
                        })
                        ->when($request->status_type, function ($query, $status_type) {
                            $query->whereHas('status', function($q) use ($status_type) {
                                $q->where('type', '=', $status_type);
                            });
                        })
                        ->when($request->order, function ($query, $order) {
                            $query->whereBetween('customers.order_date', $order);
                        })
                        ->when($request->installation, function ($query, $installation) {
                            $query->whereBetween('customers.installation_date', $installation);
                        })

                        // ->when($request->sh_vlan, function ($query, $vlan) {
                        //     $query->where('customers.vlan', $vlan);
                        // })

                        ->when($request->sh_onu_serial, function ($query, $sh_onu_serial) {
                            $query->where('customers.onu_serial', $sh_onu_serial);
                        })
                        ->when($request->sh_installation_team, function ($query, $sh_installation_team) {
                            $query->where('customers.subcom_id', $sh_installation_team['id']);
                        })
                        
                        ->when($request->assign_date, function ($query, $assign_date) {
                            $startDate = Carbon::parse($assign_date[0])->format('Y-m-d');
                            $endDate = Carbon::parse($assign_date[1])->format('Y-m-d');
                            $query->whereBetween('customers.subcom_assign_date', [$startDate, $endDate]);

                        })
                        ->when($request->installation_status, function ($query, $sh_installation_status) {

                            $query->where('customers.installation_status', $sh_installation_status);
                        })
                        ->when($request->sh_installation_timeline, function ($query, $sh_installation_timeline) {
                        
                            $query->where('customers.installation_timeline', $sh_installation_timeline);
                        });
        return $mycustomer;
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
 
            'Phone No.',
           
            'Address',
            'Lat Long',
            'Township',
            'Package',
          

            'Installation Team',
           
            'Order Date',
            'Prefer Install Date',
            'Installation Date',
            'Installation Remark',
            'Service Activation Date',
            'Fiber Distance',
            'ONU Serial',
            'ONU Power',
            'POP Site',
            'GPON OLT',
        
            'ONT ID',
            'DN',
            'SN',
            'SN Port',
            'Devices',
            'Status',
            'Partner',
            'ISP'
        ];
    }
    public function columnFormats(): array
    {
        return [
            'I' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => StyleNumberFormat::FORMAT_TEXT,
        ];
    }
    public function map($mycustomer): array
    {
      
     
        $bundle = '';
        if (!empty($mycustomer->bundle)) {
            // Split bundle IDs into an array, handling both single and comma-separated values
            $bundle_ids = array_map('trim', explode(',', $mycustomer->bundle));

            // Initialize an array to store bundle names
            $bundle_names = [];

            // Loop through each bundle ID, fetch the name, and add it to the names array if found
            foreach ($bundle_ids as $bundle_id) {
                $bundle_device = BundleEquiptment::find($bundle_id);
                if ($bundle_device) {
                    $bundle_names[] = $bundle_device->name;
                }
            }

            // Join all found bundle names with a comma separator
            $bundle = implode(', ', $bundle_names);
        }
        return [
            $mycustomer->ftth_id,
            $mycustomer->name,
            ($mycustomer->phone_1) ? $mycustomer->phone_1 : null,
            $mycustomer->address,
            $mycustomer->location,
            $mycustomer->township?->name,
            $mycustomer->package?->name,
            $mycustomer->subcon?->name,
            ($mycustomer->order_date) ? Date::stringToExcel($mycustomer->order_date) : null,
            ($mycustomer->prefer_install_date) ? Date::stringToExcel($mycustomer->prefer_install_date) : null,
            ($mycustomer->installation_date) ? Date::stringToExcel($mycustomer->installation_date) : null,
            $mycustomer->installation_remark,
            ($mycustomer->service_activation_date) ? Date::stringToExcel($mycustomer->service_activation_date) : null,
            $mycustomer->fiber_distance,
            $mycustomer->onu_serial,
            $mycustomer->onu_power,
            $mycustomer->pop?->site_name,
            $mycustomer->pop_device?->device_name,
        
            $mycustomer->gpon_ontid,
        
            $mycustomer->dn?->name,
            $mycustomer->sn?->name,
            $mycustomer->splitter_no,
            $bundle,
            $mycustomer->status?->name,
            $mycustomer->partner?->name,
            $mycustomer->isp?->name,
        ];
    }

    public function getCustomerType($type)
    {
        switch ($type) {
            case 1:
                return "Normal Customer";
                break;
            case 2:
                return "VIP Customer";
                break;
            case 3:
                return "Partner Customer";
                break;
            case 4:
                return "Office Staff";
                break;
            default:
                return "Normal Customer";
                break;
        }
    }
}
