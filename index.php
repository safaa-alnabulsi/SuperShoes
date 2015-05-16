<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require 'CSVHelper.php';
        require 'Database.php';
        //read the file contect and get the header
        $csvReader = new CSVHelper('files/TestCSV.csv');
        $fileContent = $csvReader->read();
        $fileHeader = $csvReader->getHeader();

        //Connect to Database and insert records
        $db = new Database('localhost', 'root', '', 'supershoes');
        $columns = '`name`, `gender`, `size`, `color`, `material`, `model_number`';
        $db->insert('product', $fileContent, $columns);
        //---------------------------------------------------------------------
        //Herren File
        $herrenShoes = $db->select('product', $columns, 'gender="Herren"');
        $csvWriter1 = new CSVHelper('files/HerrenShoes.csv');
        array_unshift($herrenShoes, $fileHeader);
        $csvWriter1->write($herrenShoes);
        //---------------------------------------------------------------------
        //Damen File
        $where = array('gender="Damen"');
        //$where = 'gender="Damen"';
        $damenShoes = $db->select('product', $columns, $where);
        if ($damenShoes) {
            $csvWriter2 = new CSVHelper('files/DamesnShoes.csv');
            array_unshift($damenShoes, $fileHeader);
            $csvWriter2->write($damenShoes);
        }
        //---------------------------------------------------------------------
        //Final CSV File
        $result = $db->delete('product', 'model_number=0');
        if ($result) {
            $allShoes = $db->select('product', $columns);
            $csvWriter3 = new CSVHelper('files/finallAllShoesInStore.csv');
            array_unshift($allShoes, $fileHeader);
            $csvWriter3->write($allShoes);
        }
        //---------------------------------------------------------------------
        //update 
//        $columns = array('gender' => 'Herren');
//        $where = 'gender="Damen"';
//        $db->update('product', $columns, $where);
        ?>
    </body>
</html>
