<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Version;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
            Customer::factory(5)->create()->each(function ($customer) {
                $projects = Project::factory(rand(1, 5))->create(['customer_id' => $customer->id]);
                foreach ($projects as $project) {
                    Quotation::factory(rand(1, 3))->create(['project_id' => $project->id]);
                }
            });
        }
        
    }

