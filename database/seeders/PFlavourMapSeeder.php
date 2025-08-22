<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PFlavourMap;
use Illuminate\Support\Str;

class PFlavourMapSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['m3.micro', 1, 1, 'm', '3', 'micro', 4, 1, 'No', 'No', 'No', 'No', 2],
            ['m3.small', 1, 2, 'm', '3', 'small', 4, 1, 'No', 'No', 'No', 'No', 2],
        	['c3.large', 2, 4, 'c', '3', 'large', 4, 2, 'No', 'No', 'No', 'No', 2],
        	['m3.large', 2, 8, 'm', '3', 'large', 4, 2, 'No', 'No', 'No', 'No', 2],
        	['r3.large', 2, 16, 'r', '3', 'large', 4, 2, 'No', 'No', 'No', 'No', 2],
        	['c3.xlarge', 4, 8, 'c', '3', 'xlarge', 4, 4, 'No', 'No', 'No', 'No', 2],
        	['m3.xlarge', 4, 16, 'm', '3', 'xlarge', 4, 4, 'No', 'No', 'No', 'No', 2],
        	['r3.xlarge', 4, 32, 'r', '3', 'xlarge', 4, 4, 'No', 'No', 'No', 'No', 2],
        	['c3.2xlarge', 8, 16, 'c', '3', '2xlarge', 4, 8, 'No', 'No', 'No', 'No', 4],
        	['m3.2xlarge', 8, 32, 'm', '3', '2xlarge', 4, 8, 'No', 'No', 'No', 'No', 4],
        	['r3.2xlarge', 8, 64, 'r', '3', '2xlarge', 4, 8, 'No', 'No', 'No', 'No', 4],
        	['m3.3xlarge', 12, 48, 'm', '3', '3xlarge', 6, 12, 'No', 'No', 'No', 'No', 6],
        	['c3.4xlarge', 16, 32, 'c', '3', '4xlarge', 8, 16, 'No', 'No', 'No', 'No', 8],
        	['m3.4xlarge', 16, 64, 'm', '3', '4xlarge', 8, 16, 'No', 'No', 'No', 'No', 8],
        	['r3.4xlarge', 16, 128, 'r', '3', '4xlarge', 8, 16, 'No', 'No', 'No', 'No', 8],
        	['m3.6xlarge', 24, 96, 'm', '3', '6xlarge', 12, 24, 'No', 'No', 'No', 'No', 12],
        	['c3.8xlarge', 32, 64, 'c', '3', '8xlarge', 16, 32, 'No', 'No', 'No', 'No', 16],
        	['m3.8xlarge', 32, 128, 'm', '3', '8xlarge', 16, 32, 'No', 'No', 'No', 'No', 16],
        	['r3.8xlarge', 32, 256, 'r', '3', '8xlarge', 16, 32, 'No', 'No', 'No', 'No', 16],
        	['r3.12xlarge', 48, 384, 'r', '3', '12xlarge', 24, 48, 'No', 'No', 'No', 'No', 24],
        	['c3.16xlarge', 64, 128, 'c', '3', '16xlarge', 32, 64, 'No', 'No', 'No', 'No', 32],
        	['m3.16xlarge', 64, 256, 'm', '3', '16xlarge', 32, 64, 'No', 'No', 'No', 'No', 32],
        	['r3.16xlarge', 64, 512, 'r', '3', '16xlarge', 32, 64, 'No', 'No', 'No', 'No', 32],
        	['c3p.xlarge', 4, 8, 'c', '3p', 'xlarge', 4, 4, 'No', 'Yes', 'No', 'No', 2],
        	['m3p.xlarge', 4, 16, 'm', '3p', 'xlarge', 4, 4, 'No', 'Yes', 'No', 'No', 2],
            ['r3p.xlarge', 4, 32, 'r', '3p', 'xlarge', 4, 4, 'No', 'Yes', 'No', 'No', 2],
        	['c3p.2xlarge', 8, 16, 'c', '3p', '2xlarge', 4, 8, 'No', 'Yes', 'No', 'No', 4],
        	['m3p.2xlarge', 8, 32, 'm', '3p', '2xlarge', 4, 8, 'No', 'Yes', 'No', 'No', 4],
        	['r3p.2xlarge', 8, 64, 'r', '3p', '2xlarge', 4, 8, 'No', 'Yes', 'No', 'No', 4],
        	['m3p.3xlarge', 12, 48, 'm', '3p', '3xlarge', 6, 12, 'No', 'Yes', 'No', 'No', 6],
        	['c3p.4xlarge', 16, 32, 'c', '3p', '4xlarge', 8, 16, 'No', 'Yes', 'No', 'No', 8],
        	['m3p.4xlarge', 16, 64, 'm', '3p', '4xlarge', 8, 16, 'No', 'Yes', 'No', 'No', 8],
        	['r3p.4xlarge', 16, 128, 'r', '3p', '4xlarge', 8, 16, 'No', 'Yes', 'No', 'No', 8],
        	['m3p.6xlarge', 24, 96, 'm', '3p', '6xlarge', 12, 24, 'No', 'Yes', 'No', 'No', 12],
        	['c3p.8xlarge', 32, 64, 'c', '3p', '8xlarge', 16, 32, 'No', 'Yes', 'No', 'No', 16],
        	['m3p.8xlarge', 32, 128, 'm', '3p', '8xlarge', 16, 32, 'No', 'Yes', 'No', 'No', 16],
        	['r3p.8xlarge', 32, 256, 'r', '3p', '8xlarge', 16, 32, 'No', 'Yes', 'No', 'No', 16],
        	['r3p.12xlarge', 48, 384, 'r', '3p', '12xlarge', 24, 48, 'No', 'Yes', 'No', 'No', 24],
        	['c3p.16xlarge', 64, 128, 'c', '3p', '16xlarge', 32, 64, 'No', 'Yes', 'No', 'No', 32],
        	['m3p.16xlarge', 64, 256, 'm', '3p', '16xlarge', 32, 64, 'No', 'Yes', 'No', 'No', 32],
        	['r3p.16xlarge', 64, 512, 'r', '3p', '16xlarge', 32, 64, 'No', 'Yes', 'No', 'No', 32],
        	['r3p.46xlarge.metal', 64, 1408, 'r', '3p', '46xlarge.metal', 32, 64, 'No', 'Yes', 'No', 'No', 32],
        	['m3gnt4.xlarge', 4, 16, 'm', '3gnt4', 'xlarge', 4, 4, 'No', 'No', 'Yes', 'No', 2],
        	['m3gnt4.2xlarge', 8, 32, 'm', '3gnt4', '2xlarge', 4, 8, 'No', 'No', 'Yes', 'No', 4],
        	['m3gnt4.4xlarge', 16, 64, 'm', '3gnt4', '4xlarge', 8, 16, 'No', 'No', 'Yes', 'No', 8],
        	['m3gnt4.8xlarge', 32, 128, 'm', '3gnt4', '8xlarge', 16, 32, 'No', 'No', 'Yes', 'No', 16],
        	['m3gnt4.16xlarge', 64, 256, 'm', '3gnt4', '16xlarge', 32, 64, 'No', 'No', 'Yes', 'No', 32],
        	['r3p.46xlarge.ddh', 342, 1480, 'r', '3p', '46xlarge.ddh', 171, 342, 'No', 'No', 'No', 'Yes', 171],
        	['m3.micro.dr', 1, 1, 'm', '3', 'micro', 4, 1, 'Yes', 'No', 'No', 'No', 2],
        	['m3.small.dr', 1, 2, 'm', '3', 'small', 4, 1, 'Yes', 'No', 'No', 'No', 2],
        	['c3.large.dr', 2, 4, 'c', '3', 'large', 4, 2, 'Yes', 'No', 'No', 'No', 2],
        	['m3.large.dr', 2, 8, 'm', '3', 'large', 4, 2, 'Yes', 'No', 'No', 'No', 2],
        	['r3.large.dr', 2, 16, 'r', '3', 'large', 4, 2, 'Yes', 'No', 'No', 'No', 2],
        	['c3.xlarge.dr', 4, 8, 'c', '3', 'xlarge', 4, 4, 'Yes', 'No', 'No', 'No', 2],
        	['m3.xlarge.dr', 4, 16, 'm', '3', 'xlarge', 4, 4, 'Yes', 'No', 'No', 'No', 2],
        	['r3.xlarge.dr', 4, 32, 'r', '3', 'xlarge', 4, 4, 'Yes', 'No', 'No', 'No', 2],
        	['c3.2xlarge.dr', 8, 16, 'c', '3', '2xlarge', 4, 8, 'Yes', 'No', 'No', 'No', 4],
        	['m3.2xlarge.dr', 8, 32, 'm', '3', '2xlarge', 4, 8, 'Yes', 'No', 'No', 'No', 4],
        	['r3.2xlarge.dr', 8, 64, 'r', '3', '2xlarge', 4, 8, 'Yes', 'No', 'No', 'No', 4],
        	['m3.3xlarge.dr', 12, 48, 'm', '3', '3xlarge', 6, 12, 'Yes', 'No', 'No', 'No', 6],
        	['c3.4xlarge.dr', 16, 32, 'c', '3', '4xlarge', 8, 16, 'Yes', 'No', 'No', 'No', 8],
        	['m3.4xlarge.dr', 16, 64, 'm', '3', '4xlarge', 8, 16, 'Yes', 'No', 'No', 'No', 8],
        	['r3.4xlarge.dr', 16, 128, 'r', '3', '4xlarge', 8, 16, 'Yes', 'No', 'No', 'No', 8],
        	['m3.6xlarge.dr', 24, 96, 'm', '3', '6xlarge', 12, 24, 'Yes', 'No', 'No', 'No', 12],
        	['c3.8xlarge.dr', 32, 64, 'c', '3', '8xlarge', 16, 32, 'Yes', 'No', 'No', 'No', 16],
        	['m3.8xlarge.dr', 32, 128, 'm', '3', '8xlarge', 16, 32, 'Yes', 'No', 'No', 'No', 16],
        	['r3.8xlarge.dr', 32, 256, 'r', '3', '8xlarge', 16, 32, 'Yes', 'No', 'No', 'No', 16],
        	['r3.12xlarge.dr', 48, 384, 'r', '3', '12xlarge', 24, 48, 'Yes', 'No', 'No', 'No', 24],
        	['c3.16xlarge.dr', 64, 128, 'c', '3', '16xlarge', 32, 64, 'Yes', 'No', 'No', 'No', 32],
        	['m3.16xlarge.dr', 64, 256, 'm', '3', '16xlarge', 32, 64, 'Yes', 'No', 'No', 'No', 32],
        	['r3.16xlarge.dr', 64, 512, 'r', '3', '16xlarge', 32, 64, 'Yes', 'No', 'No', 'No', 32],

            
            
        	

         
        ];

        foreach ($data as $item) {
            PFlavourMap::updateOrCreate(
                ['flavour' => $item[0]],
                [
                    'vcpu' => $item[1],
                    'vram' => $item[2],
                    'type' => $item[3],
                    'generation' => $item[4],
                    'memory_label' => $item[5],
                    'windows_license_count' => $item[6],
                    'rhel' => $item[7],
                    'dr' => $item[8],
                    'pin' => $item[9],
                    'gpu' => $item[10],
                    'ddh' => $item[11],
                    'mssql' => $item[12],
                ]
            );
        }
    }
}

