<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the databaseAL seeds.
     *
     * @return void
     */
    public function run()
    {


        $countryArray = [
            ['country_code' => 'AE', 'name' => 'UNITED ARAB EMIRATES', 'code' => '971', 'currency' => 'United Arab Emirates Dirham', 'currency_code' => 'AED','flags'=>'/backend/images/flag-icons/ae.png'],

            ['country_code' => 'AF', 'name' => 'AFGHANISTAN', 'code' => '93', 'currency' => 'Afghanistan Afghani', 'currency_code' => 'AFN','flags'=>'/backend/images/flag-icons/af.png'],

            ['country_code' => 'AN', 'name' => 'NETHERLANDS ANTILLES', 'code' => '599', 'currency' => 'Netherlands Antillean guilder', 'currency_code' => 'ANG','flags'=>'/backend/images/flag-icons/am.png'],

            ['country_code' => 'AR', 'name' => 'ARGENTINA', 'code' => '54', 'currency' => 'Argentine peso', 'currency_code' => 'ARS','flags'=>'/backend/images/flag-icons/ar.png'],

            ['country_code' => 'AT', 'name' => 'AUSTRIA', 'code' => '43', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/at.png'],

            ['country_code' => 'AU', 'name' => 'AUSTRALIA', 'code' => '61', 'currency' => 'Australian dollar', 'currency_code' => 'AUD','flags'=>'/backend/images/flag-icons/au.png'],

            ['country_code' => 'BD', 'name' => 'BANGLADESH', 'code' => '880', 'currency' => 'Bangladeshi taka', 'currency_code' => 'BDT','flags'=>'/backend/images/flag-icons/bd.png'],

            ['country_code' => 'BE', 'name' => 'BELGIUM', 'code' => '32', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/be.png'],

            ['country_code' => 'BH', 'name' => 'BAHRAIN', 'code' => '973', 'currency' => 'Bahraini dinar', 'currency_code' => 'BHD','flags'=>'/backend/images/flag-icons/bh.png'],

            ['country_code' => 'BR', 'name' => 'BRAZIL', 'code' => '55', 'currency' => 'Brazilian real', 'currency_code' => 'BRL','flags'=>'/backend/images/flag-icons/br.png'],

            ['country_code' => 'BT', 'name' => 'BHUTAN', 'code' => '975', 'currency' => 'Bhutanese ngultrum', 'currency_code' => 'BTN','flags'=>'/backend/images/flag-icons/bt.png'],

            ['country_code' => 'CA', 'name' => 'CANADA', 'code' => '1', 'currency' => 'Canadian dollar', 'currency_code' => 'CAD','flags'=>'/backend/images/flag-icons/ca.png'],

            ['country_code' => 'CG', 'name' => 'CONGO', 'code' => '242', 'currency' => 'Central African CFA franc', 'currency_code' => 'XAF','flags'=>'/backend/images/flag-icons/cg.png'],

            ['country_code' => 'CH', 'name' => 'SWITZERLAND', 'code' => '41', 'currency' => 'wiss franc', 'currency_code' => 'CHF','flags'=>'/backend/images/flag-icons/ch.png'],

            ['country_code' => 'CL', 'name' => 'CHILE', 'code' => '56', 'currency' => 'Chilean peso', 'currency_code' => 'CLP','flags'=>'/backend/images/flag-icons/cl.png'],

            ['country_code' => 'CN', 'name' => 'CHINA', 'code' => '86', 'currency' => 'Renminbi|Chinese yuan', 'currency_code' => 'CNY','flags'=>'/backend/images/flag-icons/cn.png'],

            ['country_code' => 'CO', 'name' => 'COLOMBIA', 'code' => '57', 'currency' => 'Colombian peso', 'currency_code' => 'COP','flags'=>'/backend/images/flag-icons/co.png'],

            ['country_code' => 'CR', 'name' => 'COSTA RICA', 'code' => '506', 'currency' => 'Costa Rican colon', 'currency_code' => 'CRC','flags'=>'/backend/images/flag-icons/cr.png'],

            ['country_code' => 'CU', 'name' => 'CUBA', 'code' => '53', 'currency' => 'Cuban peso', 'currency_code' => 'CUP','flags'=>'/backend/images/flag-icons/cu.png'],

            ['country_code' => 'CZ', 'name' => 'CZECH REPUBLIC', 'code' => '420', 'currency' => 'Czech koruna', 'currency_code' => 'CZK','flags'=>'/backend/images/flag-icons/cz.png'],

            ['country_code' => 'DE', 'name' => 'GERMANY', 'code' => '49', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/de.png'],

            ['country_code' => 'DK', 'name' => 'DENMARK', 'code' => '45', 'currency' => 'Danish krone', 'currency_code' => 'DKK','flags'=>'/backend/images/flag-icons/dk.png'],

            ['country_code' => 'EC', 'name' => 'ECUADOR', 'code' => '593', 'currency' => 'Ecuadorian sucre', 'currency_code' => 'ECS','flags'=>'/backend/images/flag-icons/ec.png'],

            ['country_code' => 'ES', 'name' => 'SPAIN', 'code' => '34', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/es.png'],

            ['country_code' => 'FI', 'name' => 'FINLAND', 'code' => '358', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/fi.png'],

            ['country_code' => 'FR', 'name' => 'FRANCE', 'code' => '33', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/fr.png'],

            ['country_code' => 'GB', 'name' => 'UNITED KINGDOM', 'code' => '44', 'currency' => 'Pound sterling', 'currency_code' => 'GBP','flags'=>'/backend/images/flag-icons/gb.png'],

            ['country_code' => 'HK', 'name' => 'HONG KONG', 'code' => '852', 'currency' => 'Hong Kong dollar', 'currency_code' => 'HKD','flags'=>'/backend/images/flag-icons/hn.png'],

            ['country_code' => 'IN', 'name' => 'INDIA', 'code' => '91', 'currency' => 'Indian rupee', 'currency_code' => 'INR','flags'=>'/backend/images/flag-icons/in.png'],

            ['country_code' => 'IQ', 'name' => 'IRAQ', 'code' => '964', 'currency' => 'Iraqi dinar', 'currency_code' => 'IQD','flags'=>'/backend/images/flag-icons/iq.png'],

            ['country_code' => 'JP', 'name' => 'JAPAN', 'code' => '81', 'currency' => 'Japan Yen', 'currency_code' => 'JPY','flags'=>'/backend/images/flag-icons/jp.png'],

            ['country_code' => 'KW', 'name' => 'KUWAIT', 'code' => '965', 'currency' => 'Kuwaiti dinar', 'currency_code' => 'KWD','flags'=>'/backend/images/flag-icons/kw.png'],

            ['country_code' => 'LK', 'name' => 'SRI LANKA', 'code' => '94', 'currency' => 'Sri Lanka Rupee', 'currency_code' => 'LKR','flags'=>'/backend/images/flag-icons/lk.png'],

            ['country_code' => 'MY', 'name' => 'MALAYSIA', 'code' => '60', 'currency' => 'Malaysia Ringgit', 'currency_code' => 'MYR','flags'=>'/backend/images/flag-icons/my.png'],

            ['country_code' => 'NL', 'name' => 'NETHERLANDS', 'code' => '31', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/nl.png'],

            ['country_code' => 'NP', 'name' => 'NEPAL', 'code' => '977', 'currency' => 'Nepal Rupee', 'currency_code' => 'NPR','flags'=>'/backend/images/flag-icons/np.png'],

            ['country_code' => 'NZ', 'name' => 'NEW ZEALAND', 'code' => '64', 'currency' => 'New Zealand dollar', 'currency_code' => 'NZD','flags'=>'/backend/images/flag-icons/nz.png'],

            ['country_code' => 'OM', 'name' => 'OMAN', 'code' => '968', 'currency' => 'Omani rial', 'currency_code' => 'OMR','flags'=>'/backend/images/flag-icons/om.png'],

            ['country_code' => 'PH', 'name' => 'PHILIPPINES', 'code' => '63', 'currency' => 'Philippines Peso', 'currency_code' => 'PHP','flags'=>'/backend/images/flag-icons/ph.png'],

            ['country_code' => 'PK', 'name' => 'PAKISTAN', 'code' => '92', 'currency' => 'Pakistani rupee', 'currency_code' => 'PKR','flags'=>'/backend/images/flag-icons/pk.png'],

            ['country_code' => 'PL', 'name' => 'POLAND', 'code' => '48', 'currency' => 'Polish złoty', 'currency_code' => 'PLN','flags'=>'/backend/images/flag-icons/pl.png'],

            ['country_code' => 'PT', 'name' => 'PORTUGAL', 'code' => '351', 'currency' => 'Euro', 'currency_code' => 'EUR','flags'=>'/backend/images/flag-icons/pt.png'],

            ['country_code' => 'QA', 'name' => 'QATAR', 'code' => '974', 'currency' => 'Qatar Riyal', 'currency_code' => 'QAR','flags'=>'/backend/images/flag-icons/qa.png'],

            ['country_code' => 'RU', 'name' => 'RUSSIAN FEDERATION', 'code' => '7', 'currency' => 'Russia Ruble', 'currency_code' => 'RUB','flags'=>'/backend/images/flag-icons/ru.png'],

            ['country_code' => 'SA', 'name' => 'SAUDI ARABIA', 'code' => '966', 'currency' => 'Saudi Arabia Riyal', 'currency_code' => 'SAR','flags'=>'/backend/images/flag-icons/sa.png'],

            ['country_code' => 'SG', 'name' => 'SINGAPORE', 'code' => '65', 'currency' => 'Singapore Dollar', 'currency_code' => 'SGD','flags'=>'/backend/images/flag-icons/sg.png'],

            ['country_code' => 'TR', 'name' => 'TURKEY', 'code' => '90', 'currency' => 'Turkey Lira', 'currency_code' => 'TRL','flags'=>'/backend/images/flag-icons/tr.png'],
            
            ['country_code' => 'US', 'name' => 'UNITED STATES', 'code' => '1', 'currency' => 'United States dollar', 'currency_code' => 'USD','flags'=>'/backend/images/flag-icons/us.png'],

            ['country_code' => 'YE', 'name' => 'YEMEN', 'code' => '967', 'currency' => 'Yemeni rial', 'currency_code' => 'YER','flags'=>'/backend/images/flag-icons/ye.png'],

            ['country_code' => 'ZA', 'name' => 'SOUTH AFRICA', 'code' => '27', 'currency' => 'South African rand', 'currency_code' => 'ZAR','flags'=>'/backend/images/flag-icons/za.png'],
        ];


        \App\Models\Country::truncate();
        foreach ($countryArray as $index => $country) {
            $country_obj = new \App\Models\Country();
            $country_obj->name = $country['name'];
            $country_obj->country_code = $country['country_code'];
            $country_obj->code = $country['code'];
            $country_obj->currency = $country['currency'];
            $country_obj->currency_code = $country['currency_code'];
            $country_obj->flags = $country['flags'];
			$country_obj->status = 1;
            $country_obj->save();
        }

    }
}