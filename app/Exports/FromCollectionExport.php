<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FromCollectionExport implements FromCollection, WithHeadings
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return collect($this->collection);
    }

    public function headings(): array
    {
        return array_keys($this->collection->first() ?? []);
    }
}
