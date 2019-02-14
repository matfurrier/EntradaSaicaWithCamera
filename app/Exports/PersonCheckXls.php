<?php

namespace App\Exports;

use App\Person;
use App\PersonCheck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use \Maatwebsite\Excel\Sheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PersonCheckXls implements FromView
{
    protected $filters;

    public function __construct($fil)
    {
        $this->filters = $fil;
    }

  /*  public function registerEvents(): array
    {
        return [
            BeforeExport::class  => function(BeforeExport $event) {
                $event->writer->setCreator('Patrick');
            },
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);

                $event->sheet->styleCells(
                    'B2:G8',
                    [
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFF0000'],
                            ],
                        ]
                    ]
                );
            },
        ];
    }*/

    public function view(): View
    {
        $person_id = $this->filters['person'];

        $dstar = $this->filters['dstar'];

        $dend = $this->filters['dend'];

        $division = $this->filters['division'];

        $rol = $this->filters['rol'];

        $datos = Person::leftjoin('persons_divisions','persons_divisions.person_id', 'persons.id')

            ->leftjoin('divisions','persons_divisions.division_id', 'divisions.id')

            ->leftjoin('persons_rols','persons_rols.person_id', 'persons.id')

            ->leftjoin('rols','persons_rols.rol_id', 'rols.id')

            ->leftjoin('persons_checks','persons_checks.person_id', 'persons.id');

        if ($person_id > 0) $datos->where('persons.id', $person_id);

        if ($division > 0) $datos->where('divisions.id', $division);

        $datos->whereBetween('moment', [$dstar, $dend]);

        $datos->orderby('moment', 'asc');

        if ($rol > 0) $datos->where( 'rols.id', $rol);

        $total = $datos->select('persons.names', 'persons.token', 'persons.id', 'divisions.names as div', 'rols.rol', DB::raw('DATE_FORMAT(persons_checks.moment, "%Y-%m-%d %H:%i") as moment'))->count();

        $data =  $datos->get();

        $list = [];

        $times = [];

        for ($i = 0; $i <= count($data) - 1; $i++) {

            $start = Carbon::parse($data[$i]['moment']);

            if ($i + 1 <= count($data) - 1) {

                $end = Carbon::parse($data[$i+1]['moment']);

            } else {

                $data[$i]['dend'] = '-';

                $data[$i]['dstar'] = $data[$i]['moment'];

                $data[$i]['hours'] = 0;

                $list[] = $data[$i];

                break;
            }

            if ( $start->day == $end->day) {

                $hours = (Carbon::parse($data[$i+1]['moment'])->diffInMinutes(Carbon::parse($data[$i]['moment'])));

                $times[] = $hours ;

                $data[$i]['dend'] = $data[$i+1]['moment'];

                $data[$i]['dstar'] = $data[$i]['moment'];

                if (!is_int( $hours / 60)) {

                    $aux = (string) $hours /60;

                    $h = explode('.', $aux);

                    $h[1] = round(((int) substr( $h[1], 0, 2) / 100) * 60);

                    $h[1] = $h[1] < 10 ? '0'.$h[1] : $h[1];
                }  else {

                    $h[0] = $hours / 60;  $h[1] = 0;
                }

                $data[$i]['hours'] = $h[0] . ':' . $h[1]; // number_format($hours / 60, 2, ':', '');

                $list[] = $data[$i]->toArray();

                if (($i + 2) <= count($data) - 1) {

                    if ($end->day < Carbon::parse($data[$i+2]['moment'])->day) {

                        $totalminut = collect($times)->reduce(function ($carry, $item) {
                            return $carry + $item;
                        });

                        $aux = (string) $totalminut  /60;

                        $h = explode('.', $aux);

                        $h[1] = round(((int) substr( $h[1], 0, 2) / 100) * 60);

                        $h[1] = $h[1] < 10 ? '0'.$h[1] : $h[1];

                        $list[] =['names'=> '', 'token' =>'', 'div' => '', 'rol' => '',  'moment' => '-', 'dstar' => '', 'dend' => 'Total', 'hours' => $h[0] . ':' . $h[1]];

                        $times= [];
                    }

                }

                $i = $i + 1;

                if (($i + 2) > count($data) - 1) { $i = count($data) - 1;}


            } else {

                $data[$i]['dend'] = '-';

                $data[$i]['dstar'] = $data[$i]['moment'];

                $data[$i]['hours'] = '0';

                $list[] = $data[$i]->toArray();

                $totalminut = collect($times)->reduce(function ($carry, $item) {
                    return $carry + $item;
                });

                $hours = $totalminut * (1/60);

                $list[] =  ['names'=> '', 'token' =>'', 'div' => '', 'rol' => '',  'moment' => '-', 'dstar' => '', 'dend' => 'Total', 'hours' => number_format($hours, 2, '.', '')];

                $times= [];
            }

        }


        return view('reports.xls', [
            'list' => $list
        ]);
    }


}
