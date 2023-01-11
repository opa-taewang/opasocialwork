<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            "id" => 35,
            "name" => "app_name",
            "value" => "OpaSocial",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 36,
            "name" => "currency_symbol",
            "value" => "$",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 37,
            "name" => "currency_code",
            "value" => "USD",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 38,
            "name" => "logo",
            "value" => "images\/4WXX17bDnrrcMMJbKv6m1uU4zoyNEXeKrFoxML3Z.png",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 39,
            "name" => "date_format",
            "value" => "d-m-Y",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 40,
            "name" => "banner",
            "value" => "images\/99d575092d9e0fd3a1ad35b091660b3e.png",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 41,
            "name" => "home_page_description",
            "value" => "We are the Cheapest Social media marketing website and 100% High Quality for all social networks. Get the best social media services today!",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 42,
            "name" => "recaptcha_public_key",
            "value" => "6LdPeMYeAAAAAKWK0Vu37QpJXgBwA69iKy7_gCYY",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 43,
            "name" => "recaptcha_private_key",
            "value" => "6LdPeMYeAAAAAEzIYJU_TNMYzvkuQdiVdLo60hgJ",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 44,
            "name" => "minimum_deposit_amount",
            "value" => "1000",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 45,
            "name" => "home_page_meta",
            "value" => "<meta name=\"description\" content=\"The number #1 social media growing channel in Africa\">\r\n<meta name=\"keywords\" content=\"smm, social, social media marketing in nigeria, nigeria, africa, smm panel, smm panel in africa, smm panel in nigeria \">\r\n",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 46,
            "name" => "module_api_enabled",
            "value" => "1",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 47,
            "name" => "module_support_enabled",
            "value" => "1",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 48,
            "name" => "theme_color",
            "value" => "#33691E",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 49,
            "name" => "background_color",
            "value" => "#e9ebee",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 50,
            "name" => "language",
            "value" => "en",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 51,
            "name" => "display_price_per",
            "value" => "1000",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 52,
            "name" => "admin_note",
            "value" => "All services working perfectly&nbsp;",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 53,
            "name" => "admin_layout",
            "value" => "container-fluid",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 54,
            "name" => "user_layout",
            "value" => "container",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 55,
            "name" => "panel_theme",
            "value" => "material",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 56,
            "name" => "anonymizer",
            "value" => "http:\/\/anonym.to\/?",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 57,
            "name" => "front_page",
            "value" => "home",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 58,
            "name" => "show_service_list_without_login",
            "value" => "YES",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 59,
            "name" => "notify_email",
            "value" => "info@opasocial.com",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 60,
            "name" => "currency_separator",
            "value" => ".",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 61,
            "name" => "app_key",
            "value" => '$2y$10$L2ebBUbNqNasrvPiyf3h\/.oppFQZMP.xKe.ojJUoMztPOSezNPrmC',
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 62,
            "name" => "app_code",
            "value" => '$2y$10$MuhwAYxufze.gdXRGfmD1.M60lWVzpqtIhtq9TuFIE2al9c3TygRm',
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 63,
            "name" => "Script Developed and Provided By",
            "value" => "OPASOCIAL.COM",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 64,
            "name" => "Contact OPA SCEPTRE LTD",
            "value" => "+2348154113038",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 65,
            "name" => "module_subscription_enabled",
            "value" => "1",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 66,
            "name" => "timezone",
            "value" => "Africa\/Lagos",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 67,
            "name" => "navbar_name",
            "value" => "OPASOCIAL",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 68,
            "name" => "use_color",
            "value" => "0",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 69,
            "name" => "popupnotification",
            "value" => "<p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">Hello there,<\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\"><b>We Can provide or develop any features, Payment gateway, designs for your SMM panel.&nbsp;<\/b><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\"><b>Check Our Available Payment Gateways <a href=\"https:\/\/smm-script.com\/services\/payment-gateways\" title=\"\" target=\"\">here<\/a>.<\/b><\/p><p style=\"margin-bottom: 1em; font-size: 15px; color: rgb(73, 80, 87); font-family: Lato, sans-serif;\"><span style=\"font-weight: 700;\">Check Our Available Features <a href=\"https:\/\/smm-script.com\/services\/additional-features\" title=\"\" target=\"\">here<\/a>.<\/span><\/p><p style=\"margin-bottom: 1em; font-size: 15px; color: rgb(73, 80, 87); font-family: Lato, sans-serif;\"><span style=\"font-weight: 700;\">MXZ Pro V2 is available Now which has much features including auto features like blocking a Maliciuos Users IP and many more.&nbsp; Check Out demo&nbsp;<a href=\"https:\/\/demo.mxzpro.com\/\" title=\"\" target=\"\">here<\/a>.&nbsp;<\/span><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">Thanks and Best Regards,<br><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">MXZ Team!<\/p>",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 70,
            "name" => "accountstatus",
            "value" => "<p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">Hello there,<\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\"><b>We Can provide or develop any features, Payment gateway, designs for your SMM panel.&nbsp;<\/b><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\"><b>Check Our Available Payment Gateways <a href=\"https:\/\/smm-script.com\/services\/payment-gateways\" title=\"\" target=\"\">here<\/a>.<\/b><\/p><p style=\"margin-bottom: 1em; font-size: 15px; color: rgb(73, 80, 87); font-family: Lato, sans-serif;\"><span style=\"font-weight: 700;\">Check Our Available Features <a href=\"https:\/\/smm-script.com\/services\/additional-features\" title=\"\" target=\"\">here<\/a>.<\/span><\/p><p style=\"margin-bottom: 1em; font-size: 15px; color: rgb(73, 80, 87); font-family: Lato, sans-serif;\"><span style=\"font-weight: 700;\">MXZ Pro V2 is available Now which has much features including auto features like blocking a Maliciuos Users IP and many more.&nbsp; Check Out demo&nbsp;<a href=\"https:\/\/demo.mxzpro.com\/\" title=\"\" target=\"\">here<\/a>.&nbsp;<\/span><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">Thanks and Best Regards,<br><\/p><p style=\"margin-bottom: 1em; color: rgb(73, 80, 87); font-family: Lato, sans-serif; font-size: 15px;\">MXZ Team!<\/p>",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 71,
            "name" => "profit_percentage",
            "value" => "100",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 72,
            "name" => "api_id",
            "value" => "2",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 85,
            "name" => "smm_api_key",
            "value" => "E5UDqsBOZdXI1TSNjODo9e0Nl5z6Y0qNuC6ZSgnPF9PjQqM2DWmlRG2BfFEUd2Bc",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 86,
            "name" => "child_panel",
            "value" => "on",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 87,
            "name" => "child_panel_price",
            "value" => "15",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 170,
            "name" => "child_panel_buyer",
            "value" => "admin",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 171,
            "name" => "users_per_ip",
            "value" => "10",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 172,
            "name" => "usdtongn",
            "value" => "572",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 173,
            "name" => "only_login",
            "value" => "1",
            "created_at" => now(),
            "updated_at" => now()
        ]);

        DB::table('configs')->insert([
            "id" => 174,
            "name" => "notice_error",
            "value" => "This Panel Doesn't Have valid License.",
            "created_at" => now(),
            "updated_at" => now()
        ]);
    }
}
