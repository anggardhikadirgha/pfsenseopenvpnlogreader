<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Path Bootstrap CSS -->
    <link href="include/css/bootstrap.min.css" rel="stylesheet">
       
    <!-- Path JS Bootstrap Bundle with Popper -->
    <script src="include/js/bootstrap.bundle.min.js"></script>

    <!-- Data Tables untuk paging dan searching -->

    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
    


    <title>OpenVPN Log reader</title>


<?php


// BAGIAN MEMBUKA FILE 


// LOOP UNTUK MEMBACA 10 FILE OPENVPNLOG
for ($i=0; $i<10; $i++){
	$filename [] = "include/input/openvpn".$i.".log";
	//echo $filename[$i]."<br>";
}

for ($x=0; $x<sizeof($filename); $x++) {

	echo '<div class="card bg-light mb-3" style="width:100%;">';
	echo '<div class="card-header">'."File Ke - ".$x.'</div>';
	echo '<div class="card-body">'; 
	
	  echo '<div class="table-responsive" style="overflow-x: hidden">';          
		echo '<table id="tabelip" class="table table-hover" style="width:100%;">';
		  echo '<thead>';
			echo '<tr>';
			  echo '<th> No. </th>';
			  echo '<th> NIM / Username </th>';
			  echo '<th> IP ASAL </th>';
			  echo '<th> IP MASKING</th>';
			  echo '<th> ALL IP TUJUAN </th>';
			echo '</tr>';
		  echo '</thead>';
		  echo '<tbody>';
	
	// CONVERT FILE KE ARRAY PER BARIS STRING DARI FILE 
	$baris = file($filename[$x], FILE_IGNORE_NEW_LINES);

	// MENGHAPUS DUPLIKAT DARI BARIS STRING ARRAY 1
	$array=array_values(array_unique($baris));

	// MENCARI BARIS BERISI POLA SPESIFIK 	
	// DAN MEMASUKKANNYA KE ARRAY 
	// !!!			POLA BUAT FILTER		 !!!

	//  POLA 1 BUAT NYARI INSTANCE NIM/IPASAL DAN IP MASKING
	$polaregex="/:\s+GET+\s+INST+\s+BY+\s+VIRT:+\s/";
	
	// MEMPERBAIKI POLA PERTAMA DIMANA ADA INSTANCE MENDAPATKAN IP MASKING YANG GAGAL 
	// POLA GAGAL : "GET INST BY VIRT : [failed] "
	$aneh=' [failed]';
	
		$data=array();
		$index=0;
		for ($i=0; $i<sizeof($array); $i++) {
	
			if	(preg_match($polaregex,$array[$i])==1) {
				if	(strpos($array[$i],$aneh)==false) {
				$data[$index++]=$array[$i];		
				}	 
			}	
			else {
			}	
		}   
	
	/*
	MEMECAH ARRAY 2 DAN MEMECAH LAGI AGAR MENDAPAT NIM, IP ASAL , DAN IP MASKING 
	MENGGUNAKAN FUNCTION EXPLODE DARI PHP "explode()" dan mengkonversi ke array multi dimensi
	
	*/
		for($j=0; $j<sizeof($data); $j++){
	
			//echo "[".$j."]" .$data[$j]."<br>";
	
			$pecahan=explode(' ',$data[$j]);
			
			$black=explode('/',$pecahan[12]);
	
			$array2[]=array(
			'nim' => $black[0],
			'ipasal' => $black[1],  
			'ipmask' => $pecahan[14]
			);		
		}
	
		// PROSES HAPUS DUPLIKAT DARI ARRAY MULTIDIMENSI MENGGUNAKAN FUNCTION
		$final = unique_multidim_array($array2,'ipmask');
		$finale = array_values($final);
		
		//print_r($finale);
		//echo sizeof($finale);
	
	
		//BACA ARRAY OUTPUT DARI FUNCTION YANG MEMBACA FILE FILTER OUTPUT DAN MELAKUKAN EXPLODE
			$arrTcp=read_file_output();
			$arrTcp_jlmh = count($arrTcp);
			for($i=0;$i<$arrTcp_jlmh;$i++)
			{
				$split=explode(" ",$arrTcp[$i]);
				$viaout[$i]=$split[0];
				$tujuan[$i]=$split[1];
				// echo $ipout[$i];
			}


			for ($i = 0; $i < sizeof($finale); $i++) {
              echo '<tr>';
				echo '<td>'.$i.'</td>';
				echo '<td>'.$finale[$i]['nim'].'</td>';
				echo '<td>'.$finale[$i]['ipasal'].'</td>';
				echo '<td>'.$finale[$i]['ipmask'].'</td>';
				echo '<td>';
					for ($j=0;$j<$arrTcp_jlmh;$j++) {
					  if ($finale[$i]['ipmask'] === $viaout[$j]) {
						  echo $tujuan[$j].'<br>';
					  }
			  		}
				echo '</td>';
			  echo '</tr>';
			}
	
	
		// MENCOCOKKAN DARI ARRAY UNTUK MENDAPAT IP TUJUAN	
}

              echo '</tbody>';
            echo '</table>';
          echo '</div>';

        echo '</div>';
      echo '</div>';








// ------ BAGIAN FUNCTION -------- // 
	// FUNCTION BUAT HAPUS DUPLIKAT 
	function unique_multidim_array($array, $key) {
		$temp_array = array();
		$i = 0;
		$key_array = array();
	   
		foreach($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}
	
	// FUNCTION UNTUK MEMBUKA FILE OUTPUT DAN MENGHILANGKAN DUPLIKAT 
	function read_file_output()
        {
            // Membuka file
			
			//LOOP UNTUK MEMBUKA 10 FILE FILTER 
			for ($i=0; $i<10; $i++){
				$fileout [] = "include/output/filter".$i.".log";
				//echo $filename[$i]."<br>";
			}
			
			for ($x=0; $x<sizeof($fileout); $x++) {
			
			
			
				// CONVERT FILE KE ARRAY PER BARIS STRING DARI FILE 
				$arrFile = file($fileout[$x], FILE_IGNORE_NEW_LINES);
			
   
            // var_dump($arrFile);
            $index = 0;
            $arrFile_jlmh = count($arrFile);
            // $arrVirt = array(1000);
            // for($i=0;$i<=count($arrFile);$i++)
            for($i=0;$i<$arrFile_jlmh;$i++)
            {
                if (strpos($arrFile[$i],"tcp") !== false && strpos($arrFile[$i],"210.57.216.4") === false) {
                    if($index === 0) {
                        $arrTcp[$index]=$arrFile[$i];
                        $arrTcp[$index]=substr($arrTcp[$index], strpos($arrTcp[$index], "10.0"));
                        $split=explode(",",$arrTcp[$index]);
                        $arrTcp[$index]=$split[0]." ".$split[1];
                        // echo $arrTcp[$index]."<br />";
                        $index++;
                    }
                    $arrTcp_temp=substr($arrFile[$i], strpos($arrFile[$i], "10.0"));
                    $split=explode(",",$arrTcp_temp);
                    $arrTcp_temp=$split[0]." ".$split[1];
                    $count = 0;

                    for ($j=0;$j<$index;$j++) {
                        if ($arrTcp_temp === $arrTcp[$j]) {
                            $count++;
                        }
                    }
                    if ($count === 0) {
                        $arrTcp[$index]=$arrTcp_temp;
                        // echo $arrTcp[$index]."<br />";
                        $index++;
                    }
                }
            }
		}	
            return $arrTcp;
        }

// ------ AKHIR BAGIAN FUNCTION -------- // 
?>

<script type="text/javascript" charset="utf-8">
   $(document).ready(function () {
    $('#tabelip').DataTable();
});
</script>

<script>
function openNav() {
  document.getElementById("mySidebar").style.width = "280px";
  document.getElementById("mySidebar").style.marginLeft = "0px";
  document.getElementById("mySidebar").style.paddingTop = "50px";
  document.getElementById("mySidebar").style.marginRight = "0px";
  document.getElementById("main").style.marginLeft = "280px";
  document.getElementById("main").style.paddingLeft = "1px";

}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft= "0";
}
</script>

