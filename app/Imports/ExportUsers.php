<?php

namespace App\Imports;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportUsers implements FromCollection
{

    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

   public function collection()
   {
    return collect([$this->data]);
    //    return collect($this->data);
   }
}