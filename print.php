<?php 
  require ("tfpdf/tfpdf.php");
  require ("word.php");
  require "connect.php"; 

  //customer and invoice details
  $info=[
    "customer"=>"",
    "address"=>",",
    "city"=>"",
    "nip"=>"",
    "invoice_nr"=>"",
    "invoice_date"=>"",
    "total_amt"=>"",
    "words"=>"",
  ];
  
  //Select Invoice Details From Database
  $sql="select * from invoice where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  $row=$res->fetch_assoc();
	  
	  $obj=new IndianCurrency($row["GRAND_TOTAL"]);
	 
    
	  $info=[
		"customer"=>$row["CNAME"],
		"address"=>$row["CADDRESS"],
		"city"=>$row["CCITY"],
    "nip"=>$row["NIP"],
		"invoice_nr"=>$row["INVOICE_NR"],
		"invoice_date"=>date("d-m-Y",strtotime($row["INVOICE_DATE"])),
		"total_amt"=>$row["GRAND_TOTAL"],
		"words"=> $obj->NumberToWords($row["GRAND_TOTAL"]),

	  ];
  }
  
  //invoice Products
  $products_info=[];
  
  //Select Invoice Product Details From Database
  $sql="select * from invoice_products where SID='{$_GET["id"]}'";
  $res=$con->query($sql);
  if($res->num_rows>0){
	  while($row=$res->fetch_assoc()){
		   $products_info[]=[
			"name"=>$row["PNAME"],
			"price"=>$row["PRICE"],
			"qty"=>$row["QTY"],
      "vat"=>$row["VAT"]*100,
      "val_vat"=>$row["PRICE"]*$row["QTY"]*$row["VAT"],
			"total"=>$row["TOTAL"],
		   ];
	  }
  }
  
  
  //Create A4 Page with Portrait 
  $pdf=new tFPDF("P","mm","A4");
  $pdf->SetAutoPageBreak(false, 10);
  $pdf->AddPage();
  $pdf->AddFont('Arial','','Arial.ttf',true);
      $pdf->AddFont('ArialBold','','arialbd.ttf',true);
      $pdf->SetFont('ArialBold','',14);
      //Display Company Info
      //$pdf->SetFont('Arial','B',14,'ISO-8859-2');
      $pdf->Cell(50,10,"Firma Sp z.o.o.",0,1);
      $pdf->SetFont('ArialBold','',14);
      $pdf->Cell(50,7,"Ulica nr",0,1);
      $pdf->Cell(50,7,"XX-XX Miejscowość",0,1);
      $pdf->Cell(50,7,"NIP: XXXXXXXX",0,1);
      
      //Display INVOICE text
      $pdf->SetY(15);
      $pdf->SetX(-40);
      $pdf->SetFont('ArialBold','',18);
      //$pdf->Cell(50,10,"FAKTURA",0,0);
      $pdf->Cell(0,10,"FAKTURA",0,0,"R");
      //$pdf->Ln(50);
      $pdf->Image('logo512.png',173,25,20);
      
      //Display Horizontal line
      $pdf->Line(0,48,210,48);

      $pdf->SetY(50);
      $pdf->SetX(10);
      $pdf->SetFont('ArialBold','',12);
      $pdf->Cell(50,10,"Nabywca: ",0,1);
      $pdf->SetFont('Arial','',12);
      $pdf->Cell(50,7,$info["customer"],0,1);
      $pdf->Cell(50,7,"ul. ".$info["address"],0,1);
      $pdf->Cell(50,7,$info["city"],0,1);
      $pdf->Cell(50,7,"NIP:".$info["nip"],0,1);
      
      //Display Invoice nr
      $pdf->SetY(55);
      $pdf->SetX(-60);
      $pdf->Cell(50,7,"Numer faktury: ".$info["invoice_nr"]);
      
      //Display Invoice date
      $pdf->SetY(63);
      $pdf->SetX(-60);
      $pdf->Cell(50,7,"Data wystawienia: ".$info["invoice_date"]);
      
      //Display Table headings
      $pdf->SetY(95);
      $pdf->SetX(10);
      $pdf->SetFont('ArialBold','',12);
      $pdf->Cell(50,9,"Nazwa towaru",1,0);
      $pdf->Cell(28,9,"Cena netto",1,0,"C");
      $pdf->Cell(24,9,"Ilość",1,0,"C");
      $pdf->Cell(24,9,"VAT",1,0,"C");
      $pdf->Cell(32,9,"Wartość VAT",1,0,"C");      
      $pdf->Cell(32,9,"Wartość brutto",1,1,"C");
      $pdf->SetFont('Arial','',12);
      
      //Display table product rows
      foreach($products_info as $row){
        $pdf->Cell(50,9,$row["name"],"LR",0);
        $pdf->Cell(28,9,$row["price"],"R",0,"R");
        $pdf->Cell(24,9,$row["qty"],"R",0,"C");
        $pdf->Cell(24,9,$row["vat"]."%","R",0,"C");
        $pdf->Cell(32,9,$row["val_vat"],"R",0,"C");
        $pdf->Cell(32,9,$row["total"],"R",1,"R");
      }
      //Display table empty rows
      for($i=0;$i<12-count($products_info);$i++)
      {
        $pdf->Cell(50,9,"","LR",0);
        $pdf->Cell(28,9,"","R",0,"R");
        $pdf->Cell(24,9,"","R",0,"C");
        $pdf->Cell(24,9,"","R",0,"C");
        $pdf->Cell(32,9,"","R",0,"C");
        $pdf->Cell(32,9,"","R",1,"R");
      }
      //Display table total row
      $pdf->SetFont('ArialBold','',12);
      $pdf->Cell(158,9,"Razem",1,0,"R");
      $pdf->Cell(32,9,$info["total_amt"],1,1,"R");

      $parts = explode(".",$info["total_amt"]);
      //Display amount in words
      $pdf->SetY(225);
      $pdf->SetX(10);
      $pdf->SetFont('ArialBold','',12);
      $pdf->Cell(0,9,"RAZEM SLOWNIE ",0,1);
      $pdf->SetFont('Arial','',12);
      $pdf->Cell(0, 9, $info["words"] ." ". $parts[1] . "/100 PLN", 0, 1);


 

















       //set footer position
       $pdf->SetY(-40);
       $pdf->SetFont('ArialBold','',12);
       $pdf->Cell(0,10,"................................................",0,0,"L");
       $pdf->Cell(0,10,"................................................",0,0,"R");
       $pdf->Ln(5);
       $pdf->SetFont('Arial','',10);
       $pdf->Cell(0,10,"Osoba uprawniona do odbioru",0,0,"L");
       $pdf->Cell(0,10,"Osoba uprawniona do wystawienia",0,0,"R");
      
       $pdf->SetFont('Arial','',8);
       

  //$pdf->body($info,$products_info);
       //Display Footer Text
       $pdf->SetY(-20);
       // $pdf->Cell(0,10,"Faktura wygenerowana automatycznie &#169",0,1,"C");
        $pdf->Cell(0,10,'©Konrad Czarnowski',0,1,'C',0);
  $pdf->Output();


  //$pdf->Output('F', 'C:\xampp\htdocs\invoice\faktura.pdf'.$info["invoice_nr"]);

 // $fullname = "faktura-".$info["invoice_nr"]."-".$info["invoice_date"];

 // $pdf_file_name = $fullname.".pdf";

//$pdf->Output($pdf_file_name,'F');


// zdefiniuj nazwę i ścieżkę do pliku wynikowego

$fullname = "faktura_nr_".$info["invoice_nr"]."_z_dnia_".$info["invoice_date"];
$filename = $fullname.".pdf";
$filepath = 'E:\xampp\htdocs\pwa_invoice\faktury\\' . $filename;

// zapisz plik
$pdf->Output('F', $filepath);



  ?>