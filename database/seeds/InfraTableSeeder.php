<?php

use Illuminate\Database\Seeder;

class InfraTableSeeder extends Seeder
{
  /**
  * Run the database seeds.
  *
  * @return void
  */
  public function run()
  {



    // municipality

    DB::table('municipalities')->insert([
      [
        'municipality_code'=>1101000,
        'municipality_name'=>'Atok',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'municipality_code'=>1103000,
        'municipality_name'=>'Bakun',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'municipality_code'=>1104000,
        'municipality_name'=>'Bokod',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1105000,
        'municipality_name'=>'Buguias',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1106000,
        'municipality_name'=>'Itogon',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1107000,
        'municipality_name'=>'Kabayan',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1108000,
        'municipality_name'=>'Kapangan',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1109000,
        'municipality_name'=>'Kibungan',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1110000,
        'municipality_name'=>'La Trinidad',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1111000,
        'municipality_name'=>'Mankayan',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1112000,
        'municipality_name'=>'Sablan',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1113000,
        'municipality_name'=>'Tuba',
        'created_at' => now(),
        'updated_at' => now()

      ],
      [
        'municipality_code'=>1114000,
        'municipality_name'=>'Tublay',
        'created_at' => now(),
        'updated_at' => now()
      ]

    ]);


    // barangays
    DB::table('barangays')->insert([

      [
        'barangay_code'=>1101001,
        'barangay_name'=>'Abiang',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101002,
        'barangay_name'=>'Caliking',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101003,
        'barangay_name'=>'Cattubo',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101004,
        'barangay_name'=>'Naguey',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101005,
        'barangay_name'=>'Paoay',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101006,
        'barangay_name'=>'Pasdong',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101007,
        'barangay_name'=>'Poblacion(Atok)',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1101008,
        'barangay_name'=>'Topdac',
        'municipality_id'=>1,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103001,
        'barangay_name'=>'Ampusongan',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103002,
        'barangay_name'=>'Bagu',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103003,
        'barangay_name'=>'Dalipey',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103004,
        'barangay_name'=>'Gambang',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103005,
        'barangay_name'=>'Kayapa',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103006,
        'barangay_name'=>'Poblacion(Bakun)',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1103007,
        'barangay_name'=>'Sinacbat',
        'municipality_id'=>2,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104001,
        'barangay_name'=>'Ambuclao',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104002,
        'barangay_name'=>'Bila',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104003,
        'barangay_name'=>'Bokod-Bisal',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104004,
        'barangay_name'=>'Daclan',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104005,
        'barangay_name'=>'Ekip',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104006,
        'barangay_name'=>'Karao',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104007,
        'barangay_name'=>'Nawal',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104008,
        'barangay_name'=>'Pito',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104009,
        'barangay_name'=>'Poblacion(Bokod)',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105002,
        'barangay_name'=>'Amgaleyguey',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105003,
        'barangay_name'=>'Amlimay',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105004,
        'barangay_name'=>'Baculongan Norte',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105014,
        'barangay_name'=>'Baculongan Sur',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105006,
        'barangay_name'=>'Bangao',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105007,
        'barangay_name'=>'Buyacaoan',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105008,
        'barangay_name'=>'Calamagan',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105015,
        'barangay_name'=>'Lengaoan',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105010,
        'barangay_name'=>'Loo',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105012,
        'barangay_name'=>'Natubleng',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105013,
        'barangay_name'=>'Poblacion(Buguias)',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105009,
        'barangay_name'=>'Catlubong',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106001,
        'barangay_name'=>'Ampucao',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106002,
        'barangay_name'=>'Dalupirip',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106003,
        'barangay_name'=>'Gumatdang',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106004,
        'barangay_name'=>'Loacan',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106005,
        'barangay_name'=>'Poblacion(Itogon)',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106006,
        'barangay_name'=>'Tinongdan',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106007,
        'barangay_name'=>'Tuding',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106008,
        'barangay_name'=>'Ucab',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1106009,
        'barangay_name'=>'Virac',
        'municipality_id'=>5,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107001,
        'barangay_name'=>'Adaoay',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107002,
        'barangay_name'=>'Anchukey',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107003,
        'barangay_name'=>'Ballay',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107004,
        'barangay_name'=>'Bashoy',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107005,
        'barangay_name'=>'Batan',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107009,
        'barangay_name'=>'Duacan',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107010,
        'barangay_name'=>'Eddet',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107012,
        'barangay_name'=>'Gusaran',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107013,
        'barangay_name'=>'Kabayan Barrio',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107014,
        'barangay_name'=>'Lusod',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107016,
        'barangay_name'=>'Pacso',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107017,
        'barangay_name'=>'Poblacion(Kabayan)',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1107018,
        'barangay_name'=>'Tawangan',
        'municipality_id'=>6,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108001,
        'barangay_name'=>'Balakbak',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108002,
        'barangay_name'=>'Beleng-Belis',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108003,
        'barangay_name'=>'Boklaoan',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108004,
        'barangay_name'=>'Cayapes',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108006,
        'barangay_name'=>'Cuba',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108008,
        'barangay_name'=>'Datakan',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108009,
        'barangay_name'=>'Gadang',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108010,
        'barangay_name'=>'Gaswiling',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108011,
        'barangay_name'=>'Labueg',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108013,
        'barangay_name'=>'Paykek',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108014,
        'barangay_name'=>'Poblacion(Kapangan)',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108016,
        'barangay_name'=>'Pongayan',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108015,
        'barangay_name'=>'Pudong',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108017,
        'barangay_name'=>'Sagubo',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1108018,
        'barangay_name'=>'Tabao-ao',
        'municipality_id'=>7,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109001,
        'barangay_name'=>'Badeo',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109002,
        'barangay_name'=>'Lubo',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109003,
        'barangay_name'=>'Madaymen',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109004,
        'barangay_name'=>'Palina',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109005,
        'barangay_name'=>'Poblacion(Kibungan)',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109006,
        'barangay_name'=>'Sagpat',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1109007,
        'barangay_name'=>'Tacadang',
        'municipality_id'=>8,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110001,
        'barangay_name'=>'Alapang',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110002,
        'barangay_name'=>'Alno',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110003,
        'barangay_name'=>'Ambiong',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110004,
        'barangay_name'=>'Bahong',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110005,
        'barangay_name'=>'Balili(La Trinidad)',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110006,
        'barangay_name'=>'Beckel',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110008,
        'barangay_name'=>'Betag',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110007,
        'barangay_name'=>'Bineng',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110009,
        'barangay_name'=>'Cruz',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110010,
        'barangay_name'=>'Lubas',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110011,
        'barangay_name'=>'Pico',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110012,
        'barangay_name'=>'Poblacion(LTB)',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110013,
        'barangay_name'=>'Puguis',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110014,
        'barangay_name'=>'Shilan',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110015,
        'barangay_name'=>'Tawang',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1110016,
        'barangay_name'=>'Wangal',
        'municipality_id'=>9,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111001,
        'barangay_name'=>'Balili(Mankayan)',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111002,
        'barangay_name'=>'Bedbed',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111003,
        'barangay_name'=>'Bulalacao',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111004,
        'barangay_name'=>'Cabiten',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111005,
        'barangay_name'=>'Colalo',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111008,
        'barangay_name'=>'Paco',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111006,
        'barangay_name'=>'Guinaoang',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111010,
        'barangay_name'=>'Poblacion(Mankayan)',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111011,
        'barangay_name'=>'Sapid',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111012,
        'barangay_name'=>'Tabio',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111013,
        'barangay_name'=>'Taneg',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112002,
        'barangay_name'=>'Bagong',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112003,
        'barangay_name'=>'Ballulay',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112004,
        'barangay_name'=>'Banangan',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112005,
        'barangay_name'=>'Banengbeng',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112006,
        'barangay_name'=>'Bayabas',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112007,
        'barangay_name'=>'Kamog',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112010,
        'barangay_name'=>'Pappa',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1112011,
        'barangay_name'=>'Poblacion(Sablan)',
        'municipality_id'=>11,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113001,
        'barangay_name'=>'Ansagan',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113003,
        'barangay_name'=>'Camp 3',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113004,
        'barangay_name'=>'Camp 4',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113002,
        'barangay_name'=>'Camp 1',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113006,
        'barangay_name'=>'Nangalisan',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113007,
        'barangay_name'=>'Poblacion(Tuba)',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113008,
        'barangay_name'=>'San Pascual',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113009,
        'barangay_name'=>'Tabaan Norte',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113010,
        'barangay_name'=>'Tabaan Sur',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113011,
        'barangay_name'=>'Tadiangan',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113012,
        'barangay_name'=>'Taloy Norte',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113013,
        'barangay_name'=>'Taloy Sur',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1113014,
        'barangay_name'=>'Twin Peaks',
        'municipality_id'=>12,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114001,
        'barangay_name'=>'Ambassador',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114002,
        'barangay_name'=>'Ambongdolan',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114003,
        'barangay_name'=>'Ba-ayan',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114004,
        'barangay_name'=>'Basil',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114006,
        'barangay_name'=>'Caponga(Pob.)',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114005,
        'barangay_name'=>'Daclan(Tublay)',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114007,
        'barangay_name'=>'Tublay Central',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1114008,
        'barangay_name'=>'Tuel',
        'municipality_id'=>13,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1104010,
        'barangay_name'=>'Tikey',
        'municipality_id'=>3,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1111009,
        'barangay_name'=>'Palasaan',
        'municipality_id'=>10,
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'barangay_code'=>1105016,
        'barangay_name'=>'Sebang',
        'municipality_id'=>4,
        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);

    // account classification
    DB::table('account_classifications')->insert([
      [
        'classification'=>'Capital Outlay',
        'status'=>'active',
        'created_at' => now(),
        'updated_at' => now()
      ],
      [
        'classification'=>'MOOE',
        'status'=>'active',
        'created_at' => now(),
        'updated_at' => now()
      ]
    ]);


    // contractors
    DB::table('contractors')->insert(
      [
        [
          'business_name'=>'Contractor1',
          'owner'=>'Owner 1',
          'address'=>'Wangal, La Trinidad, Benguet',
          'contact_number'=>'128566',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'business_name'=>'Contractor2',
          'owner'=>'Owner 2',
          'address'=>'Pusel',
          'contact_number'=>'09260878700',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'business_name'=>'Benguet Corps',
          'owner'=>'Mr Benguet',
          'address'=>'Benguet',
          'contact_number'=>'09938292932',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ]
      ]);

      // Funds
      DB::table('funds')->insert([

        [
          'source'=>'PSB',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'Calamity Fund',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'GF',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'Trust',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'PRDP',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'SLRF',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'PAMANA',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'PRNDP',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'DOE',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'SEF',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'CHARMP',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'Supplemental Budget',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'Other Funds',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'Support',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'20% PDF',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'source'=>'GENERAL FUND',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ]
      ]);

      // mode of procurement
      DB::table('procurement_modes')->insert([
        [
          'mode'=>'Bidding',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'mode'=>'SVP',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'mode'=>'Negotiated',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
      ]);

      // sectors

      DB::table('sectors')->insert([
        [
          'sector_name'=>'Support to Social',
          'sector_type'=>'barangay_development',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Support to Economic',
          'sector_type'=>'barangay_development',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Support to Dev-Ad',
          'sector_type'=>'barangay_development',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Support to Health',
          'sector_type'=>'barangay_development',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Support to Special Projects',
          'sector_type'=>'barangay_development',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'General Services',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Benguet General Hospital',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Atok District Hospital',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Northern Benguet District Hospital',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Itogon District Hospital',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Kapangan District Hospital',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'sector_name'=>'Social Welfare and Development',
          'sector_type'=>'office',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
      ]);


      // projtypes
      DB::table('projtypes')->insert([
        [
          'type'=>'FMR',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Bridge',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Multi-Purpose Building/Hall/Outpost',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'School Building',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Senior Citizens Building',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Domestic Water Supply/Irrigation/Waterworks',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Footbridges',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Footpath/Foot Trail',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Multi-Purpose Shed/Waiting Shed',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Multi-Purpose Gym/Basketball Count',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Drainage Canal',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Flood Control/Riprapping/Slope Protection',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Provincial Road',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Health &amp; PHO Facilities',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Agriculture Services',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Veterinary Services',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Comfort Room/Public Toilet',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Station',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Health Infastructure',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Provincial Government Properties',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
        [
          'type'=>'Training Center',
          'status'=>'active',
          'created_at' => now(),
          'updated_at' => now()
        ],
      ]);


      DB::table('project_plans')->insert([
        [
          'app_group_no'=>'8',
          'project_no'=>'GF 2019-11',
          'project_title'=>'Construction of a 4-Storey Building, Phase II, ADH',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'18084',
          'project_title'=>'Construction of a 4- Storey hospital building (Phase I), ADH',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1660000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 2019-324B',
          'project_title'=>'Construction of Open Gymnasium at Kayapa Elementary School ',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 2019-10A',
          'project_title'=>'Construction of an Open Gymnasium at Bangao Elem. School, Bangao',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 2019-10B',
          'project_title'=>'Construction of an Open Gymnasium at Sagubo Elementary School ',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF 20050',
          'project_title'=>'Construction of Parking Area & Driveway with Slope Protection at the back of Legislative (Phase I)',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF2017-10C',
          'project_title'=>'Improvement of Provincial Capitol Compound',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"2",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF 2019-26',
          'project_title'=>'Construction of PCL-LNB Multi-Purpose Building (Phase III), Wangal, La Trinidad, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20109',
          'project_title'=>'Improvement of Atayan-Balili Provincial Road, Balili, Mankayan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"6000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],

        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 20001',
          'project_title'=>'Improvement/Rehabilitation Along Jose Mencio Provincial Road, Atok',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"4000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 10D',
          'project_title'=>'Construction of Dada Slope Protection, Poblacion, Bakun, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"4000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20057',
          'project_title'=>'Opening of Yupo-an-Penas FMR, Dalipey, Bakun, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20063',
          'project_title'=>'Opening of Bulisay Agay-ay to Tamangan FMR, Kayapa',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 2019 10E',
          'project_title'=>'Construction of an Open Gymnasium at Bokod Central School, Poblacion, Bokod, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"4000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'19038',
          'project_title'=>'Improvement along Bolo-Tocdo FMR, Poblacion, La Trinidad, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 10F',
          'project_title'=>'Construction of Retaining Wall & Drainage Canal along the Sinking Portion at Poblacion, Buguias, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20087',
          'project_title'=>'Improvement of Paneg-an - Tamangan FMR (Camanlanga Section), Sebang, Buguias',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20069',
          'project_title'=>'Improvement of JFMR-NMSBaso Road, Amgaleyguey, Buguias, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF 2017 - 10G',
          'project_title'=>'Construction of Upper Tram CDC, Ucab, Itogon, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1200000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 20009',
          'project_title'=>'Improvement of Baguio Gold Elementary School, Tuding, Itogon',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20037',
          'project_title'=>'Ground Improvement of Municipal Evacuation & Crisis Center at Keystone, Ucab, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"3000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20009',
          'project_title'=>'Improvement Along Lomon - Paykek Provincial Road, Kapangan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"5000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF20060',
          'project_title'=>'Construction of Waste Storage Facility, Kapangan, KDH, Kapangan, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20099',
          'project_title'=>'Road Opening at Bolo to Pudong Barangay Hall, Pudong, Kapangan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20102',
          'project_title'=>'Concreting along Sapdaan-Amog-on FMR, Sagpat',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 20002',
          'project_title'=>'Improvement/Rehabilitation Along Sagpat-Sapdaan Provincial Road, Kibungan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 20010',
          'project_title'=>'Improvement/Rehabilitation along Halsema-Madaymen Provincial Road, Kibungan, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"3500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20012',
          'project_title'=>'Improvement of Road Along Phase II - Block 4, Wangal, Housing, La Trinidad,',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"3080000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'LDRRMF 10H',
          'project_title'=>'Construction of Drainage Canal System & Protection Wall at the Benguet SPED Center, Wangal, La Trinidad',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20038',
          'project_title'=>'Construction of Drainage Canal from the National Highway Going up to Cruz-Tawang Road at Sitio Atta, Cruz, La Trinidad',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20022',
          'project_title'=>'Completion of Waterworks Project at Sitio Marlboro, Beckel, La Trinidad',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20039',
          'project_title'=>'Construction of Drainage along Lubas Road at Sebseb, La Trinidad',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20031',
          'project_title'=>'Completion of San Roque Child Development Center, Paco, Mankayan, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"1500000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'20024',
          'project_title'=>'Construction of Balili National Highschool Gymnasium Phase I, Balili, Mankayan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"2000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF20022',
          'project_title'=>'Construction of Multi-Purpose Gym at Palali Elementary School, Palali, Sablan',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"5000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'GF20054',
          'project_title'=>'Construction of Slope Protection (Various Section), Eco Farm, Bulala, Sablan, Benguet',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"3000000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],
        [
          'app_group_no'=>'8',
          'project_no'=>'SEF 2019-05',
          'project_title'=>'Construction of School Building for Saguitlang E/S in Saguitlang',
          "project_year"=>"2020",
          "year_funded"=>"2020",
          "project_type"=>"supplementary",
          "date_added"=>"2020-10-13",
          "sector_id"=>"8",
          "municipality_id"=>"1",
          "barangay_id"=>"7",
          "projtype_id"=>"20",
          "mode_id"=>"1",
          "fund_id"=>"16",
          "account_id"=>"1",
          "abc"=>"4200000.00",
          "abc_post_date"=>"Oct-2020",
          "sub_open_date"=>"Oct-2020",
          "award_notice_date"=>"Nov-2020",
          "contract_signing_date"=>"Nov-2020",
          "status"=>"pending",
          "re_bid_count"=>"0"
        ],


      ]);


      DB::table('procurement_processes')->insert([
        [
          "process_name" => "Pre-procurement",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Advertisement",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Pre-bid Conference",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Submission of Bid",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Bid Evaluation",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Post Qualification",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Issuance of Notice of Awards",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Contract Preparation and Signing",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Approval by Higher Authority",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ],[
          "process_name" => "Notice to Proceed",
          "mode_of_procurement" => "bidding",
          "status" => 'active'
        ]
      ]);

      DB::table('procurement_processes')->insert([
        [
          "process_name" => "Advertisement",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Submission of Bid",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Bid Evaluation",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Post Qualification",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Issuance of Notice of Awards",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Contract Preparation and Signing",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Approval by Higher Authority",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ],[
          "process_name" => "Notice to Proceed",
          "mode_of_procurement" => "svp",
          "status" => 'active'
        ]
      ]);


      // fill procacts and EventConfig
      $project_plans=DB::table('project_plans')->get();
      foreach ($project_plans as $project_plan) {
        DB::table('procacts')->insert([
          [   "plan_id"=>$project_plan->plan_id,   'created_at' => now(),   'updated_at' => now()],
        ]);
        DB::table('project_timelines')->insert([
          ["plan_id"=>$project_plan->plan_id,"procact_id"=>$project_plan->plan_id, "timeline_status"=>"pending", 'created_at' => now(), 'created_at' => now(),],
        ]);

        DB::table('project_plans')->where('project_plans.plan_id',$project_plan->plan_id)
        ->update([
          'latest_procact_id'=>$project_plan->plan_id
        ]);

        if($project_plan->abc>=5000000){
          DB::table('project_activity_status')->insert([
            ["plan_id"=>$project_plan->plan_id, "procact_id"=>$project_plan->plan_id, "pre_proc"=>"pending", 'created_at' => now(), 'created_at' => now(),],
          ]);
        }
        else{
          DB::table('project_activity_status')->insert([
            ["plan_id"=>$project_plan->plan_id, "procact_id"=>$project_plan->plan_id, "pre_proc"=>"not_needed", 'created_at' => now(), 'created_at' => now(),],
          ]);
        }
      }

    }
  }
