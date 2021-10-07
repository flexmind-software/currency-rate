<?php

namespace FlexMindSoftware\CurrencyRate\Models;

use Exception;
use Illuminate\Support\Arr;

class Country
{
    /**
     * @var array
     */
    public static array $countries = [

        'BD' =>
            [
                'name' => "Bangladesh",
                'iso3' => "BGD",
                'capital' => "Dhaka",
                'phone' => "880",
                'currency' => "BDT",
            ],
        'BE' =>
            [
                'name' => "Belgium",
                'iso3' => "BEL",
                'capital' => "Brussels",
                'phone' => "32",
                'currency' => Currency::CUR_EUR,
            ],
        'BF' =>
            [
                'name' => "Burkina Faso",
                'iso3' => "BFA",
                'capital' => "Ouagadougou",
                'phone' => "226",
                'currency' => Currency::CUR_XOF,
            ],
        'BG' =>
            [
                'name' => "Bulgaria",
                'iso3' => "BGR",
                'capital' => "Sofia",
                'phone' => "359",
                'currency' => Currency::CUR_BGN,
            ],
        'BA' =>
            [
                'name' => "Bosnia and Herzegovina",
                'iso3' => "BIH",
                'capital' => "Sarajevo",
                'phone' => "387",
                'currency' => Currency::CUR_BAM,
            ],
        'BB' =>
            [
                'name' => "Barbados",
                'iso3' => "BRB",
                'capital' => "Bridgetown",
                'phone' => "+1-246",
                'currency' => "BBD",
            ],
        'WF' =>
            [
                'name' => "Wallis and Futuna",
                'iso3' => "WLF",
                'capital' => "Mata Utu",
                'phone' => "681",
                'currency' => "XPF",
            ],
        'BL' =>
            [
                'name' => "Saint Barthelemy",
                'iso3' => "BLM",
                'capital' => "Gustavia",
                'phone' => "590",
                'currency' => Currency::CUR_EUR,
            ],
        'BM' =>
            [
                'name' => "Bermuda",
                'iso3' => "BMU",
                'capital' => "Hamilton",
                'phone' => "+1-441",
                'currency' => "BMD",
            ],
        'BN' =>
            [
                'name' => "Brunei",
                'iso3' => "BRN",
                'capital' => "Bandar Seri Begawan",
                'phone' => "673",
                'currency' => "BND",
            ],
        'BO' =>
            [
                'name' => "Bolivia",
                'iso3' => "BOL",
                'capital' => "Sucre",
                'phone' => "591",
                'currency' => "BOB",
            ],
        'BH' =>
            [
                'name' => "Bahrain",
                'iso3' => "BHR",
                'capital' => "Manama",
                'phone' => "973",
                'currency' => "BHD",
            ],
        'BI' =>
            [
                'name' => "Burundi",
                'iso3' => "BDI",
                'capital' => "Bujumbura",
                'phone' => "257",
                'currency' => "BIF",
            ],
        'BJ' =>
            [
                'name' => "Benin",
                'iso3' => "BEN",
                'capital' => "Porto-Novo",
                'phone' => "229",
                'currency' => Currency::CUR_XOF,
            ],
        'BT' =>
            [
                'name' => "Bhutan",
                'iso3' => "BTN",
                'capital' => "Thimphu",
                'phone' => "975",
                'currency' => "BTN",
            ],
        'JM' =>
            [
                'name' => "Jamaica",
                'iso3' => "JAM",
                'capital' => "Kingston",
                'phone' => "+1-876",
                'currency' => "JMD",
            ],
        'BV' =>
            [
                'name' => "Bouvet Island",
                'iso3' => "BVT",
                'capital' => "",
                'phone' => "",
                'currency' => Currency::CUR_NOK,
            ],
        'BW' =>
            [
                'name' => "Botswana",
                'iso3' => "BWA",
                'capital' => "Gaborone",
                'phone' => "267",
                'currency' => Currency::CUR_BWP,
            ],
        'WS' =>
            [
                'name' => "Samoa",
                'iso3' => "WSM",
                'capital' => "Apia",
                'phone' => "685",
                'currency' => "WST",
            ],
        'BQ' =>
            [
                'name' => "Bonaire, Saint Eustatius and Saba",
                'iso3' => "BES",
                'capital' => "",
                'phone' => "599",
                'currency' => Currency::CUR_USD,
            ],
        'BR' =>
            [
                'name' => "Brazil",
                'iso3' => "BRA",
                'capital' => "Brasilia",
                'phone' => "55",
                'currency' => Currency::CUR_BRL,
            ],
        'BS' =>
            [
                'name' => "Bahamas",
                'iso3' => "BHS",
                'capital' => "Nassau",
                'phone' => "+1-242",
                'currency' => "BSD",
            ],
        'JE' =>
            [
                'name' => "Jersey",
                'iso3' => "JEY",
                'capital' => "Saint Helier",
                'phone' => "+44-1534",
                'currency' => Currency::CUR_GBP,
            ],
        'BY' =>
            [
                'name' => "Belarus",
                'iso3' => "BLR",
                'capital' => "Minsk",
                'phone' => "375",
                'currency' => Currency::CUR_BYN,
            ],
        'BZ' =>
            [
                'name' => "Belize",
                'iso3' => "BLZ",
                'capital' => "Belmopan",
                'phone' => "501",
                'currency' => "BZD",
            ],
        'RU' =>
            [
                'name' => "Russia",
                'iso3' => "RUS",
                'capital' => "Moscow",
                'phone' => "7",
                'currency' => Currency::CUR_RUB,
            ],
        'RW' =>
            [
                'name' => "Rwanda",
                'iso3' => "RWA",
                'capital' => "Kigali",
                'phone' => "250",
                'currency' => "RWF",
            ],
        'RS' =>
            [
                'name' => "Serbia",
                'iso3' => "SRB",
                'capital' => "Belgrade",
                'phone' => "381",
                'currency' => Currency::CUR_RSD,
            ],
        'TL' =>
            [
                'name' => "East Timor",
                'iso3' => "TLS",
                'capital' => "Dili",
                'phone' => "670",
                'currency' => Currency::CUR_USD,
            ],
        'RE' =>
            [
                'name' => "Reunion",
                'iso3' => "REU",
                'capital' => "Saint-Denis",
                'phone' => "262",
                'currency' => Currency::CUR_EUR,
            ],
        'TM' =>
            [
                'name' => "Turkmenistan",
                'iso3' => "TKM",
                'capital' => "Ashgabat",
                'phone' => "993",
                'currency' => "TMT",
            ],
        'TJ' =>
            [
                'name' => "Tajikistan",
                'iso3' => "TJK",
                'capital' => "Dushanbe",
                'phone' => "992",
                'currency' => "TJS",
            ],
        'RO' =>
            [
                'name' => "Romania",
                'iso3' => "ROU",
                'capital' => "Bucharest",
                'phone' => "40",
                'currency' => Currency::CUR_RON,
            ],
        'TK' =>
            [
                'name' => "Tokelau",
                'iso3' => "TKL",
                'capital' => "",
                'phone' => "690",
                'currency' => Currency::CUR_NZD,
            ],
        'GW' =>
            [
                'name' => "Guinea-Bissau",
                'iso3' => "GNB",
                'capital' => "Bissau",
                'phone' => "245",
                'currency' => Currency::CUR_XOF,
            ],
        'GU' =>
            [
                'name' => "Guam",
                'iso3' => "GUM",
                'capital' => "Hagatna",
                'phone' => "+1-671",
                'currency' => Currency::CUR_USD,
            ],
        'GT' =>
            [
                'name' => "Guatemala",
                'iso3' => "GTM",
                'capital' => "Guatemala City",
                'phone' => "502",
                'currency' => "GTQ",
            ],
        'GS' =>
            [
                'name' => "South Georgia and the South Sandwich Islands",
                'iso3' => "SGS",
                'capital' => "Grytviken",
                'phone' => "",
                'currency' => Currency::CUR_GBP,
            ],
        'GR' =>
            [
                'name' => "Greece",
                'iso3' => "GRC",
                'capital' => "Athens",
                'phone' => "30",
                'currency' => Currency::CUR_EUR,
            ],
        'GQ' =>
            [
                'name' => "Equatorial Guinea",
                'iso3' => "GNQ",
                'capital' => "Malabo",
                'phone' => "240",
                'currency' => "XAF",
            ],
        'GP' =>
            [
                'name' => "Guadeloupe",
                'iso3' => "GLP",
                'capital' => "Basse-Terre",
                'phone' => "590",
                'currency' => Currency::CUR_EUR,
            ],
        'JP' =>
            [
                'name' => "Japan",
                'iso3' => "JPN",
                'capital' => "Tokyo",
                'phone' => "81",
                'currency' => Currency::CUR_JPY,
            ],
        'GY' =>
            [
                'name' => "Guyana",
                'iso3' => "GUY",
                'capital' => "Georgetown",
                'phone' => "592",
                'currency' => "GYD",
            ],
        'GG' =>
            [
                'name' => "Guernsey",
                'iso3' => "GGY",
                'capital' => "St Peter Port",
                'phone' => "+44-1481",
                'currency' => Currency::CUR_GBP,
            ],
        'GF' =>
            [
                'name' => "French Guiana",
                'iso3' => "GUF",
                'capital' => "Cayenne",
                'phone' => "594",
                'currency' => Currency::CUR_EUR,
            ],
        'GE' =>
            [
                'name' => "Georgia",
                'iso3' => "GEO",
                'capital' => "Tbilisi",
                'phone' => "995",
                'currency' => Currency::CUR_GEL,
            ],
        'GD' =>
            [
                'name' => "Grenada",
                'iso3' => "GRD",
                'capital' => "St. George's",
                'phone' => "+1-473",
                'currency' => "XCD",
            ],
        'GB' =>
            [
                'name' => "United Kingdom",
                'iso3' => "GBR",
                'capital' => "London",
                'phone' => "44",
                'currency' => Currency::CUR_GBP,
            ],
        'GA' =>
            [
                'name' => "Gabon",
                'iso3' => "GAB",
                'capital' => "Libreville",
                'phone' => "241",
                'currency' => "XAF",
            ],
        'SV' =>
            [
                'name' => "El Salvador",
                'iso3' => "SLV",
                'capital' => "San Salvador",
                'phone' => "503",
                'currency' => Currency::CUR_USD,
            ],
        'GN' =>
            [
                'name' => "Guinea",
                'iso3' => "GIN",
                'capital' => "Conakry",
                'phone' => "224",
                'currency' => "GNF",
            ],
        'GM' =>
            [
                'name' => "Gambia",
                'iso3' => "GMB",
                'capital' => "Banjul",
                'phone' => "220",
                'currency' => "GMD",
            ],
        'GL' =>
            [
                'name' => "Greenland",
                'iso3' => "GRL",
                'capital' => "Nuuk",
                'phone' => "299",
                'currency' => Currency::CUR_DKK,
            ],
        'GI' =>
            [
                'name' => "Gibraltar",
                'iso3' => "GIB",
                'capital' => "Gibraltar",
                'phone' => "350",
                'currency' => "GIP",
            ],
        'GH' =>
            [
                'name' => "Ghana",
                'iso3' => "GHA",
                'capital' => "Accra",
                'phone' => "233",
                'currency' => "GHS",
            ],
        'OM' =>
            [
                'name' => "Oman",
                'iso3' => "OMN",
                'capital' => "Muscat",
                'phone' => "968",
                'currency' => "OMR",
            ],
        'TN' =>
            [
                'name' => "Tunisia",
                'iso3' => "TUN",
                'capital' => "Tunis",
                'phone' => "216",
                'currency' => "TND",
            ],
        'JO' =>
            [
                'name' => "Jordan",
                'iso3' => "JOR",
                'capital' => "Amman",
                'phone' => "962",
                'currency' => "JOD",
            ],
        'HR' =>
            [
                'name' => "Croatia",
                'iso3' => "HRV",
                'capital' => "Zagreb",
                'phone' => "385",
                'currency' => Currency::CUR_HRK,
            ],
        'HT' =>
            [
                'name' => "Haiti",
                'iso3' => "HTI",
                'capital' => "Port-au-Prince",
                'phone' => "509",
                'currency' => "HTG",
            ],
        'HU' =>
            [
                'name' => "Hungary",
                'iso3' => "HUN",
                'capital' => "Budapest",
                'phone' => "36",
                'currency' => Currency::CUR_HUF,
            ],
        'HK' =>
            [
                'name' => "Hong Kong",
                'iso3' => "HKG",
                'capital' => "Hong Kong",
                'phone' => "852",
                'currency' => Currency::CUR_HKD,
            ],
        'HN' =>
            [
                'name' => "Honduras",
                'iso3' => "HND",
                'capital' => "Tegucigalpa",
                'phone' => "504",
                'currency' => "HNL",
            ],
        'HM' =>
            [
                'name' => "Heard Island and McDonald Islands",
                'iso3' => "HMD",
                'capital' => "",
                'phone' => "",
                'currency' => Currency::CUR_AUD,
            ],
        'VE' =>
            [
                'name' => "Venezuela",
                'iso3' => "VEN",
                'capital' => "Caracas",
                'phone' => "58",
                'currency' => "VEF",
            ],
        'PR' =>
            [
                'name' => "Puerto Rico",
                'iso3' => "PRI",
                'capital' => "San Juan",
                'phone' => "+1-787 and 1-939",
                'currency' => Currency::CUR_USD,
            ],
        'PS' =>
            [
                'name' => "Palestinian Territory",
                'iso3' => "PSE",
                'capital' => "East Jerusalem",
                'phone' => "970",
                'currency' => Currency::CUR_ILS,
            ],
        'PW' =>
            [
                'name' => "Palau",
                'iso3' => "PLW",
                'capital' => "Melekeok",
                'phone' => "680",
                'currency' => Currency::CUR_USD,
            ],
        'PT' =>
            [
                'name' => "Portugal",
                'iso3' => "PRT",
                'capital' => "Lisbon",
                'phone' => "351",
                'currency' => Currency::CUR_EUR,
            ],
        'SJ' =>
            [
                'name' => "Svalbard and Jan Mayen",
                'iso3' => "SJM",
                'capital' => "Longyearbyen",
                'phone' => "47",
                'currency' => Currency::CUR_NOK,
            ],
        'PY' =>
            [
                'name' => "Paraguay",
                'iso3' => "PRY",
                'capital' => "Asuncion",
                'phone' => "595",
                'currency' => "PYG",
            ],
        'IQ' =>
            [
                'name' => "Iraq",
                'iso3' => "IRQ",
                'capital' => "Baghdad",
                'phone' => "964",
                'currency' => "IQD",
            ],
        'PA' =>
            [
                'name' => "Panama",
                'iso3' => "PAN",
                'capital' => "Panama City",
                'phone' => "507",
                'currency' => "PAB",
            ],
        'PF' =>
            [
                'name' => "French Polynesia",
                'iso3' => "PYF",
                'capital' => "Papeete",
                'phone' => "689",
                'currency' => "XPF",
            ],
        'PG' =>
            [
                'name' => "Papua New Guinea",
                'iso3' => "PNG",
                'capital' => "Port Moresby",
                'phone' => "675",
                'currency' => "PGK",
            ],
        'PE' =>
            [
                'name' => "Peru",
                'iso3' => "PER",
                'capital' => "Lima",
                'phone' => "51",
                'currency' => "PEN",
            ],
        'PK' =>
            [
                'name' => "Pakistan",
                'iso3' => "PAK",
                'capital' => "Islamabad",
                'phone' => "92",
                'currency' => "PKR",
            ],
        'PH' =>
            [
                'name' => "Philippines",
                'iso3' => "PHL",
                'capital' => "Manila",
                'phone' => "63",
                'currency' => Currency::CUR_PHP,
            ],
        'PN' =>
            [
                'name' => "Pitcairn",
                'iso3' => "PCN",
                'capital' => "Adamstown",
                'phone' => "870",
                'currency' => Currency::CUR_NZD,
            ],
        'PL' =>
            [
                'name' => "Poland",
                'iso3' => "POL",
                'capital' => "Warsaw",
                'phone' => "48",
                'currency' => Currency::CUR_PLN,
            ],
        'PM' =>
            [
                'name' => "Saint Pierre and Miquelon",
                'iso3' => "SPM",
                'capital' => "Saint-Pierre",
                'phone' => "508",
                'currency' => Currency::CUR_EUR,
            ],
        'ZM' =>
            [
                'name' => "Zambia",
                'iso3' => "ZMB",
                'capital' => "Lusaka",
                'phone' => "260",
                'currency' => "ZMK",
            ],
        'EH' =>
            [
                'name' => "Western Sahara",
                'iso3' => "ESH",
                'capital' => "El-Aaiun",
                'phone' => "212",
                'currency' => "MAD",
            ],
        'EE' =>
            [
                'name' => "Estonia",
                'iso3' => "EST",
                'capital' => "Tallinn",
                'phone' => "372",
                'currency' => Currency::CUR_EUR,
            ],
        'EG' =>
            [
                'name' => "Egypt",
                'iso3' => "EGY",
                'capital' => "Cairo",
                'phone' => "20",
                'currency' => "EGP",
            ],
        'ZA' =>
            [
                'name' => "South Africa",
                'iso3' => "ZAF",
                'capital' => "Pretoria",
                'phone' => "27",
                'currency' => Currency::CUR_ZAR,
            ],
        'EC' =>
            [
                'name' => "Ecuador",
                'iso3' => "ECU",
                'capital' => "Quito",
                'phone' => "593",
                'currency' => Currency::CUR_USD,
            ],
        'IT' =>
            [
                'name' => "Italy",
                'iso3' => "ITA",
                'capital' => "Rome",
                'phone' => "39",
                'currency' => Currency::CUR_EUR,
            ],
        'VN' =>
            [
                'name' => "Vietnam",
                'iso3' => "VNM",
                'capital' => "Hanoi",
                'phone' => "84",
                'currency' => "VND",
            ],
        'SB' =>
            [
                'name' => "Solomon Islands",
                'iso3' => "SLB",
                'capital' => "Honiara",
                'phone' => "677",
                'currency' => "SBD",
            ],
        'ET' =>
            [
                'name' => "Ethiopia",
                'iso3' => "ETH",
                'capital' => "Addis Ababa",
                'phone' => "251",
                'currency' => "ETB",
            ],
        'SO' =>
            [
                'name' => "Somalia",
                'iso3' => "SOM",
                'capital' => "Mogadishu",
                'phone' => "252",
                'currency' => "SOS",
            ],
        'ZW' =>
            [
                'name' => "Zimbabwe",
                'iso3' => "ZWE",
                'capital' => "Harare",
                'phone' => "263",
                'currency' => "ZWL",
            ],
        'SA' =>
            [
                'name' => "Saudi Arabia",
                'iso3' => "SAU",
                'capital' => "Riyadh",
                'phone' => "966",
                'currency' => Currency::CUR_SAR,
            ],
        'ES' =>
            [
                'name' => "Spain",
                'iso3' => "ESP",
                'capital' => "Madrid",
                'phone' => "34",
                'currency' => Currency::CUR_EUR,
            ],
        'ER' =>
            [
                'name' => "Eritrea",
                'iso3' => "ERI",
                'capital' => "Asmara",
                'phone' => "291",
                'currency' => "ERN",
            ],
        'ME' =>
            [
                'name' => "Montenegro",
                'iso3' => "MNE",
                'capital' => "Podgorica",
                'phone' => "382",
                'currency' => Currency::CUR_EUR,
            ],
        'MD' =>
            [
                'name' => "Moldova",
                'iso3' => "MDA",
                'capital' => "Chisinau",
                'phone' => "373",
                'currency' => Currency::CUR_MDL,
            ],
        'MG' =>
            [
                'name' => "Madagascar",
                'iso3' => "MDG",
                'capital' => "Antananarivo",
                'phone' => "261",
                'currency' => "MGA",
            ],
        'MF' =>
            [
                'name' => "Saint Martin",
                'iso3' => "MAF",
                'capital' => "Marigot",
                'phone' => "590",
                'currency' => Currency::CUR_EUR,
            ],
        'MA' =>
            [
                'name' => "Morocco",
                'iso3' => "MAR",
                'capital' => "Rabat",
                'phone' => "212",
                'currency' => "MAD",
            ],
        'MC' =>
            [
                'name' => "Monaco",
                'iso3' => "MCO",
                'capital' => "Monaco",
                'phone' => "377",
                'currency' => Currency::CUR_EUR,
            ],
        'UZ' =>
            [
                'name' => "Uzbekistan",
                'iso3' => "UZB",
                'capital' => "Tashkent",
                'phone' => "998",
                'currency' => "UZS",
            ],
        'MM' =>
            [
                'name' => "Myanmar",
                'iso3' => "MMR",
                'capital' => "Nay Pyi Taw",
                'phone' => "95",
                'currency' => "MMK",
            ],
        'ML' =>
            [
                'name' => "Mali",
                'iso3' => "MLI",
                'capital' => "Bamako",
                'phone' => "223",
                'currency' => Currency::CUR_XOF,
            ],
        'MO' =>
            [
                'name' => "Macao",
                'iso3' => "MAC",
                'capital' => "Macao",
                'phone' => "853",
                'currency' => "MOP",
            ],
        'MN' =>
            [
                'name' => "Mongolia",
                'iso3' => "MNG",
                'capital' => "Ulan Bator",
                'phone' => "976",
                'currency' => "MNT",
            ],
        'MH' =>
            [
                'name' => "Marshall Islands",
                'iso3' => "MHL",
                'capital' => "Majuro",
                'phone' => "692",
                'currency' => Currency::CUR_USD,
            ],
        'MK' =>
            [
                'name' => "Macedonia",
                'iso3' => "MKD",
                'capital' => "Skopje",
                'phone' => "389",
                'currency' => Currency::CUR_MKD,
            ],
        'MU' =>
            [
                'name' => "Mauritius",
                'iso3' => "MUS",
                'capital' => "Port Louis",
                'phone' => "230",
                'currency' => "MUR",
            ],
        'MT' =>
            [
                'name' => "Malta",
                'iso3' => "MLT",
                'capital' => "Valletta",
                'phone' => "356",
                'currency' => Currency::CUR_EUR,
            ],
        'MW' =>
            [
                'name' => "Malawi",
                'iso3' => "MWI",
                'capital' => "Lilongwe",
                'phone' => "265",
                'currency' => "MWK",
            ],
        'MV' =>
            [
                'name' => "Maldives",
                'iso3' => "MDV",
                'capital' => "Male",
                'phone' => "960",
                'currency' => "MVR",
            ],
        'MQ' =>
            [
                'name' => "Martinique",
                'iso3' => "MTQ",
                'capital' => "Fort-de-France",
                'phone' => "596",
                'currency' => Currency::CUR_EUR,
            ],
        'MP' =>
            [
                'name' => "Northern Mariana Islands",
                'iso3' => "MNP",
                'capital' => "Saipan",
                'phone' => "+1-670",
                'currency' => Currency::CUR_USD,
            ],
        'MS' =>
            [
                'name' => "Montserrat",
                'iso3' => "MSR",
                'capital' => "Plymouth",
                'phone' => "+1-664",
                'currency' => "XCD",
            ],
        'MR' =>
            [
                'name' => "Mauritania",
                'iso3' => "MRT",
                'capital' => "Nouakchott",
                'phone' => "222",
                'currency' => "MRO",
            ],
        'IM' =>
            [
                'name' => "Isle of Man",
                'iso3' => "IMN",
                'capital' => "Douglas, Isle of Man",
                'phone' => "+44-1624",
                'currency' => Currency::CUR_GBP,
            ],
        'UG' =>
            [
                'name' => "Uganda",
                'iso3' => "UGA",
                'capital' => "Kampala",
                'phone' => "256",
                'currency' => "UGX",
            ],
        'TZ' =>
            [
                'name' => "Tanzania",
                'iso3' => "TZA",
                'capital' => "Dodoma",
                'phone' => "255",
                'currency' => "TZS",
            ],
        'MY' =>
            [
                'name' => "Malaysia",
                'iso3' => "MYS",
                'capital' => "Kuala Lumpur",
                'phone' => "60",
                'currency' => Currency::CUR_MYR,
            ],
        'MX' =>
            [
                'name' => "Mexico",
                'iso3' => "MEX",
                'capital' => "Mexico City",
                'phone' => "52",
                'currency' => Currency::CUR_MXN,
            ],
        'IL' =>
            [
                'name' => "Israel",
                'iso3' => "ISR",
                'capital' => "Jerusalem",
                'phone' => "972",
                'currency' => Currency::CUR_ILS,
            ],
        'FR' =>
            [
                'name' => "France",
                'iso3' => "FRA",
                'capital' => "Paris",
                'phone' => "33",
                'currency' => Currency::CUR_EUR,
            ],
        'IO' =>
            [
                'name' => "British Indian Ocean Territory",
                'iso3' => "IOT",
                'capital' => "Diego Garcia",
                'phone' => "246",
                'currency' => Currency::CUR_USD,
            ],
        'SH' =>
            [
                'name' => "Saint Helena",
                'iso3' => "SHN",
                'capital' => "Jamestown",
                'phone' => "290",
                'currency' => "SHP",
            ],
        'FI' =>
            [
                'name' => "Finland",
                'iso3' => "FIN",
                'capital' => "Helsinki",
                'phone' => "358",
                'currency' => Currency::CUR_EUR,
            ],
        'FJ' =>
            [
                'name' => "Fiji",
                'iso3' => "FJI",
                'capital' => "Suva",
                'phone' => "679",
                'currency' => "FJD",
            ],
        'FK' =>
            [
                'name' => "Falkland Islands",
                'iso3' => "FLK",
                'capital' => "Stanley",
                'phone' => "500",
                'currency' => "FKP",
            ],
        'FM' =>
            [
                'name' => "Micronesia",
                'iso3' => "FSM",
                'capital' => "Palikir",
                'phone' => "691",
                'currency' => Currency::CUR_USD,
            ],
        'FO' =>
            [
                'name' => "Faroe Islands",
                'iso3' => "FRO",
                'capital' => "Torshavn",
                'phone' => "298",
                'currency' => Currency::CUR_DKK,
            ],
        'NI' =>
            [
                'name' => "Nicaragua",
                'iso3' => "NIC",
                'capital' => "Managua",
                'phone' => "505",
                'currency' => "NIO",
            ],
        'NL' =>
            [
                'name' => "Netherlands",
                'iso3' => "NLD",
                'capital' => "Amsterdam",
                'phone' => "31",
                'currency' => Currency::CUR_EUR,
            ],
        'NO' =>
            [
                'name' => "Norway",
                'iso3' => "NOR",
                'capital' => "Oslo",
                'phone' => "47",
                'currency' => Currency::CUR_NOK,
            ],
        'NA' =>
            [
                'name' => "Namibia",
                'iso3' => "NAM",
                'capital' => "Windhoek",
                'phone' => "264",
                'currency' => "NAD",
            ],
        'VU' =>
            [
                'name' => "Vanuatu",
                'iso3' => "VUT",
                'capital' => "Port Vila",
                'phone' => "678",
                'currency' => "VUV",
            ],
        'NC' =>
            [
                'name' => "New Caledonia",
                'iso3' => "NCL",
                'capital' => "Noumea",
                'phone' => "687",
                'currency' => "XPF",
            ],
        'NE' =>
            [
                'name' => "Niger",
                'iso3' => "NER",
                'capital' => "Niamey",
                'phone' => "227",
                'currency' => Currency::CUR_XOF,
            ],
        'NF' =>
            [
                'name' => "Norfolk Island",
                'iso3' => "NFK",
                'capital' => "Kingston",
                'phone' => "672",
                'currency' => Currency::CUR_AUD,
            ],
        'NG' =>
            [
                'name' => "Nigeria",
                'iso3' => "NGA",
                'capital' => "Abuja",
                'phone' => "234",
                'currency' => "NGN",
            ],
        'NZ' =>
            [
                'name' => "New Zealand",
                'iso3' => "NZL",
                'capital' => "Wellington",
                'phone' => "64",
                'currency' => Currency::CUR_NZD,
            ],
        'NP' =>
            [
                'name' => "Nepal",
                'iso3' => "NPL",
                'capital' => "Kathmandu",
                'phone' => "977",
                'currency' => "NPR",
            ],
        'NR' =>
            [
                'name' => "Nauru",
                'iso3' => "NRU",
                'capital' => "Yaren",
                'phone' => "674",
                'currency' => Currency::CUR_AUD,
            ],
        'NU' =>
            [
                'name' => "Niue",
                'iso3' => "NIU",
                'capital' => "Alofi",
                'phone' => "683",
                'currency' => Currency::CUR_NZD,
            ],
        'CK' =>
            [
                'name' => "Cook Islands",
                'iso3' => "COK",
                'capital' => "Avarua",
                'phone' => "682",
                'currency' => Currency::CUR_NZD,
            ],
        'XK' =>
            [
                'name' => "Kosovo",
                'iso3' => "XKX",
                'capital' => "Pristina",
                'phone' => "",
                'currency' => Currency::CUR_EUR,
            ],
        'CI' =>
            [
                'name' => "Ivory Coast",
                'iso3' => "CIV",
                'capital' => "Yamoussoukro",
                'phone' => "225",
                'currency' => Currency::CUR_XOF,
            ],
        'CH' =>
            [
                'name' => "Switzerland",
                'iso3' => "CHE",
                'capital' => "Berne",
                'phone' => "41",
                'currency' => Currency::CUR_CHF,
            ],
        'CO' =>
            [
                'name' => "Colombia",
                'iso3' => "COL",
                'capital' => "Bogota",
                'phone' => "57",
                'currency' => "COP",
            ],
        'CN' =>
            [
                'name' => "China",
                'iso3' => "CHN",
                'capital' => "Beijing",
                'phone' => "86",
                'currency' => Currency::CUR_CNY,
            ],
        'CM' =>
            [
                'name' => "Cameroon",
                'iso3' => "CMR",
                'capital' => "Yaounde",
                'phone' => "237",
                'currency' => "XAF",
            ],
        'CL' =>
            [
                'name' => "Chile",
                'iso3' => "CHL",
                'capital' => "Santiago",
                'phone' => "56",
                'currency' => "CLP",
            ],
        'CC' =>
            [
                'name' => "Cocos Islands",
                'iso3' => "CCK",
                'capital' => "West Island",
                'phone' => "61",
                'currency' => Currency::CUR_AUD,
            ],
        'CA' =>
            [
                'name' => "Canada",
                'iso3' => "CAN",
                'capital' => "Ottawa",
                'phone' => "1",
                'currency' => Currency::CUR_CAD,
            ],
        'CG' =>
            [
                'name' => "Republic of the Congo",
                'iso3' => "COG",
                'capital' => "Brazzaville",
                'phone' => "242",
                'currency' => "XAF",
            ],
        'CF' =>
            [
                'name' => "Central African Republic",
                'iso3' => "CAF",
                'capital' => "Bangui",
                'phone' => "236",
                'currency' => "XAF",
            ],
        'CD' =>
            [
                'name' => "Democratic Republic of the Congo",
                'iso3' => "COD",
                'capital' => "Kinshasa",
                'phone' => "243",
                'currency' => "CDF",
            ],
        'CZ' =>
            [
                'name' => "Czech Republic",
                'iso3' => "CZE",
                'capital' => "Prague",
                'phone' => "420",
                'currency' => Currency::CUR_CZK,
            ],
        'CY' =>
            [
                'name' => "Cyprus",
                'iso3' => "CYP",
                'capital' => "Nicosia",
                'phone' => "357",
                'currency' => Currency::CUR_EUR,
            ],
        'CX' =>
            [
                'name' => "Christmas Island",
                'iso3' => "CXR",
                'capital' => "Flying Fish Cove",
                'phone' => "61",
                'currency' => Currency::CUR_AUD,
            ],
        'CR' =>
            [
                'name' => "Costa Rica",
                'iso3' => "CRI",
                'capital' => "San Jose",
                'phone' => "506",
                'currency' => "CRC",
            ],
        'CW' =>
            [
                'name' => "Curacao",
                'iso3' => "CUW",
                'capital' => "Willemstad",
                'phone' => "599",
                'currency' => "ANG",
            ],
        'CV' =>
            [
                'name' => "Cape Verde",
                'iso3' => "CPV",
                'capital' => "Praia",
                'phone' => "238",
                'currency' => "CVE",
            ],
        'CU' =>
            [
                'name' => "Cuba",
                'iso3' => "CUB",
                'capital' => "Havana",
                'phone' => "53",
                'currency' => "CUP",
            ],
        'SZ' =>
            [
                'name' => "Swaziland",
                'iso3' => "SWZ",
                'capital' => "Mbabane",
                'phone' => "268",
                'currency' => "SZL",
            ],
        'SY' =>
            [
                'name' => "Syria",
                'iso3' => "SYR",
                'capital' => "Damascus",
                'phone' => "963",
                'currency' => "SYP",
            ],
        'SX' =>
            [
                'name' => "Sint Maarten",
                'iso3' => "SXM",
                'capital' => "Philipsburg",
                'phone' => "599",
                'currency' => "ANG",
            ],
        'KG' =>
            [
                'name' => "Kyrgyzstan",
                'iso3' => "KGZ",
                'capital' => "Bishkek",
                'phone' => "996",
                'currency' => "KGS",
            ],
        'KE' =>
            [
                'name' => "Kenya",
                'iso3' => "KEN",
                'capital' => "Nairobi",
                'phone' => "254",
                'currency' => "KES",
            ],
        'SS' =>
            [
                'name' => "South Sudan",
                'iso3' => "SSD",
                'capital' => "Juba",
                'phone' => "211",
                'currency' => "SSP",
            ],
        'SR' =>
            [
                'name' => "Suriname",
                'iso3' => "SUR",
                'capital' => "Paramaribo",
                'phone' => "597",
                'currency' => "SRD",
            ],
        'KI' =>
            [
                'name' => "Kiribati",
                'iso3' => "KIR",
                'capital' => "Tarawa",
                'phone' => "686",
                'currency' => Currency::CUR_AUD,
            ],
        'KH' =>
            [
                'name' => "Cambodia",
                'iso3' => "KHM",
                'capital' => "Phnom Penh",
                'phone' => "855",
                'currency' => "KHR",
            ],
        'KN' =>
            [
                'name' => "Saint Kitts and Nevis",
                'iso3' => "KNA",
                'capital' => "Basseterre",
                'phone' => "+1-869",
                'currency' => "XCD",
            ],
        'KM' =>
            [
                'name' => "Comoros",
                'iso3' => "COM",
                'capital' => "Moroni",
                'phone' => "269",
                'currency' => "KMF",
            ],
        'ST' =>
            [
                'name' => "Sao Tome and Principe",
                'iso3' => "STP",
                'capital' => "Sao Tome",
                'phone' => "239",
                'currency' => "STD",
            ],
        'SK' =>
            [
                'name' => "Slovakia",
                'iso3' => "SVK",
                'capital' => "Bratislava",
                'phone' => "421",
                'currency' => Currency::CUR_EUR,
            ],
        'KR' =>
            [
                'name' => "South Korea",
                'iso3' => "KOR",
                'capital' => "Seoul",
                'phone' => "82",
                'currency' => Currency::CUR_KRW,
            ],
        'SI' =>
            [
                'name' => "Slovenia",
                'iso3' => "SVN",
                'capital' => "Ljubljana",
                'phone' => "386",
                'currency' => Currency::CUR_EUR,
            ],
        'KP' =>
            [
                'name' => "North Korea",
                'iso3' => "PRK",
                'capital' => "Pyongyang",
                'phone' => "850",
                'currency' => "KPW",
            ],
        'KW' =>
            [
                'name' => "Kuwait",
                'iso3' => "KWT",
                'capital' => "Kuwait City",
                'phone' => "965",
                'currency' => Currency::CUR_KWD,
            ],
        'SN' =>
            [
                'name' => "Senegal",
                'iso3' => "SEN",
                'capital' => "Dakar",
                'phone' => "221",
                'currency' => Currency::CUR_XOF,
            ],
        'SM' =>
            [
                'name' => "San Marino",
                'iso3' => "SMR",
                'capital' => "San Marino",
                'phone' => "378",
                'currency' => Currency::CUR_EUR,
            ],
        'SL' =>
            [
                'name' => "Sierra Leone",
                'iso3' => "SLE",
                'capital' => "Freetown",
                'phone' => "232",
                'currency' => "SLL",
            ],
        'SC' =>
            [
                'name' => "Seychelles",
                'iso3' => "SYC",
                'capital' => "Victoria",
                'phone' => "248",
                'currency' => "SCR",
            ],
        'KZ' =>
            [
                'name' => "Kazakhstan",
                'iso3' => "KAZ",
                'capital' => "Astana",
                'phone' => "7",
                'currency' => "KZT",
            ],
        'KY' =>
            [
                'name' => "Cayman Islands",
                'iso3' => "CYM",
                'capital' => "George Town",
                'phone' => "+1-345",
                'currency' => "KYD",
            ],
        'SG' =>
            [
                'name' => "Singapore",
                'iso3' => "SGP",
                'capital' => "Singapur",
                'phone' => "65",
                'currency' => Currency::CUR_SGD,
            ],
        'SE' =>
            [
                'name' => "Sweden",
                'iso3' => "SWE",
                'capital' => "Stockholm",
                'phone' => "46",
                'currency' => Currency::CUR_SEK,
            ],
        'SD' =>
            [
                'name' => "Sudan",
                'iso3' => "SDN",
                'capital' => "Khartoum",
                'phone' => "249",
                'currency' => "SDG",
            ],
        'DO' =>
            [
                'name' => "Dominican Republic",
                'iso3' => "DOM",
                'capital' => "Santo Domingo",
                'phone' => "+1-809 and 1-829",
                'currency' => "DOP",
            ],
        'DM' =>
            [
                'name' => "Dominica",
                'iso3' => "DMA",
                'capital' => "Roseau",
                'phone' => "+1-767",
                'currency' => "XCD",
            ],
        'DJ' =>
            [
                'name' => "Djibouti",
                'iso3' => "DJI",
                'capital' => "Djibouti",
                'phone' => "253",
                'currency' => "DJF",
            ],
        'DK' =>
            [
                'name' => "Denmark",
                'iso3' => "DNK",
                'capital' => "Copenhagen",
                'phone' => "45",
                'currency' => Currency::CUR_DKK,
            ],
        'VG' =>
            [
                'name' => "British Virgin Islands",
                'iso3' => "VGB",
                'capital' => "Road Town",
                'phone' => "+1-284",
                'currency' => Currency::CUR_USD,
            ],
        'DE' =>
            [
                'name' => "Germany",
                'iso3' => "DEU",
                'capital' => "Berlin",
                'phone' => "49",
                'currency' => Currency::CUR_EUR,
            ],
        'YE' =>
            [
                'name' => "Yemen",
                'iso3' => "YEM",
                'capital' => "Sanaa",
                'phone' => "967",
                'currency' => "YER",
            ],
        'DZ' =>
            [
                'name' => "Algeria",
                'iso3' => "DZA",
                'capital' => "Algiers",
                'phone' => "213",
                'currency' => "DZD",
            ],
        'US' =>
            [
                'name' => "United States",
                'iso3' => "USA",
                'capital' => "Washington",
                'phone' => "1",
                'currency' => Currency::CUR_USD,
            ],
        'UY' =>
            [
                'name' => "Uruguay",
                'iso3' => "URY",
                'capital' => "Montevideo",
                'phone' => "598",
                'currency' => "UYU",
            ],
        'YT' =>
            [
                'name' => "Mayotte",
                'iso3' => "MYT",
                'capital' => "Mamoudzou",
                'phone' => "262",
                'currency' => Currency::CUR_EUR,
            ],
        'UM' =>
            [
                'name' => "United States Minor Outlying Islands",
                'iso3' => "UMI",
                'capital' => "",
                'phone' => "1",
                'currency' => Currency::CUR_USD,
            ],
        'LB' =>
            [
                'name' => "Lebanon",
                'iso3' => "LBN",
                'capital' => "Beirut",
                'phone' => "961",
                'currency' => "LBP",
            ],
        'LC' =>
            [
                'name' => "Saint Lucia",
                'iso3' => "LCA",
                'capital' => "Castries",
                'phone' => "+1-758",
                'currency' => "XCD",
            ],
        'LA' =>
            [
                'name' => "Laos",
                'iso3' => "LAO",
                'capital' => "Vientiane",
                'phone' => "856",
                'currency' => "LAK",
            ],
        'TV' =>
            [
                'name' => "Tuvalu",
                'iso3' => "TUV",
                'capital' => "Funafuti",
                'phone' => "688",
                'currency' => Currency::CUR_AUD,
            ],
        'TW' =>
            [
                'name' => "Taiwan",
                'iso3' => "TWN",
                'capital' => "Taipei",
                'phone' => "886",
                'currency' => Currency::CUR_TWD,
            ],
        'TT' =>
            [
                'name' => "Trinidad and Tobago",
                'iso3' => "TTO",
                'capital' => "Port of Spain",
                'phone' => "+1-868",
                'currency' => "TTD",
            ],
        'TR' =>
            [
                'name' => "Turkey",
                'iso3' => "TUR",
                'capital' => "Ankara",
                'phone' => "90",
                'currency' => Currency::CUR_TRY,
            ],
        'LK' =>
            [
                'name' => "Sri Lanka",
                'iso3' => "LKA",
                'capital' => "Colombo",
                'phone' => "94",
                'currency' => "LKR",
            ],
        'LI' =>
            [
                'name' => "Liechtenstein",
                'iso3' => "LIE",
                'capital' => "Vaduz",
                'phone' => "423",
                'currency' => Currency::CUR_CHF,
            ],
        'LV' =>
            [
                'name' => "Latvia",
                'iso3' => "LVA",
                'capital' => "Riga",
                'phone' => "371",
                'currency' => Currency::CUR_EUR,
            ],
        'TO' =>
            [
                'name' => "Tonga",
                'iso3' => "TON",
                'capital' => "Nuku'alofa",
                'phone' => "676",
                'currency' => "TOP",
            ],
        'LT' =>
            [
                'name' => "Lithuania",
                'iso3' => "LTU",
                'capital' => "Vilnius",
                'phone' => "370",
                'currency' => "LTL",
            ],
        'LU' =>
            [
                'name' => "Luxembourg",
                'iso3' => "LUX",
                'capital' => "Luxembourg",
                'phone' => "352",
                'currency' => Currency::CUR_EUR,
            ],
        'LR' =>
            [
                'name' => "Liberia",
                'iso3' => "LBR",
                'capital' => "Monrovia",
                'phone' => "231",
                'currency' => "LRD",
            ],
        'LS' =>
            [
                'name' => "Lesotho",
                'iso3' => "LSO",
                'capital' => "Maseru",
                'phone' => "266",
                'currency' => "LSL",
            ],
        'TH' =>
            [
                'name' => "Thailand",
                'iso3' => "THA",
                'capital' => "Bangkok",
                'phone' => "66",
                'currency' => Currency::CUR_THB,
            ],
        'TF' =>
            [
                'name' => "French Southern Territories",
                'iso3' => "ATF",
                'capital' => "Port-aux-Francais",
                'phone' => "",
                'currency' => Currency::CUR_EUR,
            ],
        'TG' =>
            [
                'name' => "Togo",
                'iso3' => "TGO",
                'capital' => "Lome",
                'phone' => "228",
                'currency' => Currency::CUR_XOF,
            ],
        'TD' =>
            [
                'name' => "Chad",
                'iso3' => "TCD",
                'capital' => "N'Djamena",
                'phone' => "235",
                'currency' => "XAF",
            ],
        'TC' =>
            [
                'name' => "Turks and Caicos Islands",
                'iso3' => "TCA",
                'capital' => "Cockburn Town",
                'phone' => "+1-649",
                'currency' => Currency::CUR_USD,
            ],
        'LY' =>
            [
                'name' => "Libya",
                'iso3' => "LBY",
                'capital' => "Tripolis",
                'phone' => "218",
                'currency' => "LYD",
            ],
        'VA' =>
            [
                'name' => "Vatican",
                'iso3' => "VAT",
                'capital' => "Vatican City",
                'phone' => "379",
                'currency' => Currency::CUR_EUR,
            ],
        'VC' =>
            [
                'name' => "Saint Vincent and the Grenadines",
                'iso3' => "VCT",
                'capital' => "Kingstown",
                'phone' => "+1-784",
                'currency' => "XCD",
            ],
        'AE' =>
            [
                'name' => "United Arab Emirates",
                'iso3' => "ARE",
                'capital' => "Abu Dhabi",
                'phone' => "971",
                'currency' => "AED",
            ],
        'AD' =>
            [
                'name' => "Andorra",
                'iso3' => "AND",
                'capital' => "Andorra la Vella",
                'phone' => "376",
                'currency' => Currency::CUR_EUR,
            ],
        'AG' =>
            [
                'name' => "Antigua and Barbuda",
                'iso3' => "ATG",
                'capital' => "St. John's",
                'phone' => "+1-268",
                'currency' => "XCD",
            ],
        'AF' =>
            [
                'name' => "Afghanistan",
                'iso3' => "AFG",
                'capital' => "Kabul",
                'phone' => "93",
                'currency' => "AFN",
            ],
        'AI' =>
            [
                'name' => "Anguilla",
                'iso3' => "AIA",
                'capital' => "The Valley",
                'phone' => "+1-264",
                'currency' => "XCD",
            ],
        'VI' =>
            [
                'name' => "U.S. Virgin Islands",
                'iso3' => "VIR",
                'capital' => "Charlotte Amalie",
                'phone' => "+1-340",
                'currency' => Currency::CUR_USD,
            ],
        'IS' =>
            [
                'name' => "Iceland",
                'iso3' => "ISL",
                'capital' => "Reykjavik",
                'phone' => "354",
                'currency' => Currency::CUR_ISK,
            ],
        'IR' =>
            [
                'name' => "Iran",
                'iso3' => "IRN",
                'capital' => "Tehran",
                'phone' => "98",
                'currency' => "IRR",
            ],
        'AM' =>
            [
                'name' => "Armenia",
                'iso3' => "ARM",
                'capital' => "Yerevan",
                'phone' => "374",
                'currency' => Currency::CUR_AMD,
            ],
        'AL' =>
            [
                'name' => "Albania",
                'iso3' => "ALB",
                'capital' => "Tirana",
                'phone' => "355",
                'currency' => Currency::CUR_ALL,
            ],
        'AO' =>
            [
                'name' => "Angola",
                'iso3' => "AGO",
                'capital' => "Luanda",
                'phone' => "244",
                'currency' => "AOA",
            ],
        'AQ' =>
            [
                'name' => "Antarctica",
                'iso3' => "ATA",
                'capital' => "",
                'phone' => "",
                'currency' => "",
            ],
        'AS' =>
            [
                'name' => "American Samoa",
                'iso3' => "ASM",
                'capital' => "Pago Pago",
                'phone' => "+1-684",
                'currency' => Currency::CUR_USD,
            ],
        'AR' =>
            [
                'name' => "Argentina",
                'iso3' => "ARG",
                'capital' => "Buenos Aires",
                'phone' => "54",
                'currency' => "ARS",
            ],
        'AU' =>
            [
                'name' => "Australia",
                'iso3' => "AUS",
                'capital' => "Canberra",
                'phone' => "61",
                'currency' => Currency::CUR_AUD,
            ],
        'AT' =>
            [
                'name' => "Austria",
                'iso3' => "AUT",
                'capital' => "Vienna",
                'phone' => "43",
                'currency' => Currency::CUR_EUR,
            ],
        'AW' =>
            [
                'name' => "Aruba",
                'iso3' => "ABW",
                'capital' => "Oranjestad",
                'phone' => "297",
                'currency' => "AWG",
            ],
        'IN' =>
            [
                'name' => "India",
                'iso3' => "IND",
                'capital' => "New Delhi",
                'phone' => "91",
                'currency' => Currency::CUR_INR,
            ],
        'AX' =>
            [
                'name' => "Aland Islands",
                'iso3' => "ALA",
                'capital' => "Mariehamn",
                'phone' => "+358-18",
                'currency' => Currency::CUR_EUR,
            ],
        'AZ' =>
            [
                'name' => "Azerbaijan",
                'iso3' => "AZE",
                'capital' => "Baku",
                'phone' => "994",
                'currency' => Currency::CUR_AZN,
            ],
        'IE' =>
            [
                'name' => "Ireland",
                'iso3' => "IRL",
                'capital' => "Dublin",
                'phone' => "353",
                'currency' => Currency::CUR_EUR,
            ],
        'ID' =>
            [
                'name' => "Indonesia",
                'iso3' => "IDN",
                'capital' => "Jakarta",
                'phone' => "62",
                'currency' => Currency::CUR_IDR,
            ],
        'UA' =>
            [
                'name' => "Ukraine",
                'iso3' => "UKR",
                'capital' => "Kiev",
                'phone' => "380",
                'currency' => Currency::CUR_UAH,
            ],
        'QA' =>
            [
                'name' => "Qatar",
                'iso3' => "QAT",
                'capital' => "Doha",
                'phone' => "974",
                'currency' => "QAR",
            ],
        'MZ' =>
            [
                'name' => "Mozambique",
                'iso3' => "MOZ",
                'capital' => "Maputo",
                'phone' => "258",
                'currency' => "MZN",
            ],
    ];

    /**
     * Returns the country name list array with
     * any valid field as the key
     *
     * @param string $value The field you want as the value for return array
     * @param string $key The field you want as the key for return array
     *
     * @return array $countryList    Array containing the list of countries
     * @throws Exception            if the key or value field are not valid
     */
    public static function getAllCountryList(string $value = 'name', string $key = 'iso2'): array
    {
        if (!in_array($value, ['iso3', 'name', 'capital', 'currency', 'phone'])) {
            throw new Exception('Value is not a valid field name');
        }

        if (!in_array($key, ['iso2', 'iso3', 'name', 'capital', 'currency', 'phone'])) {
            throw new Exception('Key is not a valid field name');
        }

        $countryList = [];

        foreach (self::$countries as $iso2 => $country) {
            if ($key == 'iso2') {
                $countryList[$iso2] = $country[$value];
            } elseif ($key == 'currency') {
                if (!isset($countryList[$country[$key]])) {
                    $countryList[$country[$key]] = [];
                }
                $countryList[$country[$key]][] = $country[$value];
            } else {
                $countryList[$country[$key]] = $country[$value];
            }
        }

        return $countryList;
    }

    /**
     * @param string $currency
     *
     * @return array
     */
    public static function getCountriesByCurrency(string $currency): array
    {
        return Arr::where(self::$countries, function ($item) use ($currency) {
            return $item['currency'] == $currency;
        });
    }
}
