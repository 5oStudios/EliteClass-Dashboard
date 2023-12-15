<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('role_has_permissions')->delete();
        
        DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => '1',
                'role_id' => '1',
            ),
            1 => 
            array (
                'permission_id' => '2',
                'role_id' => '1',
            ),
            2 => 
            array (
                'permission_id' => '3',
                'role_id' => '1',
            ),
            3 => 
            array (
                'permission_id' => '4',
                'role_id' => '1',
            ),
            4 => 
            array (
                'permission_id' => '5',
                'role_id' => '1',
            ),
            5 => 
            array (
                'permission_id' => '6',
                'role_id' => '1',
            ),
            6 => 
            array (
                'permission_id' => '7',
                'role_id' => '1',
            ),
            7 => 
            array (
                'permission_id' => '8',
                'role_id' => '1',
            ),
            8 => 
            array (
                'permission_id' => '9',
                'role_id' => '1',
            ),
            9 => 
            array (
                'permission_id' => '10',
                'role_id' => '1',
            ),
            18 => 
            array (
                'permission_id' => '20',
                'role_id' => '1',
            ),
            // 19 => 
            // array (
            //     'permission_id' => '21',
            //     'role_id' => '1',
            // ),
            20 => 
            array (
                'permission_id' => '22',
                'role_id' => '1',
            ),
            21 => 
            array (
                'permission_id' => '23',
                'role_id' => '1',
            ),
            23 => 
            array (
                'permission_id' => '25',
                'role_id' => '1',
            ),
            24 => 
            array (
                'permission_id' => '26',
                'role_id' => '1',
            ),
            29 => 
            array (
                'permission_id' => '32',
                'role_id' => '1',
            ),
            // 38 => 
            // array (
            //     'permission_id' => '41',
            //     'role_id' => '1',
            // ),
            43 => 
            array (
                'permission_id' => '46',
                'role_id' => '1',
            ),
            44 => 
            array (
                'permission_id' => '47',
                'role_id' => '1',
            ),
            46 => 
            array (
                'permission_id' => '49',
                'role_id' => '1',
            ),
            50 => 
            array (
                'permission_id' => '53',
                'role_id' => '1',
            ),
            56 => 
            array (
                'permission_id' => '59',
                'role_id' => '1',
            ),
            65 => 
            array (
                'permission_id' => '68',
                'role_id' => '1',
            ),
            70 => 
            array (
                'permission_id' => '73',
                'role_id' => '1',
            ),
            73 => 
            array (
                'permission_id' => '76',
                'role_id' => '1',
            ),
            74 => 
            array (
                'permission_id' => '77',
                'role_id' => '1',
            ),
            75 => 
            array (
                'permission_id' => '78',
                'role_id' => '1',
            ),
            76 => 
            array (
                'permission_id' => '79',
                'role_id' => '1',
            ),
            77 => 
            array (
                'permission_id' => '80',
                'role_id' => '1',
            ),
            78 => 
            array (
                'permission_id' => '81',
                'role_id' => '1',
            ),
            79 => 
            array (
                'permission_id' => '82',
                'role_id' => '1',
            ),
            80 => 
            array (
                'permission_id' => '83',
                'role_id' => '1',
            ),
            81 => 
            array (
                'permission_id' => '84',
                'role_id' => '1',
            ),
            82 => 
            array (
                'permission_id' => '85',
                'role_id' => '1',
            ),
            83 => 
            array (
                'permission_id' => '86',
                'role_id' => '1',
            ),
            84 => 
            array (
                'permission_id' => '87',
                'role_id' => '1',
            ),
            85 => 
            array (
                'permission_id' => '88',
                'role_id' => '1',
            ),
            86 => 
            array (
                'permission_id' => '89',
                'role_id' => '1',
            ),
            87 => 
            array (
                'permission_id' => '90',
                'role_id' => '1',
            ),
            88 => 
            array (
                'permission_id' => '91',
                'role_id' => '1',
            ),
            93 => 
            array (
                'permission_id' => '96',
                'role_id' => '1',
            ),
            94 => 
            array (
                'permission_id' => '97',
                'role_id' => '1',
            ),
            95 => 
            array (
                'permission_id' => '98',
                'role_id' => '1',
            ),
            96 => 
            array (
                'permission_id' => '99',
                'role_id' => '1',
            ),
            109 => 
            array (
                'permission_id' => '112',
                'role_id' => '1',
            ),
            110 => 
            array (
                'permission_id' => '113',
                'role_id' => '1',
            ),
            111 => 
            array (
                'permission_id' => '114',
                'role_id' => '1',
            ),
            112 => 
            array (
                'permission_id' => '115',
                'role_id' => '1',
            ),
            129 => 
            array (
                'permission_id' => '132',
                'role_id' => '1',
            ),
            130 => 
            array (
                'permission_id' => '133',
                'role_id' => '1',
            ),
            131 => 
            array (
                'permission_id' => '134',
                'role_id' => '1',
            ),
            132 => 
            array (
                'permission_id' => '135',
                'role_id' => '1',
            ),
            133 => 
            array (
                'permission_id' => '136',
                'role_id' => '1',
            ),
            134 => 
            array (
                'permission_id' => '137',
                'role_id' => '1',
            ),
            135 => 
            array (
                'permission_id' => '138',
                'role_id' => '1',
            ),
            136 => 
            array (
                'permission_id' => '139',
                'role_id' => '1',
            ),
            145 => 
            array (
                'permission_id' => '148',
                'role_id' => '1',
            ),
            146 => 
            array (
                'permission_id' => '149',
                'role_id' => '1',
            ),
            147 => 
            array (
                'permission_id' => '150',
                'role_id' => '1',
            ),
            148 => 
            array (
                'permission_id' => '151',
                'role_id' => '1',
            ),
            153 => 
            array (
                'permission_id' => '156',
                'role_id' => '1',
            ),
            154 => 
            array (
                'permission_id' => '157',
                'role_id' => '1',
            ),
            155 => 
            array (
                'permission_id' => '158',
                'role_id' => '1',
            ),
            156 => 
            array (
                'permission_id' => '159',
                'role_id' => '1',
            ),
            189 => 
            array (
                'permission_id' => '192',
                'role_id' => '1',
            ),
            190 => 
            array (
                'permission_id' => '193',
                'role_id' => '1',
            ),
            191 => 
            array (
                'permission_id' => '194',
                'role_id' => '1',
            ),
            192 => 
            array (
                'permission_id' => '195',
                'role_id' => '1',
            ),
            201 => 
            array (
                'permission_id' => '204',
                'role_id' => '1',
            ),
            202 => 
            array (
                'permission_id' => '205',
                'role_id' => '1',
            ),
            203 => 
            array (
                'permission_id' => '206',
                'role_id' => '1',
            ),
            204 => 
            array (
                'permission_id' => '207',
                'role_id' => '1',
            ),
            221 => 
            array (
                'permission_id' => '224',
                'role_id' => '1',
            ),
            222 => 
            array (
                'permission_id' => '225',
                'role_id' => '1',
            ),
            223 => 
            array (
                'permission_id' => '226',
                'role_id' => '1',
            ),
            224 => 
            array (
                'permission_id' => '227',
                'role_id' => '1',
            ),
            241 => 
            array (
                'permission_id' => '244',
                'role_id' => '1',
            ),
            242 => 
            array (
                'permission_id' => '245',
                'role_id' => '1',
            ),
            243 => 
            array (
                'permission_id' => '246',
                'role_id' => '1',
            ),
            244 => 
            array (
                'permission_id' => '247',
                'role_id' => '1',
            ),
            245 => 
            array (
                'permission_id' => '248',
                'role_id' => '1',
            ),
            246 => 
            array (
                'permission_id' => '249',
                'role_id' => '1',
            ),
            247 => 
            array (
                'permission_id' => '250',
                'role_id' => '1',
            ),
            248 => 
            array (
                'permission_id' => '251',
                'role_id' => '1',
            ),
            249 => 
            array (
                'permission_id' => '252',
                'role_id' => '1',
            ),
            250 => 
            array (
                'permission_id' => '253',
                'role_id' => '1',
            ),
            251 => 
            array (
                'permission_id' => '254',
                'role_id' => '1',
            ),
            252 => 
            array (
                'permission_id' => '255',
                'role_id' => '1',
            ),
            257 => 
            array (
                'permission_id' => '260',
                'role_id' => '1',
            ),
            258 => 
            array (
                'permission_id' => '261',
                'role_id' => '1',
            ),
            259 => 
            array (
                'permission_id' => '262',
                'role_id' => '1',
            ),
            260 => 
            array (
                'permission_id' => '263',
                'role_id' => '1',
            ),
            265 => 
            array (
                'permission_id' => '268',
                'role_id' => '1',
            ),
            266 => 
            array (
                'permission_id' => '269',
                'role_id' => '1',
            ),
            267 => 
            array (
                'permission_id' => '270',
                'role_id' => '1',
            ),
            268 => 
            array (
                'permission_id' => '271',
                'role_id' => '1',
            ),
            281 => 
            array (
                'permission_id' => '284',
                'role_id' => '1',
            ),
            282 => 
            array (
                'permission_id' => '285',
                'role_id' => '1',
            ),
            283 => 
            array (
                'permission_id' => '286',
                'role_id' => '1',
            ),
            284 => 
            array (
                'permission_id' => '287',
                'role_id' => '1',
            ),
            285 => 
            array (
                'permission_id' => '288',
                'role_id' => '1',
            ),
            286 => 
            array (
                'permission_id' => '289',
                'role_id' => '1',
            ),
            287 => 
            array (
                'permission_id' => '290',
                'role_id' => '1',
            ),
            288 => 
            array (
                'permission_id' => '291',
                'role_id' => '1',
            ),
            289 => 
            array (
                'permission_id' => '292',
                'role_id' => '1',
            ),
            294 => 
            array (
                'permission_id' => '297',
                'role_id' => '1',
            ),
            295 => 
            array (
                'permission_id' => '298',
                'role_id' => '1',
            ),
            296 => 
            array (
                'permission_id' => '299',
                'role_id' => '1',
            ),
            297 => 
            array (
                'permission_id' => '300',
                'role_id' => '1',
            ),
            298 => 
            array (
                'permission_id' => '301',
                'role_id' => '1',
            ),
            299 => 
            array (
                'permission_id' => '302',
                'role_id' => '1',
            ),
            300 => 
            array (
                'permission_id' => '303',
                'role_id' => '1',
            ),
            301 => 
            array (
                'permission_id' => '304',
                'role_id' => '1',
            ),
            302 => 
            array (
                'permission_id' => '305',
                'role_id' => '1',
            ),
            303 => 
            array (
                'permission_id' => '306',
                'role_id' => '1',
            ),
            304 => 
            array (
                'permission_id' => '307',
                'role_id' => '1',
            ),
            305 => 
            array (
                'permission_id' => '308',
                'role_id' => '1',
            ),
            306 => 
            array (
                'permission_id' => '309',
                'role_id' => '1',
            ),
            307 => 
            array (
                'permission_id' => '310',
                'role_id' => '1',
            ),
            319 => 
            array (
                'permission_id' => '1',
                'role_id' => '3',
            ),
            320 => 
            array (
                'permission_id' => '2',
                'role_id' => '3',
            ),
            328 => 
            array (
                'permission_id' => '25',
                'role_id' => '3',
            ),
            329 => 
            array (
                'permission_id' => '26',
                'role_id' => '3',
            ),
            332 => 
            array (
                'permission_id' => '69',
                'role_id' => '3',
            ),
            345 => 
            array (
                'permission_id' => '84',
                'role_id' => '3',
            ),
            346 => 
            array (
                'permission_id' => '85',
                'role_id' => '3',
            ),
            347 => 
            array (
                'permission_id' => '86',
                'role_id' => '3',
            ),
            349 => 
            array (
                'permission_id' => '92',
                'role_id' => '3',
            ),
            350 => 
            array (
                'permission_id' => '93',
                'role_id' => '3',
            ),
            351 => 
            array (
                'permission_id' => '94',
                'role_id' => '3',
            ),
            352 => 
            array (
                'permission_id' => '95',
                'role_id' => '3',
            ),
            353 => 
            array (
                'permission_id' => '96',
                'role_id' => '3',
            ),
            354 => 
            array (
                'permission_id' => '112',
                'role_id' => '3',
            ),
            360 => 
            array (
                'permission_id' => '132',
                'role_id' => '3',
            ),
            361 => 
            array (
                'permission_id' => '133',
                'role_id' => '3',
            ),
            362 => 
            array (
                'permission_id' => '134',
                'role_id' => '3',
            ),
            363 => 
            array (
                'permission_id' => '135',
                'role_id' => '3',
            ),
            376 => 
            array (
                'permission_id' => '148',
                'role_id' => '3',
            ),
            377 => 
            array (
                'permission_id' => '149',
                'role_id' => '3',
            ),
            378 => 
            array (
                'permission_id' => '150',
                'role_id' => '3',
            ),
            379 => 
            array (
                'permission_id' => '151',
                'role_id' => '3',
            ),
            380 => 
            array (
                'permission_id' => '244',
                'role_id' => '3',
            ),
            381 => 
            array (
                'permission_id' => '245',
                'role_id' => '3',
            ),
            382 => 
            array (
                'permission_id' => '246',
                'role_id' => '3',
            ),
            383 => 
            array (
                'permission_id' => '247',
                'role_id' => '3',
            ),
            396 => 
            array (
                'permission_id' => '248',
                'role_id' => '3',
            ),
            397 => 
            array (
                'permission_id' => '249',
                'role_id' => '3',
            ),
            398 => 
            array (
                'permission_id' => '250',
                'role_id' => '3',
            ),
            399 => 
            array (
                'permission_id' => '251',
                'role_id' => '3',
            ),
            400 => 
            array (
                'permission_id' => '252',
                'role_id' => '3',
            ),
            401 => 
            array (
                'permission_id' => '253',
                'role_id' => '3',
            ),
            402 => 
            array (
                'permission_id' => '254',
                'role_id' => '3',
            ),
            403 => 
            array (
                'permission_id' => '255',
                'role_id' => '3',
            ),
            407 => 
            array (
                'permission_id' => '259',
                'role_id' => '3',
            ),
            408 => 
            array (
                'permission_id' => '260',
                'role_id' => '3',
            ),
            409 => 
            array (
                'permission_id' => '261',
                'role_id' => '3',
            ),
            410 => 
            array (
                'permission_id' => '262',
                'role_id' => '3',
            ),
            411 => 
            array (
                'permission_id' => '263',
                'role_id' => '3',
            ),
            416 => 
            array (
                'permission_id' => '268',
                'role_id' => '3',
            ),
            417 => 
            array (
                'permission_id' => '269',
                'role_id' => '3',
            ),
            418 => 
            array (
                'permission_id' => '270',
                'role_id' => '3',
            ),
            419 => 
            array (
                'permission_id' => '271',
                'role_id' => '3',
            ),
            430 => 
            array (
                'permission_id' => '290',
                'role_id' => '3',
            ),
            431 => 
            array (
                'permission_id' => '291',
                'role_id' => '3',
            ),
            432 => 
            array (
                'permission_id' => '292',
                'role_id' => '3',
            ),
            437 => 
            array (
                'permission_id' => '305',
                'role_id' => '3',
            ),
            438 => 
            array (
                'permission_id' => '306',
                'role_id' => '3',
            ),
            439 => 
            array (
                'permission_id' => '307',
                'role_id' => '3',
            ),
            440 => 
            array (
                'permission_id' => '308',
                'role_id' => '3',
            ),
        ));
        
        
    }
}