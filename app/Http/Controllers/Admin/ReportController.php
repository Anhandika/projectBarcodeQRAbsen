<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
class ReportController extends Controller{
    public function index(Request $r){
        $date=$r->input('date', now()->toDateString());
        $attendances=Attendance::with('user')->whereDate('attendance_date',$date)->orderBy('scanned_at','desc')->paginate(20)->withQueryString();
        return view('admin.reports.index', compact('attendances','date'));
    }
    public function export(Request $r){
        $date=$r->input('date', now()->toDateString());
        $format=$r->input('format', 'csv');
        $rows=Attendance::with('user')->whereDate('attendance_date',$date)->get();

        if ($format === 'pdf') {
            return view('admin.reports.print', compact('rows', 'date'));
        }

        if ($format === 'excel') {
            $output = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
            $output .= "<head><meta charset=\"utf-8\"></head><body>";
            $output .= "<h2>LAPORAN KEHADIRAN DIGITAL SMK BINA UTAMA KENDAL</h2>";
            $output .= "<h4>Tanggal: $date</h4>";
            $output .= "<table border=\"1\">";
            $output .= "<tr style=\"background-color: #2c68f5; color: #ffffff; font-weight: bold;\">";
            $output .= "<th>Nama Lengkap</th><th>NISN / Nomor Induk</th><th>Grup / Kelas</th><th>Waktu Scan</th><th>Hasil</th>";
            $output .= "</tr>";
            foreach($rows as $a) {
                $time = $a->scanned_at ? $a->scanned_at->format('H:i:s') : '--:--';
                $output .= "<tr><td>{$a->user?->name}</td><td>'{$a->user?->identifier}</td><td>{$a->user?->class_name}</td><td>{$time}</td><td>{$a->result->value}</td></tr>";
            }
            $output .= "</table></body></html>";
            return response($output, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=laporan-$date.xls",
                'Cache-Control' => 'max-age=0'
            ]);
        }

        $csv="Nama,NISN,Kelas,Waktu,Hasil\n";
        foreach($rows as $a) {
            $time = $a->scanned_at ? $a->scanned_at->format('H:i:s') : '';
            $csv.="\"{$a->user?->name}\",{$a->user?->identifier},{$a->user?->class_name},{$time},{$a->result->value}\n";
        }
        return response($csv,200,['Content-Type'=>'text/csv','Content-Disposition'=>"attachment; filename=laporan-$date.csv"]);
    }
}
