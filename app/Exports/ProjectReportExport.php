<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ProjectReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Mengambil proyek milik user aktif beserta jumlah tugasnya
        return Project::where('user_id', Auth::id())->withCount('tasks')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Proyek',
            'Status',
            'Total Tugas',
            'Tanggal Dibuat',
        ];
    }

    public function map($project): array
    {
        return [
            $project->id,
            $project->title,
            ucfirst($project->status),
            $project->tasks_count . ' Tugas',
            $project->created_at->format('d/m/Y'),
        ];
    }
}