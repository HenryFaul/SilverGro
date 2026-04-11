<?php

namespace App\Http\Controllers;

use App\Models\AssignedUserComm;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show',
        ]);

        $paginate = $request['show'] ?? 10;
        $staff = User::with('roles')->with('Staff')->paginate($paginate)->withQueryString();

        return inertia(
            'Staff/Index',
            [
                'filters' => $filters,
                'staff' => $staff,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string'],
            'last_legal_name' => ['required', 'string', 'string'],
            'nickname' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'id_reg_no' => ['nullable', 'string', 'unique:staff,id_reg_no'],
            'job_description' => ['nullable', 'string'],
            'password_confirmation' => ['required'],
            'password' => ['required', 'string', new Password, 'required_with:password_confirmation', 'same:password_confirmation'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $user = User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user->exists()) {
            Staff::create([
                'first_name' => $request->first_name,
                'last_legal_name' => $request->last_legal_name,
                'nickname' => $request->nickname,
                'title' => $request->title,
                'id_reg_no' => $request->id_reg_no,
                'job_description' => $request->job_description,
                'user_id' => $user->id,
            ]);

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff created');
        }

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        $staff = User::where('id', '=', $staff->user_id)->with('roles')->with('Staff')->first();

        $all_roles_in_database = Role::all();
        $permissions = $staff?->getPermissionsViaRoles()->unique('name')->pluck('name');

        return inertia(
            'Staff/Show',
            [
                'staff' => $staff,
                'all_roles' => $all_roles_in_database,
                'permissions' => $permissions,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {

        //Update the system user first name field to align with staff
        $user = $staff->User()->first();

        if ($request->email != $user->email) {
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        }

        if ($request->password != null || $request->password != '') {
            $request->validate([
                'password' => ['required', 'string', new Password],
            ]);
        }

        $staff->update(
            $request->validate([
                'first_name' => ['nullable', 'string'],
                'last_legal_name' => ['nullable', 'string'],
                'nickname' => ['nullable', 'string'],
                'title' => ['nullable', 'string'],
                'id_reg_no' => ['nullable', 'string'],
                'is_active' => ['nullable', 'boolean'],
                'job_description' => ['nullable', 'string'],
            ])
        );

        if ($user->exists()) {
            if ($request->password != null && $request->password != '') {
                $user->update([
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            } else {
                $user->update([
                    'name' => $request->first_name,
                    'email' => $request->email,
                ]);
            }

        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Staff updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        //

    }

    public function staffComm(Request $request)
    {
        $filters = $request->only(['staff_id', 'date_from', 'date_to']);

        $commissions = $this->fetchCommissions($filters);

        $staff_list = Staff::where('is_active', 1)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_legal_name']);

        return inertia('Staff/StaffComm', [
            'commissions' => $commissions,
            'staff_list'  => $staff_list,
            'filters'     => $filters,
        ]);
    }

    public function staffCommExport(Request $request)
    {
        $filters = $request->only(['staff_id', 'date_from', 'date_to']);
        $commissions = $this->fetchCommissions($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Staff Commissions');

        $titleStyle = ['font' => ['bold' => true, 'size' => 14]];
        $headerStyle = [
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];

        $sheet->setCellValue('A1', 'Staff Commissions Report (Approved trades only)');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        $dateLabel = '';
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $dateLabel = $filters['date_from'] . ' to ' . $filters['date_to'];
        } elseif (!empty($filters['date_from'])) {
            $dateLabel = 'From ' . $filters['date_from'];
        } elseif (!empty($filters['date_to'])) {
            $dateLabel = 'To ' . $filters['date_to'];
        }
        if ($dateLabel) {
            $sheet->setCellValue('A2', 'Period: ' . $dateLabel);
            $sheet->mergeCells('A2:D2');
        }

        $sheet->setCellValue('A4', 'Staff Name');
        $sheet->setCellValue('B4', 'Supplier Commission (ZAR)');
        $sheet->setCellValue('C4', 'Customer Commission (ZAR)');
        $sheet->setCellValue('D4', 'Total Commission (ZAR)');
        $sheet->getStyle('A4:D4')->applyFromArray($headerStyle);

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(28);

        $currencyFormat = '#,##0.00';
        $row = 5;
        foreach ($commissions as $comm) {
            $sheet->setCellValue('A' . $row, $comm['name']);
            $sheet->setCellValue('B' . $row, $comm['supplier_comm']);
            $sheet->setCellValue('C' . $row, $comm['customer_comm']);
            $sheet->setCellValue('D' . $row, $comm['supplier_comm'] + $comm['customer_comm']);
            $sheet->getStyle('B' . $row . ':D' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
            $row++;
        }

        // Totals row
        $sheet->setCellValue('A' . $row, 'TOTAL');
        $sheet->setCellValue('B' . $row, array_sum(array_column($commissions, 'supplier_comm')));
        $sheet->setCellValue('C' . $row, array_sum(array_column($commissions, 'customer_comm')));
        $totalAll = array_sum(array_column($commissions, 'supplier_comm')) + array_sum(array_column($commissions, 'customer_comm'));
        $sheet->setCellValue('D' . $row, $totalAll);
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('B' . $row . ':D' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        $filename = 'staff_commissions_' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function fetchCommissions(array $filters): array
    {
        $query = AssignedUserComm::with(['AssignedUserSupplier', 'AssignedUserCustomer'])
            ->whereHas('TransportTransaction', fn($q) => $q->whereNotNull('a_mq'))
            ->where(fn($q) => $q->where('supplier_comm', '>', 0)->orWhere('customer_comm', '>', 0));

        if (!empty($filters['staff_id'])) {
            $sid = $filters['staff_id'];
            $query->where(fn($q) => $q
                ->where('assigned_user_supplier_id', $sid)
                ->orWhere('assigned_user_customer_id', $sid)
            );
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $records = $query->get();

        $staffTotals = [];
        foreach ($records as $comm) {
            if ($comm->assigned_user_supplier_id && $comm->supplier_comm > 0) {
                $id   = $comm->assigned_user_supplier_id;
                $name = $comm->AssignedUserSupplier
                    ? ($comm->AssignedUserSupplier->first_name . ' ' . $comm->AssignedUserSupplier->last_legal_name)
                    : 'Unknown';
                if (!isset($staffTotals[$id])) {
                    $staffTotals[$id] = ['name' => $name, 'supplier_comm' => 0, 'customer_comm' => 0];
                }
                $staffTotals[$id]['supplier_comm'] += $comm->supplier_comm;
            }

            if ($comm->assigned_user_customer_id && $comm->customer_comm > 0) {
                $id   = $comm->assigned_user_customer_id;
                $name = $comm->AssignedUserCustomer
                    ? ($comm->AssignedUserCustomer->first_name . ' ' . $comm->AssignedUserCustomer->last_legal_name)
                    : 'Unknown';
                if (!isset($staffTotals[$id])) {
                    $staffTotals[$id] = ['name' => $name, 'supplier_comm' => 0, 'customer_comm' => 0];
                }
                $staffTotals[$id]['customer_comm'] += $comm->customer_comm;
            }
        }

        usort($staffTotals, fn($a, $b) => strcmp($a['name'], $b['name']));

        return array_values($staffTotals);
    }
}
