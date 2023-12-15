<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => '1',
                'name' => 'dashboard.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:37:17',
                'updated_at' => '2022-03-08 11:37:17',
            ),
            1 => 
            array (
                'id' => '2',
                'name' => 'users.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:39:12',
                'updated_at' => '2022-03-08 11:39:12',
            ),
            2 => 
            array (
                'id' => '3',
                'name' => 'users.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:39:12',
                'updated_at' => '2022-03-08 11:39:12',
            ),
            3 => 
            array (
                'id' => '4',
                'name' => 'users.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:39:12',
                'updated_at' => '2022-03-08 11:39:12',
            ),
            4 => 
            array (
                'id' => '5',
                'name' => 'users.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:39:12',
                'updated_at' => '2022-03-08 11:39:12',
            ),
            5 => 
            array (
                'id' => '6',
                'name' => 'marketing-dashboard.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:40:34',
                'updated_at' => '2022-03-08 11:40:34',
            ),
            6 => 
            array (
                'id' => '7',
                'name' => 'categories.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:42:23',
                'updated_at' => '2022-03-08 11:42:23',
            ),
            7 => 
            array (
                'id' => '8',
                'name' => 'categories.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:42:23',
                'updated_at' => '2022-03-08 11:42:23',
            ),
            8 => 
            array (
                'id' => '9',
                'name' => 'categories.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:42:23',
                'updated_at' => '2022-03-08 11:42:23',
            ),
            9 => 
            array (
                'id' => '10',
                'name' => 'categories.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-08 11:42:23',
                'updated_at' => '2022-03-08 11:42:23',
            ),
            18 => 
            array (
                'id' => '20',
                'name' => 'affiliate.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:24:12',
                'updated_at' => '2022-03-09 05:24:12',
            ),
            // 19 => 
            // array (
            //     'id' => '21',
            //     'name' => 'wallet-setting.manage',
            //     'guard_name' => 'web',
            //     'created_at' => '2022-03-09 05:25:03',
            //     'updated_at' => '2022-03-09 05:25:03',
            // ),
            20 => 
            array (
                'id' => '22',
                'name' => 'wallet-transactions.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:26:59',
                'updated_at' => '2022-03-09 05:26:59',
            ),
            21 => 
            array (
                'id' => '23',
                'name' => 'push-notification.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:28:13',
                'updated_at' => '2022-03-09 05:28:13',
            ),
            23 => 
            array (
                'id' => '25',
                'name' => 'orders.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:29:43',
                'updated_at' => '2022-03-09 05:29:43',
            ),
            24 => 
            array (
                'id' => '26',
                'name' => 'report.quiz-report.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:30:46',
                'updated_at' => '2022-03-09 05:30:46',
            ),
            29 => 
            array (
                'id' => '32',
                'name' => 'device-history.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 05:42:07',
                'updated_at' => '2022-03-09 05:42:07',
            ),
            // 30 => 
            // array (
            //     'id' => '41',
            //     'name' => 'homepage-setting.manage',
            //     'guard_name' => 'web',
            //     'created_at' => '2022-03-09 06:59:08',
            //     'updated_at' => '2022-03-09 06:59:08',
            // ),
            43 => 
            array (
                'id' => '46',
                'name' => 'terms-condition.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:07:38',
                'updated_at' => '2022-03-09 07:07:38',
            ),
            44 => 
            array (
                'id' => '47',
                'name' => 'privacy-policy.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:08:40',
                'updated_at' => '2022-03-09 07:08:40',
            ),
            46 => 
            array (
                'id' => '49',
                'name' => 'login-signup.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:12:10',
                'updated_at' => '2022-03-09 07:12:10',
            ),
            50 => 
            array (
                'id' => '53',
                'name' => 'settings.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:15:31',
                'updated_at' => '2022-03-09 07:15:31',
            ),
            56 => 
            array (
                'id' => '59',
                'name' => 'payment-setting-credentials.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:25:19',
                'updated_at' => '2022-03-09 07:25:19',
            ),
            65 => 
            array (
                'id' => '68',
                'name' => 'review-rating.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:42:25',
                'updated_at' => '2022-03-09 07:42:25',
            ),
            70 => 
            array (
                'id' => '73',
                'name' => 'quiz-review.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 07:50:31',
                'updated_at' => '2022-03-09 07:50:31',
            ),
            73 => 
            array (
                'id' => '76',
                'name' => 'subcategories.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:41:59',
                'updated_at' => '2022-03-09 08:41:59',
            ),
            74 => 
            array (
                'id' => '77',
                'name' => 'subcategories.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:41:59',
                'updated_at' => '2022-03-09 08:41:59',
            ),
            75 => 
            array (
                'id' => '78',
                'name' => 'subcategories.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:41:59',
                'updated_at' => '2022-03-09 08:41:59',
            ),
            76 => 
            array (
                'id' => '79',
                'name' => 'subcategories.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:42:00',
                'updated_at' => '2022-03-09 08:42:00',
            ),
            77 => 
            array (
                'id' => '80',
                'name' => 'childcategories.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:44:58',
                'updated_at' => '2022-03-09 08:44:58',
            ),
            78 => 
            array (
                'id' => '81',
                'name' => 'childcategories.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:44:58',
                'updated_at' => '2022-03-09 08:44:58',
            ),
            79 => 
            array (
                'id' => '82',
                'name' => 'childcategories.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:44:58',
                'updated_at' => '2022-03-09 08:44:58',
            ),
            80 => 
            array (
                'id' => '83',
                'name' => 'childcategories.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:44:58',
                'updated_at' => '2022-03-09 08:44:58',
            ),
            81 => 
            array (
                'id' => '84',
                'name' => 'courses.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:46:10',
                'updated_at' => '2022-03-09 08:46:10',
            ),
            82 => 
            array (
                'id' => '85',
                'name' => 'courses.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:46:10',
                'updated_at' => '2022-03-09 08:46:10',
            ),
            83 => 
            array (
                'id' => '86',
                'name' => 'courses.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:46:10',
                'updated_at' => '2022-03-09 08:46:10',
            ),
            84 => 
            array (
                'id' => '87',
                'name' => 'courses.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 08:46:10',
                'updated_at' => '2022-03-09 08:46:10',
            ),
            85 => 
            array (
                'id' => '88',
                'name' => 'bundle-courses.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:17:25',
                'updated_at' => '2022-03-09 10:17:25',
            ),
            86 => 
            array (
                'id' => '89',
                'name' => 'bundle-courses.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:17:25',
                'updated_at' => '2022-03-09 10:17:25',
            ),
            87 => 
            array (
                'id' => '90',
                'name' => 'bundle-courses.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:17:25',
                'updated_at' => '2022-03-09 10:17:25',
            ),
            88 => 
            array (
                'id' => '91',
                'name' => 'bundle-courses.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:17:25',
                'updated_at' => '2022-03-09 10:17:25',
            ),
            93 => 
            array (
                'id' => '96',
                'name' => 'course-reviews.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:22:21',
                'updated_at' => '2022-03-09 10:22:21',
            ),
            94 => 
            array (
                'id' => '97',
                'name' => 'course-reviews.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:22:21',
                'updated_at' => '2022-03-09 10:22:21',
            ),
            95 => 
            array (
                'id' => '98',
                'name' => 'course-reviews.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:22:21',
                'updated_at' => '2022-03-09 10:22:21',
            ),
            96 => 
            array (
                'id' => '99',
                'name' => 'course-reviews.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:22:21',
                'updated_at' => '2022-03-09 10:22:21',
            ),
            109 => 
            array (
                'id' => '112',
                'name' => 'quiz-review.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:25:51',
                'updated_at' => '2022-03-09 10:25:51',
            ),
            110 => 
            array (
                'id' => '113',
                'name' => 'quiz-review.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:25:51',
                'updated_at' => '2022-03-09 10:25:51',
            ),
            111 => 
            array (
                'id' => '114',
                'name' => 'quiz-review.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:25:51',
                'updated_at' => '2022-03-09 10:25:51',
            ),
            112 => 
            array (
                'id' => '115',
                'name' => 'quiz-review.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:25:51',
                'updated_at' => '2022-03-09 10:25:51',
            ),
            129 => 
            array (
                'id' => '132',
                'name' => 'meetings.big-blue.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            130 => 
            array (
                'id' => '133',
                'name' => 'meetings.big-blue.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            131 => 
            array (
                'id' => '134',
                'name' => 'meetings.big-blue.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            132 => 
            array (
                'id' => '135',
                'name' => 'meetings.big-blue.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            133 => 
            array (
                'id' => '136',
                'name' => 'in-person-session.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            134 => 
            array (
                'id' => '137',
                'name' => 'in-person-session.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            135 => 
            array (
                'id' => '138',
                'name' => 'in-person-session.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            136 => 
            array (
                'id' => '139',
                'name' => 'in-person-session.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:33:35',
                'updated_at' => '2022-03-09 10:33:35',
            ),
            145 => 
            array (
                'id' => '148',
                'name' => 'meetings.meeting-recordings.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:38:47',
                'updated_at' => '2022-03-09 10:38:47',
            ),
            146 => 
            array (
                'id' => '149',
                'name' => 'meetings.meeting-recordings.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:38:47',
                'updated_at' => '2022-03-09 10:38:47',
            ),
            147 => 
            array (
                'id' => '150',
                'name' => 'meetings.meeting-recordings.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:38:47',
                'updated_at' => '2022-03-09 10:38:47',
            ),
            148 => 
            array (
                'id' => '151',
                'name' => 'meetings.meeting-recordings.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:38:47',
                'updated_at' => '2022-03-09 10:38:47',
            ),
            153 => 
            array (
                'id' => '156',
                'name' => 'coupons.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:41:32',
                'updated_at' => '2022-03-09 10:41:32',
            ),
            154 => 
            array (
                'id' => '157',
                'name' => 'coupons.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:41:32',
                'updated_at' => '2022-03-09 10:41:32',
            ),
            155 => 
            array (
                'id' => '158',
                'name' => 'coupons.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:41:33',
                'updated_at' => '2022-03-09 10:41:33',
            ),
            156 => 
            array (
                'id' => '159',
                'name' => 'coupons.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:41:33',
                'updated_at' => '2022-03-09 10:41:33',
            ),
            189 => 
            array (
                'id' => '192',
                'name' => 'currency.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:51:28',
                'updated_at' => '2022-03-09 10:51:28',
            ),
            190 => 
            array (
                'id' => '193',
                'name' => 'currency.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:51:28',
                'updated_at' => '2022-03-09 10:51:28',
            ),
            191 => 
            array (
                'id' => '194',
                'name' => 'currency.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:51:29',
                'updated_at' => '2022-03-09 10:51:29',
            ),
            192 => 
            array (
                'id' => '195',
                'name' => 'currency.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 10:51:29',
                'updated_at' => '2022-03-09 10:51:29',
            ),
            201 => 
            array (
                'id' => '204',
                'name' => 'front-settings.sliders.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:04:04',
                'updated_at' => '2022-03-09 11:04:04',
            ),
            202 => 
            array (
                'id' => '205',
                'name' => 'front-settings.sliders.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:04:04',
                'updated_at' => '2022-03-09 11:04:04',
            ),
            203 => 
            array (
                'id' => '206',
                'name' => 'front-settings.sliders.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:04:04',
                'updated_at' => '2022-03-09 11:04:04',
            ),
            204 => 
            array (
                'id' => '207',
                'name' => 'front-settings.sliders.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:04:04',
                'updated_at' => '2022-03-09 11:04:04',
            ),
            221 => 
            array (
                'id' => '224',
                'name' => 'site-settings.language.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:42:22',
                'updated_at' => '2022-03-09 11:42:22',
            ),
            222 => 
            array (
                'id' => '225',
                'name' => 'site-settings.language.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:42:22',
                'updated_at' => '2022-03-09 11:42:22',
            ),
            223 => 
            array (
                'id' => '226',
                'name' => 'site-settings.language.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:42:22',
                'updated_at' => '2022-03-09 11:42:22',
            ),
            224 => 
            array (
                'id' => '227',
                'name' => 'site-settings.language.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 11:42:22',
                'updated_at' => '2022-03-09 11:42:22',
            ),
            241 => 
            array (
                'id' => '244',
                'name' => 'what-learn.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:08:56',
                'updated_at' => '2022-03-09 12:08:56',
            ),
            242 => 
            array (
                'id' => '245',
                'name' => 'what-learn.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:08:56',
                'updated_at' => '2022-03-09 12:08:56',
            ),
            243 => 
            array (
                'id' => '246',
                'name' => 'what-learn.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:08:56',
                'updated_at' => '2022-03-09 12:08:56',
            ),
            244 => 
            array (
                'id' => '247',
                'name' => 'what-learn.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:08:56',
                'updated_at' => '2022-03-09 12:08:56',
            ),
            245 => 
            array (
                'id' => '248',
                'name' => 'course-chapter.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:10:56',
                'updated_at' => '2022-03-09 12:10:56',
            ),
            246 => 
            array (
                'id' => '249',
                'name' => 'course-chapter.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:10:56',
                'updated_at' => '2022-03-09 12:10:56',
            ),
            247 => 
            array (
                'id' => '250',
                'name' => 'course-chapter.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:10:56',
                'updated_at' => '2022-03-09 12:10:56',
            ),
            248 => 
            array (
                'id' => '251',
                'name' => 'course-chapter.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:10:56',
                'updated_at' => '2022-03-09 12:10:56',
            ),
            249 => 
            array (
                'id' => '252',
                'name' => 'course-class.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:14:27',
                'updated_at' => '2022-03-09 12:14:27',
            ),
            250 => 
            array (
                'id' => '253',
                'name' => 'course-class.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:14:27',
                'updated_at' => '2022-03-09 12:14:27',
            ),
            251 => 
            array (
                'id' => '254',
                'name' => 'course-class.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:14:27',
                'updated_at' => '2022-03-09 12:14:27',
            ),
            252 => 
            array (
                'id' => '255',
                'name' => 'course-class.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:14:28',
                'updated_at' => '2022-03-09 12:14:28',
            ),
           257 => 
            array (
                'id' => '260',
                'name' => 'question.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:15:28',
                'updated_at' => '2022-03-09 12:15:28',
            ),
            258 => 
            array (
                'id' => '261',
                'name' => 'question.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:15:28',
                'updated_at' => '2022-03-09 12:15:28',
            ),
            259 => 
            array (
                'id' => '262',
                'name' => 'question.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:15:29',
                'updated_at' => '2022-03-09 12:15:29',
            ),
            260 => 
            array (
                'id' => '263',
                'name' => 'question.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:15:29',
                'updated_at' => '2022-03-09 12:15:29',
            ),
            265 => 
            array (
                'id' => '268',
                'name' => 'quiz-topic.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:20:22',
                'updated_at' => '2022-03-09 12:20:22',
            ),
            266 => 
            array (
                'id' => '269',
                'name' => 'quiz-topic.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:20:22',
                'updated_at' => '2022-03-09 12:20:22',
            ),
            267 => 
            array (
                'id' => '270',
                'name' => 'quiz-topic.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:20:22',
                'updated_at' => '2022-03-09 12:20:22',
            ),
            268 => 
            array (
                'id' => '271',
                'name' => 'quiz-topic.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:20:22',
                'updated_at' => '2022-03-09 12:20:22',
            ),
            281 => 
            array (
                'id' => '284',
                'name' => 'role.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:25:00',
                'updated_at' => '2022-03-09 12:25:00',
            ),
            282 => 
            array (
                'id' => '285',
                'name' => 'role.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:25:00',
                'updated_at' => '2022-03-09 12:25:00',
            ),
            283 => 
            array (
                'id' => '286',
                'name' => 'role.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:25:00',
                'updated_at' => '2022-03-09 12:25:00',
            ),
            284 => 
            array (
                'id' => '287',
                'name' => 'role.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-09 12:25:00',
                'updated_at' => '2022-03-09 12:25:00',
            ),
            287 => 
            array (
                'id' => '290',
                'name' => 'meetings.big-blue.settings',
                'guard_name' => 'web',
                'created_at' => '2022-03-10 09:56:00',
                'updated_at' => '2022-03-10 09:56:00',
            ),
            288 => 
            array (
                'id' => '291',
                'name' => 'meetings.big-blue.list-meetings',
                'guard_name' => 'web',
                'created_at' => '2022-03-10 09:59:09',
                'updated_at' => '2022-03-10 09:59:09',
            ),
            289 => 
            array (
                'id' => '292',
                'name' => 'meetings.big-blue.recorded',
                'guard_name' => 'web',
                'created_at' => '2022-03-10 10:00:28',
                'updated_at' => '2022-03-10 10:00:28',
            ),
           294 => 
            array (
                'id' => '297',
                'name' => 'Allinstructor.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 09:38:46',
                'updated_at' => '2022-03-14 09:38:46',
            ),
            295 => 
            array (
                'id' => '298',
                'name' => 'Allinstructor.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 09:38:46',
                'updated_at' => '2022-03-14 09:38:46',
            ),
            296 => 
            array (
                'id' => '299',
                'name' => 'Allinstructor.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 09:38:46',
                'updated_at' => '2022-03-14 09:38:46',
            ),
            297 => 
            array (
                'id' => '300',
                'name' => 'Allinstructor.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 09:38:46',
                'updated_at' => '2022-03-14 09:38:46',
            ),
            298 => 
            array (
                'id' => '301',
                'name' => 'Alluser.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 10:39:10',
                'updated_at' => '2022-03-14 10:39:10',
            ),
            299 => 
            array (
                'id' => '302',
                'name' => 'Alluser.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 10:39:10',
                'updated_at' => '2022-03-14 10:39:10',
            ),
            300 => 
            array (
                'id' => '303',
                'name' => 'Alluser.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 10:39:10',
                'updated_at' => '2022-03-14 10:39:10',
            ),
            301 => 
            array (
                'id' => '304',
                'name' => 'Alluser.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 10:39:10',
                'updated_at' => '2022-03-14 10:39:10',
            ),
            302 => 
            array (
                'id' => '305',
                'name' => 'answer.view',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:43',
                'updated_at' => '2022-03-14 13:05:43',
            ),
            303 => 
            array (
                'id' => '306',
                'name' => 'answer.create',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:44',
                'updated_at' => '2022-03-14 13:05:44',
            ),
            304 => 
            array (
                'id' => '307',
                'name' => 'answer.edit',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:44',
                'updated_at' => '2022-03-14 13:05:44',
            ),
            305 => 
            array (
                'id' => '308',
                'name' => 'answer.delete',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:44',
                'updated_at' => '2022-03-14 13:05:44',
            ),
            306 => 
            array (
                'id' => '309',
                'name' => 'blocked-users.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:44',
                'updated_at' => '2022-03-14 13:05:44',
            ),
            307 => 
            array (
                'id' => '310',
                'name' => 'payment-charges.manage',
                'guard_name' => 'web',
                'created_at' => '2022-03-14 13:05:44',
                'updated_at' => '2022-03-14 13:05:44',
            ),
        ));
        
        
    }
}