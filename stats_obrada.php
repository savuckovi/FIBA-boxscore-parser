

<textarea rows="30" cols="100" name="tekst">
	<?php 
		$url = $_POST["link"];
		//dobijem - http://www.fibalivestats.com/matches/18/08/85/68/69ctqSKrBvzQ/

		$dom = new DOMDocument();
		if ($dom != '' && @$dom->loadHTMLFile($url)) {
			
			$finder = new DomXPath($dom);
			$klasa="boxS";
			$table = $finder->query("//table[contains(@class, '$klasa')]");
			
			$klasa="scoreA";
			$prvi = $finder->query("//div[contains(@class, '$klasa')]");
			
			foreach ($prvi as $p)
			{
				$per1 = $p->firstChild->nextSibling->nodeValue;
				$per2 = $p->firstChild->nextSibling->nextSibling->nextSibling->nodeValue;
				$per3 = $p->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;
				
				$r = $p->firstChild;
				while (true)
				{
					$r = $r->nextSibling;

					if ($r->nodeType == XML_ELEMENT_NODE)
					{
						if ($r->getAttribute("class") != null && $r->getAttribute("class") == "final")
						{
							$rezA = $r->nodeValue;
							break;
						}
					}
					
				}
				//echo "per1: " . $per1;
				//echo "per2: " . $per2;
				//echo "per3: " . $per3;
			}
			
			$klasa="scoreB";
			$drugi = $finder->query("//div[contains(@class, '$klasa')]");
			
			foreach ($drugi as $p)
			{
				if ($p->firstChild->nextSibling->getAttribute("class") != "period") continue;
				$per4 = $p->firstChild->nextSibling->nodeValue;
				$per5 = $p->firstChild->nextSibling->nextSibling->nextSibling->nodeValue;
				$per6 = $p->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;
				
				$r = $p->firstChild;

				while (true)
				{
					$r = $r->nextSibling;
					if ($r->nodeType == XML_ELEMENT_NODE)
					{
						if ($r->getAttribute("class") == "final")
						{
							$rezB = $r->nodeValue;
							break;
						}
					}
				}
				
				//echo "per4: " . $per4;
				//echo "per5: " . $per5;
				//echo "per6: " . $per6;
				
				break;
			}
			
			//echo "(" . $per1 . ":" . $per4 . ", " . ($per1+$per2) . ":" . ($per4+$per5) . ", " . ($per1+$per2+$per3) . ":" . ($per4+$per5+$per6) . ") <br>";
			
			$br = 0;
			$prva_ekipa = array();
			$druga_ekipa = array();
			$ek1 = "";
			$ek2 = "";
			
			foreach ($table as $t)
			{		
				if ($t->getAttribute("class") == "boxS")
				{
					$br++;
					$tmp = array();
					$node = $t->previousSibling->previousSibling;
					
					//echo "<br><br> ekipa " . $node->nodeValue;
					
					if (strlen($ek1)<2)
						$ek1 = $node->nodeValue;
					else $ek2 = $node->nodeValue;
					
					$prvi = $t->firstChild->nextSibling->nextSibling->firstChild->nextSibling->nextSibling;
					
					$ime = $prvi->nodeValue;
					
					$kos = $prvi->parentNode->lastChild->previousSibling;
					
					//echo "Ime: " . $ime . " kos: " . $kos->nodeValue;
					
					if (strlen($kos->nodeValue)<7 && $kos->nodeValue != "0")
					{
						$tmp[trim($ime,"*")] = $kos->nodeValue;
					}
					
					/*$drugi = $prvi->parentNode->nextSibling->firstChild->nextSibling->nextSibling;
					
					echo "drugi: " . $drugi->nodeValue;*/
					
					$dalje = $prvi->parentNode;
					
					while ($dalje->nextSibling)
					{
						$dalje = $dalje->nextSibling;
						$drugi = $dalje->firstChild->nextSibling->nextSibling;
						
						if (strlen($drugi->nodeValue)>3)
						{
							//echo "<br>dalje: " . trim($drugi->nodeValue,"*");
							
							$kos = $drugi->parentNode->lastChild->previousSibling;
						
							//echo " kos: " . $kos->nodeValue;
							
							if (strlen($kos->nodeValue)<7 && $kos->nodeValue != "0")
							{
								//echo "ime: " . trim($drugi->nodeValue,"*") . " kos: " . $kos->nodeValue . "<br>";
								$tmp[trim($drugi->nodeValue,"*")] = $kos->nodeValue;
							}
						}
						
					}
					
					if ($br == 1 )
						$prva_ekipa = $tmp;
					else
						$druga_ekipa = $tmp;
						
				}
			}
			
			
		
		}
		arsort($prva_ekipa);
		//print_r($prva_ekipa);
		//echo "<br>";
		
		$kriva_slova = array("Spain", "Croatia", "Bosnia And Herzegovina", "Montenegro", "Serbia", "Lithuania", 
							"Dominican Republic", "Jamaica", "Mexico", "Canada", "Puerto Rico", "Uruguay"); 
		$prava_slova = array("Španjolska", "Hrvatska", "Bosna i Hercegovina", "Crna Gora", "Srbija", "Litva", 
							"Dominikanska Republika", "Jamajka", "Meksiko", "Kanada", "Portoriko", "Urugvaj"); 
		
		
		$ekipa1 = substr($ek1, 2);
		$ekipa2 = substr($ek2, 2);
		
		$tim1 = ucwords(strtolower($ekipa1));
		$tim2 =  ucwords(strtolower($ekipa2));
		
		$tim1 = str_replace($kriva_slova, $prava_slova, $tim1);
		$tim2 = str_replace($kriva_slova, $prava_slova, $tim2);
		
		//<h3 class="rezultat">Ekipa - Ekipa 100:95 <span>(četvrtine)</span></h3>
		
		echo "<hr /> \n \n<h3 class=\"rezultat\">" . $tim1 . " - " . $tim2 . " ". $rezA . ":" . $rezB . " <span>(" . $per1 . ":" . $per4 . ", " . ($per1+$per2) . ":" . ($per4+$per5) . ", " . ($per1+$per2+$per3) . ":" . ($per4+$per5+$per6) . ")</span></h3>\n\n";
		
		$kriva_slova = array("Bogdanovic", "Zoric", "Ukic ", "Saric", "Rudez", "Tomic", "Mccalebb ", "Gechevski", "Samardziski", "Sehovic", 
						"Vucevic", "Dasic", "Dubljevic", "Popovic", "Ivanovic", "Sekulic", "Nedovic", "Krstic", "Micic","Markovic","Kalinic", 
						"Djordje","Gagic","Andjusic","Katic","Stimac","Masic","Sutalo","Bavcic","Gordic","Kikanovic","Teletovic","Dedovic", 
						"Stipanovic"); 
		
		$prava_slova = array("Bogdanović", "Žorić", "Ukić", "Šarić", "Rudež", "Tomić", "McCalebb ", "Gečevski", "Samardžiski", "Šehović", 
						"Vučević", "Dašić", "Dubljević", "Popović", "Ivanović", "Sekulić", "Nedović", "Krstić", "Micić", "Marković", "Kalinić", 
						"Đorđe", "Gagić", "Anđušić", "Katić","Štimac","Mašić","Šutalo","Bavčić","Gordić","Kikanović","Teletović","Đedović", 
						"Stipanović"); 
		
		$ispis1 = "<strong>" . $tim1 . "</strong>: ";
		foreach ($prva_ekipa as $k=>$v){
			$exp = explode(".",$k);
			// $str = substr($str, 1);
			$trim = substr($exp[1], 2);
			
			$ispis1 .= ucwords(strtolower($trim)) . " " . $v . ", ";
		}
		$ispis1 = str_replace($kriva_slova, $prava_slova, $ispis1);
		echo substr($ispis1,0,-2);
		echo "\n\n";
		arsort($druga_ekipa);
		//print_r($druga_ekipa);
		
		
		$ispis2 = "<strong>" . $tim2 . "</strong>: ";
		foreach ($druga_ekipa as $k=>$v){
			$exp = explode(".",$k);
			// $str = substr($str, 1);
			$trim = substr($exp[1], 2);
			
			$ispis2 .= ucwords(strtolower($trim)) . " " . $v . ", ";
		}
		$ispis2 = str_replace($kriva_slova, $prava_slova, $ispis2);
		echo substr($ispis2,0,-2);
		//echo $url;
		
		echo "\n\n";
	?>
</textarea>


<a href="index.php">Back</a>
