<?php
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class AlternateCatalogues
{
    public static function CheckAlternate($annotations)
    {
        //Check every name in annotations, if its one of messiers catalogue
        foreach($annotations as $oKey => $object)
        {	
            foreach($object["names"] as $nKey => $name)
            {                
                if(array_key_exists($name,self::GetMessier()))
                {
                    //Set new name and type
                    $object["names"][$nKey] = self::GetMessier()[$name];
                    $object["type"] = "messier";

                    //set manipulated object
                    $annotations[$oKey] = $object;
                }
            }
        }

        return $annotations;
    }

    //Reference Table/Array
    public static function GetMessier()
    {
        return array(
            "NGC 1952" => "M 1",
            "NGC 7089" => "M 2",
            "NGC 5272" => "M 3",
            "NGC 6121" => "M 4",
            "NGC 5904" => "M 5",
            "NGC 6405" => "M 6",
            "NGC 6475" => "M 7",
            "NGC 6523" => "M 8",
            "NGC 6526" => "M 8",
            "NGC 6530" => "M 8",
            "NGC 6533" => "M 8",
            "NGC 6333" => "M 9",
            "NGC 6254" => "M 10",
            "NGC 6705" => "M 11",
            "NGC 6218" => "M 12",
            "NGC 6205" => "M 13",
            "NGC 6402" => "M 14",
            "NGC 7078" => "M 15",
            "NGC 6611" => "M 16",
            "NGC 6618" => "M 17",
            "NGC 6613" => "M 18",
            "NGC 6273" => "M 19",
            "NGC 6514" => "M 20",
            "NGC 6531" => "M 21",
            "NGC 6656" => "M 22",
            "NGC 6494" => "M 23",
            "NGC 6603" => "M 24",
            "NGC 6694" => "M 26",
            "NGC 6853" => "M 27",
            "NGC 6626" => "M 28",
            "NGC 6913" => "M 29",
            "NGC 7099" => "M 30",
            "NGC 206"  => "M 31",
            "NGC 221"  => "M 32",
            "NGC 224"  => "M 31",
            "NGC 588"  => "M 33", //Check
            "NGC 592"  => "M 33", //Check
            "NGC 595"  => "M 33", //Check
            "NGC 598"  => "M 33", //Check
            "NGC 603"  => "M 33", //Check
            "NGC 604"  => "M 33", //Check
            "NGC 1039" => "M 34",
            "NGC 2168" => "M 35",
            "NGC 1960" => "M 36",
            "NGC 2099" => "M 37",
            "NGC 1922" => "M 38",
            "NGC 7092" => "M 39",
            "NGC 2287" => "M 41",
            "NGC 1976" => "M 42",
            "NGC 1982" => "M 43",
            "NGC 2632" => "M 44",
            "NGC 1432" => "M 45", //Check
            "NGC 1435" => "M 45", //Check
            "NGC 2437" => "M 46", //Check
            "NGC 2438" => "M 46", //Check
            "NGC 2422" => "M 47",
            "NGC 2478" => "M 48",
            "NGC 4472" => "M 49",
            "NGC 2323" => "M 50",
            "NGC 5194" => "M 51",
            "NGC 7654" => "M 52",    
            "NGC 5024" => "M 53",
            "NGC 6715" => "M 54",            
            "NGC 6809" => "M 55",
            "NGC 6779" => "M 56",
            "NGC 6720" => "M 57",
            "NGC 4579" => "M 58",
            "NGC 4621" => "M 59",
            "NGC 4649" => "M 60",
            "NGC 4303" => "M 61",            
            "NGC 6266" => "M 62",
            "NGC 5055" => "M 63",
            "NGC 4826" => "M 64",
            "NGC 3623" => "M 65",
            "NGC 3627" => "M 66",
            "NGC 2682" => "M 67",
            "NGC 4590" => "M 68",
            "NGC 6637" => "M 69",
            "NGC 6681" => "M 70",
            "NGC 6838" => "M 71",
            "NGC 6981" => "M 72",
            "NGC 6994" => "M 73",
            "NGC 628"  => "M 74",
            "NGC 6864" => "M 75",
            "NGC 650"  => "M 76", //Check
            "NGC 651"  => "M 76", //Check
            "NGC 1068" => "M 77",
            "NGC 2068" => "M 78",
            "NGC 1904" => "M 79",
            "NGC 6093" => "M 80",
            "NGC 3031" => "M 81",
            "NGC 3034" => "M 82",
            "NGC 5236" => "M 83",
            "NGC 4374" => "M 84",
            "NGC 4382" => "M 85",
            "NGC 4406" => "M 86",    
            "NGC 4486" => "M 87",
            "NGC 4501" => "M 88",            
            "NGC 4552" => "M 89",
            "NGC 4569" => "M 90",
            "NGC 4548" => "M 91", //Check
            "NGC 4571" => "M 91", //Check   
            "NGC 6341" => "M 92",
            "NGC 2447" => "M 93",
            "NGC 4736" => "M 94",
            "NGC 3351" => "M 95",
            "NGC 3368" => "M 96",
            "NGC 3587" => "M 97",
            "NGC 4192" => "M 98",
            "NGC 4254" => "M 99",
            "NGC 4321" => "M 100",
            "NGC 5457" => "M 101",
            "NGC 5866" => "M 102",
            "NGC 581"  => "M 103",
            "NGC 4594" => "M 104",
            "NGC 3379" => "M 105",
            "NGC 4258" => "M 106",
            "NGC 6171" => "M 107",
            "NGC 3556" => "M 108",
            "NGC 3953" => "M 109", //Check
            "NGC 3992" => "M 109", //Check
            "NGC 205"  => "M 110"
        );
    }
}