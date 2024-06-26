<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\RespondentScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class RespondentTable extends DataTableComponent
{
    protected $model = User::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setSortingEnabled();
        $this->setFiltersEnabled();
    }

    public function builder(): Builder
    {
        $respondents_id = RespondentScore::where('jury_id',Auth::user()->id)->pluck('respondent_id')->toArray();
        return User::query()
            ->with([
                'work_unit',
                'score.jury',
                'answers.children'
            ])
            ->whereIn('users.id', $respondents_id);
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Tipe Unit Kerja')
                ->options([
                    ""             => "Semua",
                    "PELAKSANA"    => "Pelaksana",
                    "DARAT"        => "Darat",
                    "LAUT"         => "Laut",
                    "UDARA"        => "Udara",
                    "KERETA"       => "Kereta",
                    "BPSDMP(UP)"   => "BPSDMP(UP)"
                ])
                ->filter(function(Builder $builder, string $value) {
                    $builder->where('work_unit.category', $value);
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Unit Kerja", "work_unit.name")
                ->searchable()
                ->sortable(),
            Column::make("Responden")
                ->label(function($row) {
                    if (!$row->name) {
                        return "<p class=\"w-40 text-center text-xs font-extrabold p-1 px-2 rounded-md uppercase bg-gray-200 text-gray-500\">BELUM DILENGKAPI</p>";
                    } else {
                        return $row->name;
                    }
                })
                ->html()
                ->sortable(),
            Column::make("Status Penilaian")
                ->label(
                    fn($row) => view('components.datatable.respondent-questionnaire-status', compact('row'))
                ),
            Column::make("Nilai akhir",'id')
                ->label(function($row) {
                    if (!$row->score->is_done_scoring) {
                        if($row->score->is_done_filling){
                            return "<p class=\"w-32 text-center text-xs font-extrabold p-1 px-2 rounded-md uppercase bg-red-200 text-red-800\">BELUM DINILAI</p>";
                        } else {
                            return "<p class=\"w-32 text-center text-xs font-extrabold p-1 px-2 rounded-md uppercase bg-gray-200 text-gray-500\">BELUM DIKIRIM</p>";
                        }
                    } else {
                        return $row->score->total_score;
                    }
                })
                ->html(),
            Column::make("Aksi",'id')
                ->label(fn($row) => view('components.datatable.respondent-datatable-actions', compact('row'))),
            // COLLAPSED COLUMNS
            Column::make("Detail Pengerjaan Penilaian")
                ->label(fn($row) => view('components.datatable.questionnaire-progress-details', compact('row')))
                ->collapseAlways(),
            // HIDDEN COLUMNS
            Column::make("hide", "id")->hideIf(true),
            Column::make("hide", "name")->hideIf(true),
            Column::make("hide", "role")->hideIf(true),
            Column::make("hide", "email")->hideIf(true),
            Column::make("hide", "phone")->hideIf(true),
            Column::make("hide", "whatsapp")->hideIf(true),
            Column::make("hide", "work_unit.head_name")->hideIf(true),
            Column::make("hide", "work_unit.email")->hideIf(true),
            Column::make("hide", "work_unit.phone")->hideIf(true)
        ];
    }
}
