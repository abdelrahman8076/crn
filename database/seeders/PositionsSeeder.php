<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create hierarchy: CEO > VP > Director > Manager > Sales > Staff
        
        $ceo = Position::firstOrCreate(
            ['name' => 'CEO'],
            [
                'description' => 'Chief Executive Officer',
                'level' => 0,
                'sort_order' => 1,
            ]
        );
        
        $vp = Position::firstOrCreate(
            ['name' => 'Vice President'],
            [
                'description' => 'Vice President',
                'parent_id' => $ceo->id,
                'level' => 1,
                'sort_order' => 1,
            ]
        );
        
        $director = Position::firstOrCreate(
            ['name' => 'Director'],
            [
                'description' => 'Director',
                'parent_id' => $vp->id,
                'level' => 2,
                'sort_order' => 1,
            ]
        );
        
        $manager = Position::firstOrCreate(
            ['name' => 'Manager'],
            [
                'description' => 'Manager',
                'parent_id' => $director->id,
                'level' => 3,
                'sort_order' => 1,
            ]
        );
        
        $sales = Position::firstOrCreate(
            ['name' => 'Sales'],
            [
                'description' => 'Sales Representative',
                'parent_id' => $manager->id,
                'level' => 4,
                'sort_order' => 1,
            ]
        );
        
        $staff = Position::firstOrCreate(
            ['name' => 'Staff'],
            [
                'description' => 'Staff Member',
                'parent_id' => $sales->id,
                'level' => 5,
                'sort_order' => 1,
            ]
        );
    }
}
