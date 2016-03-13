<?php

session_start();

//Path info
$siteurl = "http://".$_SERVER['HTTP_HOST'];
$root = "/fantasycurling/";
$path = $root;

//Error reporting
error_reporting(-1);
ini_set('display_errors', 'on');

//Server settings
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
ini_set('max_input_time', '300');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '120M');

//date
$months[1] = "January";
$months[2] = "February";
$months[3] = "March";
$months[4] = "April";
$months[5] = "May";
$months[6] = "June";
$months[7] = "July";
$months[8] = "August";
$months[9] = "September";
$months[10] = "October";
$months[11] = "November";
$months[12] = "December";

//provinces
$provinces[1][0] = "Alberta";
$provinces[2][0] = "British Columbia";
$provinces[3][0] = "Manitoba";
$provinces[4][0] = "New Brunswick";
$provinces[5][0] = "Newfoundland";
$provinces[6][0] = "Northwest Territories";
$provinces[7][0] = "Nova Scotia";
$provinces[8][0] = "Nunavut";
$provinces[9][0] = "Ontario";
$provinces[10][0] = "Prince Edward Island";
$provinces[11][0] = "Quebec";
$provinces[12][0] = "Saskatchewan";
$provinces[13][0] = "Yukon";
$provinces[1][1] = "AB";
$provinces[2][1] = "BC";
$provinces[3][1] = "MB";
$provinces[4][1] = "NB";
$provinces[5][1] = "NL";
$provinces[6][1] = "NT";
$provinces[7][1] = "NS";
$provinces[8][1] = "NU";
$provinces[9][1] = "ON";
$provinces[10][1] = "PE";
$provinces[11][1] = "QC";
$provinces[12][1] = "SK";
$provinces[13][1] = "YT";

//states
$states[1][0] = "Alabama";
$states[2][0] = "Alaska";
$states[3][0] = "Arizona";
$states[4][0] = "Arkansas";
$states[5][0] = "California";
$states[6][0] = "Colorado";
$states[7][0] = "Connecticut";
$states[8][0] = "Delaware";
$states[9][0] = "Florida";
$states[10][0] = "Georgia";
$states[11][0] = "Hawaii";
$states[12][0] = "Idaho";
$states[13][0] = "Illinois";
$states[14][0] = "Indiana";
$states[15][0] = "Iowa";
$states[16][0] = "Kansas";
$states[17][0] = "Kentucky";
$states[18][0] = "Louisiana";
$states[19][0] = "Maine";
$states[20][0] = "Maryland";
$states[21][0] = "Massachusetts";
$states[22][0] = "Michigan";
$states[23][0] = "Minnesota";
$states[24][0] = "Mississippi";
$states[25][0] = "Missouri";
$states[26][0] = "Montana";
$states[27][0] = "Nebraska";
$states[28][0] = "Nevada";
$states[29][0] = "New Hampshire";
$states[30][0] = "New Jersey";
$states[31][0] = "New Mexico";
$states[32][0] = "New York";
$states[33][0] = "North Carolina";
$states[34][0] = "North Dakota";
$states[35][0] = "Ohio";
$states[36][0] = "Oklahoma";
$states[37][0] = "Oregon";
$states[38][0] = "Pennsylvania";
$states[39][0] = "Rhode Island";
$states[40][0] = "South Carolina";
$states[41][0] = "South Dakota";
$states[42][0] = "Tennessee";
$states[43][0] = "Texas";
$states[44][0] = "Utah";
$states[45][0] = "Vermont";
$states[46][0] = "Virginia";
$states[47][0] = "Washington";
$states[48][0] = "West Virginia";
$states[49][0] = "Wisconsin";
$states[50][0] = "Wyoming";
$states[1][1] = "AL";
$states[2][1] = "AK";
$states[3][1] = "AZ";
$states[4][1] = "AR";
$states[5][1] = "CA";
$states[6][1] = "CO";
$states[7][1] = "CT";
$states[8][1] = "DE";
$states[9][1] = "FL";
$states[10][1] = "GA";
$states[11][1] = "HI";
$states[12][1] = "ID";
$states[13][1] = "IL";
$states[14][1] = "IN";
$states[15][1] = "IA";
$states[16][1] = "KS";
$states[17][1] = "KY";
$states[18][1] = "LA";
$states[19][1] = "ME";
$states[20][1] = "MD";
$states[21][1] = "MA";
$states[22][1] = "MI";
$states[23][1] = "MN";
$states[24][1] = "MS";
$states[25][1] = "MO";
$states[26][1] = "MT";
$states[27][1] = "NE";
$states[28][1] = "NV";
$states[29][1] = "NH";
$states[30][1] = "NJ"; 
$states[31][1] = "NM";
$states[32][1] = "NY";
$states[33][1] = "NC"; 
$states[34][1] = "ND";
$states[35][1] = "OH";
$states[36][1] = "OK";
$states[37][1] = "OR";
$states[38][1] = "PA";
$states[39][1] = "RI";
$states[40][1] = "SC";
$states[41][1] = "SD";
$states[42][1] = "TN";
$states[43][1] = "TX";
$states[44][1] = "UT";
$states[45][1] = "VT";
$states[46][1] = "VA";
$states[47][1] = "WA";
$states[48][1] = "WV";
$states[49][1] = "WI";
$states[50][1] = "WY";

?>