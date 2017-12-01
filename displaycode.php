<?php

namespace display;


class displaycode{
    public static function displayTable($records){
    	   $tableGen = '<table border="4">';
           $tableGen .= '<tr>';
        foreach($records[0] as $key=>$value){
            $tableGen .= '<th>' . htmlspecialchars($key) . '</th>';
        } 
            $tableGen .= '</tr>';
        foreach($records as $key=>$value){
            $tableGen .= '<tr>';
        foreach($value as $key2=>$value2)
                {
            $tableGen .= '<td>' . htmlspecialchars($value2) . '<br></td>';
                }
            $tableGen .= '</tr>';
        }
            $tableGen .= '</tbody></table>';
        return $tableGen;
    }     
    public static function displaytwo($records){
    	    $tableGen = '<table border="4">';
            $tableGen .= '<tr>';
        foreach($records as $key => $value){
            $tableGen .= '<th>' . htmlspecialchars($key) . '</th>';
        }
       	    $tableGen .= '</tr>';
        foreach($records as $value){
            $tableGen .= '<td>' . $value . '</td>';
        }
            $tableGen .= '</tr></table>';
        return $tableGen;
	}
}
?>
