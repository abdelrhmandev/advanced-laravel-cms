<?php
namespace Database\Seeders;
use App\Traits\Functions;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageDatabaseSeeder extends Seeder
{
    use Functions;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pages')->delete();
        DB::table('page_translations')->delete();

        $items = [];
        $items[0] = ['image' => null,'template'=>'homepage','created_at'=>NOW()];
        $items[1] = ['image' => null,'template'=>'about-us','created_at'=>NOW()];

        DB::table('pages')->insert($items);

        $translated_items = [

            ['title' => 'Home', 'slug' => $this->make_slug('Home'), 'description'=>'Home','locale' => 'en', 'page_id' => 1],
            ['title' => 'الرئيسيه', 'slug' =>$this->make_slug('الرئيسيه'), 'description'=>'الرئيسيه','locale' => 'ar', 'page_id' => 1],

            ['title' => 'About Us', 'slug' => $this->make_slug('About Us'), 'description'=>'About Us','locale' => 'en', 'page_id' => 2],
            ['title' => 'نبذه عن الشركه', 'slug' =>$this->make_slug('نبذه عن الشركه'), 'description'=>'نبذه عن الشركه','locale' => 'ar', 'page_id' => 2],
        ];

        DB::table('page_translations')->insert($translated_items);
    }
}
