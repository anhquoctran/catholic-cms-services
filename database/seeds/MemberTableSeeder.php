<?php

use Illuminate\Database\Seeder;

class MemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $index = 1;
        for($i = 1; $i < 50; $i++) {

            $rand = rand(1, 2);
            $lastname = $rand == 1 ? SeederHelper::$maleLastname[array_rand(SeederHelper::$maleLastname)] : SeederHelper::$femaleLastname[array_rand(SeederHelper::$femaleLastname)];
            $sain_name = $rand = 1 ? SeederHelper::$saint_name_male[array_rand(SeederHelper::$saint_name_male)] : SeederHelper::$saint_name_female[array_rand(SeederHelper::$saint_name_female)];
            $gender = SeederHelper::getGenderFromRandomName($lastname);
            $middlename = SeederHelper::$middleName[array_rand(SeederHelper::$middleName)];
            $firstname = SeederHelper::getFirstname($gender, $middlename);
            $full_name = $lastname.' '.$middlename.' '.$firstname;
            $full_name_en = SeederHelper::removeVietnameseCharacters($full_name);
            $saint_name_relativer = $rand = 1 ? SeederHelper::$saint_name_female[array_rand(SeederHelper::$saint_name_female)] : SeederHelper::$saint_name_male[array_rand(SeederHelper::$saint_name_male)];
            $gender_relativer = $rand == 1 ? 2 : 1;


            DB::table('membertbl')->insert([
                'uuid' => SeederHelper::getNextUuid(),
                'saint_name' => $sain_name,
                'full_name' => $full_name,
                'full_name_en' => $full_name_en,
                'gender' => $rand,
                'birth_year' => rand(1950, 1987),
                'saint_name_of_relativer' => $saint_name_relativer,
                'gender_of_relativer' => $gender_relativer,
            ]);
            echo "Creating draft member #".$index.'\r\n';
        }

    }
}
